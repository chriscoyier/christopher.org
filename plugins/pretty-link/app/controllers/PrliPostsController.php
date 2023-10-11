<?php
if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); }

class PrliPostsController extends PrliBaseController {
  public $opt_fields;

  public function load_hooks() {
    add_action('init', array($this, 'add_tinymce_buttons'));
    add_action('add_meta_boxes', array($this, 'add_meta_box'));
    add_action('wp_ajax_prli_tinymce_form', array($this, 'display_tinymce_form'));
    add_action('wp_ajax_prli_tinymce_validate_slug', array($this, 'validate_tinymce_slug'));
    add_action('wp_ajax_prli_create_pretty_link', array($this, 'create_pretty_link'));
    add_action('wp_ajax_prli_search_for_links', array($this, 'search_results'));
  }

  // registers the buttons for use
  public function register_buttons($buttons) {
    array_push($buttons, "prli_tinymce_form");
    return $buttons;
  }

  // add the button to the tinyMCE bar
  public function add_tinymce_plugin($plugin_array) {
    $plugin_array['PrliTinyMCE'] = PRLI_JS_URL.'/tinymce_form.js';
    return $plugin_array;
  }

  // filters the tinyMCE buttons and adds our custom buttons
  public function add_tinymce_buttons() {

    // If this isn't a Pretty Link authorized user then bail
    if(!PrliUtils::is_authorized()) { return; }

    // Add only in Rich Editor mode
    if(get_user_option('rich_editing') == 'true') {
      // filter the tinyMCE buttons and add our own
      add_filter("mce_external_plugins", array($this, "add_tinymce_plugin"));
      add_filter('mce_buttons', array($this, 'register_buttons'));
    }
  }

  /**
   * Adds a "Pretty Links" metabox to various post screens.
   *
   * @access public
   * @return void
   */
  public function add_meta_box() {
    global $plp_update;

    if($plp_update->is_installed()) {
      global $plp_options;

      $post_types = $plp_options->get_post_types();
    } else {
      $post_types = array('post', 'page');
    }

    add_meta_box('pretty-links-sidebar', esc_html__('Pretty Links', 'pretty-link'), array($this, 'render_meta_box'), $post_types, 'side');
  }

  /**
   * Renders the content for the "Pretty Links" metabox.
   *
   * @access public
   * @return void
   */
  public function render_meta_box() {
    global $plp_update;

    if(!$plp_update->is_installed()) {
      ?>
      <p><?php esc_html_e('To get access to these advanced features, upgrade to Pretty Links Pro.', 'pretty-link'); ?></p>
      <a href="https://prettylinks.com/pl/pro-feature-indicator/upgrade?pretty-link-metabox" target="_blank" class="pretty-link-cta-button"><?php esc_html_e('Upgrade to Pretty Links Pro now!', 'pretty-link'); ?></a>
      <?php
    }

    do_action('prli_sidebar_meta_box');
  }

  //AJAX
  public function display_tinymce_form() {
    global $prli_link, $prli_options, $plp_update;

    //Setup some vars for the view
    $home_url = home_url() . '/';
    $random_slug      = $prli_link->generateValidSlug();
    $default_redirect = $prli_options->link_redirect_type;
    $default_nofollow = ($prli_options->link_nofollow)?'enabled':'disabled';
    $default_sponsored= ($prli_options->link_sponsored)?'enabled':'disabled';
    $default_tracking = ($prli_options->link_track_me)?'enabled':'disabled';

    //Get alternate Base URL
    if($plp_update->is_installed()) {
      global $plp_options;

      if(isset($plp_options) && $plp_options->use_prettylink_url && !empty($plp_options->prettylink_url)) {
        $home_url = stripslashes($plp_options->prettylink_url) . '/';
      }
    }

    wp_register_style('prli-ui-smoothness', PRLI_VENDOR_LIB_URL.'/jquery-ui/jquery-ui.min.css', array(), '1.11.4');
    wp_register_style('prli-tinymce-popup-form', PRLI_CSS_URL . '/tinymce_form_popup.css', array('prli-ui-smoothness'), PRLI_VERSION);

    $css = sprintf('.ui-autocomplete-loading {
      background: white url(%s) right center no-repeat;
    }
    .ui-autocomplete {
      max-height: 200px;
      overflow-y: auto;
      overflow-x: hidden;
      width: 510px !important;
    }', esc_url(admin_url('images/wpspin_light.gif')));

    wp_add_inline_style('prli-tinymce-popup-form', $css);

    wp_register_script(
      'prli-tinymce-popup-form',
      PRLI_JS_URL . '/tinymce_form_popup.js',
      array(
        'jquery',
        'jquery-ui-core',
        'jquery-ui-widget',
        'jquery-ui-position',
        'jquery-ui-menu',
        'jquery-ui-autocomplete',
        'jquery-ui-accordion'
      ),
      PRLI_VERSION,
      true
    );

    wp_localize_script('prli-tinymce-popup-form', 'prliTinymceL10n', array(
      'prli_selected_text' => '',
      'home_url' => $home_url,
      'default_redirect' => $default_redirect,
      'default_nofollow' => $default_nofollow,
      'default_sponsored' => $default_sponsored,
      'default_tracking' => $default_tracking,
      'ajaxurl' => admin_url('admin-ajax.php')
    ));

    require(PRLI_VIEWS_PATH.'/shared/tinymce_form_popup.php');
    die();
  }

  //AJAX
  public function validate_tinymce_slug() {
    if(!isset($_POST['slug']) || empty($_POST['slug'])) {
      echo "false";
      die();
    }

    $slug = sanitize_text_field(stripslashes($_POST['slug']));

    //Can't end in a slash
    if(substr($slug, -1) == '/' || $slug[0] == '/' || preg_match('/\s/', $slug) || is_wp_error(PrliUtils::is_slug_available($slug))) {
      echo "false";
      die();
    }

    echo "true";
    die();
  }

  //AJAX
  public function create_pretty_link() {
    $valid_vars = array('target', 'slug', 'redirect', 'nofollow', 'sponsored', 'tracking');

    if(!PrliUtils::is_authorized()) {
      echo "invalid_user";
      die();
    }

    if(!isset($_POST) || !($valid_vars == array_intersect($valid_vars, array_keys($_POST)))) {
      echo "invalid_inputs";
      die();
    }

    //Using the local API Yo
    $id = prli_create_pretty_link(
            esc_url_raw(trim(stripslashes($_POST['target']))),
            sanitize_text_field(stripslashes($_POST['slug'])),
            '', //Name
            '', //Desc
            0, //Group ID (Deprecated)
            (int)($_POST['tracking'] == 'enabled'),
            (int)($_POST['nofollow'] == 'enabled'),
            (int)($_POST['sponsored'] == 'sponsored'),
            sanitize_key(stripslashes($_POST['redirect']))
          );

    if((int)$id > 0) {
      echo "true";
      die();
    }

    echo "link_failed_to_create";
    die();
  }

  //AJAX
  public function search_results() {
    global $prli_link, $wpdb;

    if(!isset($_GET['term']) || empty($_GET['term'])) { die(''); }

    $return = array();
    $term = '%' . $wpdb->esc_like(sanitize_text_field(stripslashes($_GET['term']))) . '%';
    $q = "SELECT * FROM {$prli_link->table_name} WHERE link_status='enabled' AND (slug LIKE %s OR name LIKE %s OR url LIKE %s) LIMIT 20";
    $q = $wpdb->prepare($q, $term, $term, $term);
    $results = $wpdb->get_results($q, ARRAY_A);

    //Prepare the results for JSON
    if(!empty($results)) {
      foreach($results as $result) {
        $result = stripslashes_deep($result);

        if(extension_loaded('mbstring')) {
          $alt_name = (mb_strlen($result['name']) > 55)?mb_substr($result['name'], 0, 55).'...':$result['name'];
        }
        else {
          $alt_name = (strlen($result['name']) > 55)?substr($result['name'], 0, 55).'...':$result['name'];
        }

        $pretty_link = prli_get_pretty_link_url($result['id']);

        $return[] = array(
          'id'         => $result['id'],
          'pretty_url' => (empty($pretty_link) ? home_url() : $pretty_link),
          'value'      => (empty($result['name']))?$result['slug']:$alt_name,
          'slug'       => $result['slug'],
          'target'     => $result['url'],
          'title'      => $result['name'], //Not used currently, but we may want this at some point
          'nofollow'   => (int)$result['nofollow'],
          'sponsored'  => (int)$result['sponsored']
        );
      }

      die(json_encode($return));
    }

    die();
  }
} //End class
