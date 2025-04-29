<?php if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); }

class PrliLinksController extends PrliBaseController {
  public function load_hooks() {
    add_action( 'init', array($this, 'register_post_type'), 0);
    add_filter( 'cron_schedules', array($this,'intervals') );
    add_action( 'prli_cleanup_visitor_locks_worker', array($this,'cleanup_visitor_locks') );
    add_action( 'admin_init', array($this,'maybe_cleanup_visitor_locks') );
    add_action( 'pre_get_posts', array($this, 'set_custom_post_types_admin_order') );
    add_action( 'save_post', array($this, 'save_cpt_link') );
    add_action( 'deleted_post', array($this, 'delete_cpt_link') );
    add_action( 'transition_post_status', array($this, 'transition_cpt_status'), 10, 3 );
    add_action( 'transition_post_status', array($this, 'transition_cpt_status'), 10, 3 );
    add_filter( 'redirect_post_location', array($this, 'redirect_post_location'), 10, 2 );
    add_action( 'admin_notices', array($this, 'link_saved_admin_notice') );
    add_action( 'admin_notices', array($this, 'fee_admin_notice') );
    add_action( 'wp_ajax_validate_pretty_link', array($this,'ajax_validate_pretty_link') );
    add_action( 'wp_ajax_reset_pretty_link', array($this,'ajax_reset_pretty_link') );
    add_action( 'wp_ajax_prli_quick_create', array($this, 'ajax_quick_create'));

    // Add slug and URL to search
    add_filter( 'posts_search', array($this, 'search_links_table'), 10, 2 );

    // Links table join
    add_filter( 'posts_where', array($this, 'prettypay_links_filter'), 10, 2 );
    add_filter( 'posts_fields', array($this, 'add_clicks_to_select'), 10, 2 );
    add_filter( 'posts_join', array($this,'join_links_to_posts'), 10, 2 );

    // Legacy Groups Filter
    add_action( 'restrict_manage_posts', array($this,'filter_links_by_legacy_groups') );
    add_filter( 'posts_where', array($this,'where_links_belong_to_legacy_group'), 10, 2 );

    // Alter Quick Links Menu (subsubsub)
    add_filter( 'views_edit-'.PrliLink::$cpt, array($this,'modify_quick_links') );

    // Sort links by custom columns
    add_action( 'posts_orderby', array($this, 'custom_link_sort_orderby'), 10, 2 );

    add_action('manage_'.PrliLink::$cpt.'_posts_custom_column', array($this,'custom_columns'), 10, 2);
    add_filter('manage_edit-'.PrliLink::$cpt.'_columns', array($this,'columns'));
    add_filter('manage_edit-'.PrliLink::$cpt.'_sortable_columns', array($this,'sortable_columns'));
    add_filter('default_hidden_columns', array($this, 'default_hidden_columns'), 10, 2);
    add_action('quick_edit_custom_box', array($this, 'quick_bulk_edit_add'), 10, 2);
    add_action('bulk_edit_custom_box', array($this, 'quick_bulk_edit_add'), 10, 2);
    add_action('save_post', array($this, 'save_quick_edit'), 10, 2);
    add_action('wp_ajax_prli_links_list_save_bulk_edit', array($this, 'save_bulk_edit'));
    add_filter('admin_body_class', array($this, 'add_post_status_body_class'));
    add_filter('post_row_actions', array($this, 'add_row_actions'), 10, 2);

    if(!($snapshot_timestamp = wp_next_scheduled('prli_cleanup_visitor_locks_worker'))) {
      wp_schedule_event( time(), 'prli_cleanup_visitor_locks_interval', 'prli_cleanup_visitor_locks_worker' );
    }

    // PrettyPay links
    add_filter('admin_url', array($this, 'add_new_prettypay_link_url'), 10, 2);
    add_filter('submenu_file', array($this, 'highlight_prettypay_menu_item'));
    add_action('current_screen', array($this, 'maybe_change_post_type_labels'));
    // 3rd Party Compatibility
    add_filter( 'acf/input/meta_box_priority', array( $this, 'update_acf_meta_box_priority' ), 10 );
  }

  public function register_post_type() {
    $role = PrliUtils::get_minimum_role();

    $args = array(
      'labels' => array(
        'name'               => esc_html__('Pretty Links', 'pretty-link'),
        'singular_name'      => esc_html__('Pretty Link', 'pretty-link'),
        'add_new'            => esc_html__('Add New', 'pretty-link'),
        'add_new_item'       => esc_html__('Add New Pretty Link', 'pretty-link'),
        'edit_item'          => esc_html__('Edit Pretty Link', 'pretty-link'),
        'new_item'           => esc_html__('New Pretty Link', 'pretty-link'),
        'view_item'          => esc_html__('View Pretty Link', 'pretty-link'),
        'search_items'       => esc_html__('Search Pretty Links', 'pretty-link'),
        'not_found'          => esc_html__('No Pretty Links found', 'pretty-link'),
        'not_found_in_trash' => esc_html__('No Pretty Links found in Trash', 'pretty-link'),
        'parent_item_colon'  => esc_html__('Parent Pretty Link:', 'pretty-link')
      ),
      'public' => false,
      'menu_position' => 55.5532265,
      'show_ui' => true,
      'show_in_admin_bar' => true,
      'exclude_from_search' => true,
      'can_export' => false,
      'capabilities' => array(
        'edit_post'              => $role,
        'read_post'              => $role,
        'delete_post'            => $role,
        'create_posts'           => $role,
        'edit_posts'             => $role,
        'edit_others_posts'      => $role,
        'publish_posts'          => $role,
        'read_private_posts'     => $role,
        'read'                   => 'read',
        'delete_posts'           => $role,
        'delete_private_posts'   => $role,
        'delete_published_posts' => $role,
        'delete_others_posts'    => $role,
        'edit_private_posts'     => $role,
        'edit_published_posts'   => $role
      ),
      'hierarchical' => false,
      'register_meta_box_cb' => array($this, 'add_meta_boxes'),
      'rewrite' => false,
      'supports' => array('title')
    );

    $plp_update = new PrliUpdateController();

    if($plp_update->is_installed()) {
      $args['taxonomies'] = array(
        PlpLinkCategoriesController::$ctax,
        PlpLinkTagsController::$ctax,
      );
    }

    register_post_type( PrliLink::$cpt, $args );
  }

  /** Ensures that the CPT Links will list properly by post_date DESC */
  public function set_custom_post_types_admin_order($wp_query) {
    if( is_admin() && isset($wp_query->query['post_type']) ) {
      // Get the post type from the query
      $post_type = $wp_query->query['post_type'];

      if( $post_type == PrliLink::$cpt ) {
        $wp_query->set( 'orderby', 'post_date' );
        $wp_query->set( 'order', 'DESC' );
      }
    }
  }

  public function add_meta_boxes() {
    global $post_id, $prli_link, $plp_update;

    add_meta_box(
      'pretty-link-settings',
      esc_html__('Pretty Link Settings', 'pretty-link'),
      array($this, 'link_meta_box'), PrliLink::$cpt,
      'normal', 'high'
    );

    if ( ! $plp_update->is_installed() ) {
      add_meta_box(
        'pretty-link-advanced-features',
        esc_html__('Advanced Features', 'pretty-link'),
        array($this, 'advanced_featured_meta_box'), PrliLink::$cpt,
        'side', 'default'
      );
    }

    remove_meta_box('slugdiv', PrliLink::$cpt, 'normal');
  }

  public function link_meta_box($post, $args) {
    global $prli_link, $pagenow;

    if($pagenow==='post-new.php') {
      $values = $this->setup_new_vars();
    }
    else {
      $id = $prli_link->get_link_from_cpt($post->ID);
      $link = $prli_link->getOne($id);
      $values = $this->setup_edit_vars($link);
    }
    require PRLI_VIEWS_PATH . '/links/form.php';
  }

  public function advanced_featured_meta_box($post, $args) {
    ?>
    <ul class="upgrade-features">
      <li><?php esc_html_e( 'Advanced Redirect Types', 'pretty-link' ); ?></li>
      <li><?php esc_html_e( 'Auto Create Pretty Links', 'pretty-link' ); ?></li>
      <li><?php esc_html_e( 'Auto-Link Keywords', 'pretty-link' ); ?></li>
      <li><?php esc_html_e( 'Support & Updates', 'pretty-link' ); ?></li>
      <li><?php esc_html_e( 'Link Categories', 'pretty-link' ); ?></li>
      <li><?php esc_html_e( 'Link Tags', 'pretty-link' ); ?></li>
      <li><?php esc_html_e( 'Plus Much More!', 'pretty-link' ); ?></li>
    </ul>
    <a href="https://prettylinks.com/pl/pro-feature-indicator/upgrade?advanced-features" class="pretty-link-cta-button"><?php esc_html_e( 'Tell Me More', 'pretty-link' ); ?></a>
    <?php
  }

  public function setup_new_vars() {
    global $prli_link, $prli_options;

    $values = array();
    $values['url'] =  (isset($_REQUEST['url'])?esc_url_raw(trim(stripslashes($_REQUEST['url']))):'');
    $values['slug'] = (isset($_REQUEST['slug'])?sanitize_text_field(stripslashes($_REQUEST['slug'])):$prli_link->generateValidSlug());
    $values['name'] = (isset($_REQUEST['name'])?sanitize_text_field(stripslashes($_REQUEST['name'])):'');
    $values['description'] = (isset($_REQUEST['description'])?sanitize_textarea_field(stripslashes($_REQUEST['description'])):'');
    $values['prettypay_link'] = !empty($_REQUEST['prettypay']);

    $values['track_me'] = ((isset($_REQUEST['track_me']) and $_REQUEST['track_me'] == 'on') or (!isset($_REQUEST['track_me']) and $prli_options->link_track_me == '1'));
    $values['nofollow'] = ((isset($_REQUEST['nofollow']) and $_REQUEST['nofollow'] == 'on') or (!isset($_REQUEST['nofollow']) and $prli_options->link_nofollow == '1'));
    $values['sponsored'] = ((isset($_REQUEST['sponsored']) and $_REQUEST['sponsored'] == 'on') or (!isset($_REQUEST['sponsored']) and $prli_options->link_sponsored == '1'));

    $values['redirect_type'] = array();
    $values['redirect_type']['307'] = (((isset($_REQUEST['redirect_type']) and $_REQUEST['redirect_type'] == '307') or (!isset($_REQUEST['redirect_type']) and $prli_options->link_redirect_type == '307'))?' selected="selected"':'');
    $values['redirect_type']['302'] = (((isset($_REQUEST['redirect_type']) and $_REQUEST['redirect_type'] == '302') or (!isset($_REQUEST['redirect_type']) and $prli_options->link_redirect_type == '302'))?' selected="selected"':'');
    $values['redirect_type']['301'] = (((isset($_REQUEST['redirect_type']) and $_REQUEST['redirect_type'] == '301') or (!isset($_REQUEST['redirect_type']) and $prli_options->link_redirect_type == '301'))?' selected="selected"':'');
    $values['redirect_type']['prettybar'] = (((isset($_REQUEST['redirect_type']) and $_REQUEST['redirect_type'] == 'prettybar') or (!isset($_REQUEST['redirect_type']) and $prli_options->link_redirect_type == 'prettybar'))?' selected="selected"':'');
    $values['redirect_type']['cloak'] = (((isset($_REQUEST['redirect_type']) and $_REQUEST['redirect_type'] == 'cloak') or (!isset($_REQUEST['redirect_type']) and $prli_options->link_redirect_type == 'cloak'))?' selected="selected"':'');
    $values['redirect_type']['pixel'] = (((isset($_REQUEST['redirect_type']) and $_REQUEST['redirect_type'] == 'pixel') or (!isset($_REQUEST['redirect_type']) and $prli_options->link_redirect_type == 'pixel'))?' selected="selected"':'');
    $values['redirect_type']['metarefresh'] = (((isset($_REQUEST['redirect_type']) and $_REQUEST['redirect_type'] == 'metarefresh') or (!isset($_REQUEST['redirect_type']) and $prli_options->link_redirect_type == 'metarefresh'))?' selected="selected"':'');
    $values['redirect_type']['javascript'] = (((isset($_REQUEST['redirect_type']) and $_REQUEST['redirect_type'] == 'javascript') or (!isset($_REQUEST['redirect_type']) and $prli_options->link_redirect_type == 'javascript'))?' selected="selected"':'');

    $values['groups'] = array();

    $values['param_forwarding'] = isset($_REQUEST['param_forwarding']);
    $values['delay'] = (isset($_REQUEST['delay']) ? (int) $_REQUEST['delay'] : 0);

    if(isset($_REQUEST['google_tracking'])) {
      $values['google_tracking'] = true;
    }
    else {
      global $plp_update;
      if( $plp_update->is_installed() ) {
        global $plp_options;
        $values['google_tracking'] = $plp_options->google_tracking?true:false;
      }
      else {
        $values['google_tracking'] = false;
      }
    }

    return apply_filters('prli_setup_new_vars', $values);
  }

  public function setup_edit_vars($record) {
    global $prli_link_meta;

    $values = array();
    $values['link_id'] = isset($record->id) ? $record->id : null;
    $values['url'] =  ((isset($_REQUEST['url']) and $record == null)?esc_url_raw(trim(stripslashes($_REQUEST['url']))):stripslashes($record->url));
    $values['slug'] = ((isset($_REQUEST['slug']) and $record == null)?sanitize_text_field(stripslashes($_REQUEST['slug'])):stripslashes($record->slug));
    $values['name'] = ((isset($_REQUEST['name']) and $record == null)?sanitize_text_field(stripslashes($_REQUEST['name'])):stripslashes($record->name));
    $values['description'] = ((isset($_REQUEST['description']) and $record == null)?sanitize_textarea_field(stripslashes($_REQUEST['description'])):stripslashes($record->description));
    $values['track_me'] = ((isset($_REQUEST['track_me']) or $record->track_me) and ((isset($_REQUEST['track_me']) and $_REQUEST['track_me'] == 'on') or $record->track_me == 1));
    $values['nofollow'] = ((isset($_REQUEST['nofollow']) and $_REQUEST['nofollow'] == 'on') or (isset($record->nofollow) && $record->nofollow == 1));
    $values['sponsored'] = ((isset($_REQUEST['sponsored']) and $_REQUEST['sponsored'] == 'on') or (isset($record->sponsored) && $record->sponsored == 1));
    $values['prettypay_link'] = $record->prettypay_link == 1;

    $values['groups'] = array();

    $values['param_forwarding'] = (isset($_REQUEST['param_forwarding']) || !(empty($record->param_forwarding) || $record->param_forwarding=='off'));

    $values['redirect_type'] = array();
    $values['redirect_type']['307'] = ((!isset($_REQUEST['redirect_type']) or (isset($_REQUEST['redirect_type']) and $_REQUEST['redirect_type'] == '307') or (isset($record->redirect_type) and $record->redirect_type == '307'))?' selected="selected"':'');
    $values['redirect_type']['302'] = (((isset($_REQUEST['redirect_type']) and $_REQUEST['redirect_type'] == '302') or (isset($record->redirect_type) and $record->redirect_type == '302'))?' selected="selected"':'');
    $values['redirect_type']['301'] = (((isset($_REQUEST['redirect_type']) and $_REQUEST['redirect_type'] == '301') or (isset($record->redirect_type) and $record->redirect_type == '301'))?' selected="selected"':'');
    $values['redirect_type']['prettybar'] = (((isset($_REQUEST['redirect_type']) and $_REQUEST['redirect_type'] == 'prettybar') or (isset($record->redirect_type) and $record->redirect_type == 'prettybar'))?' selected="selected"':'');
    $values['redirect_type']['cloak'] = (((isset($_REQUEST['redirect_type']) and $_REQUEST['redirect_type'] == 'cloak') or (isset($record->redirect_type) and $record->redirect_type == 'cloak'))?' selected="selected"':'');
    $values['redirect_type']['pixel'] = (((isset($_REQUEST['redirect_type']) and $_REQUEST['redirect_type'] == 'pixel') or (isset($record->redirect_type) and $record->redirect_type == 'pixel'))?' selected="selected"':'');
    $values['redirect_type']['metarefresh'] = (((isset($_REQUEST['redirect_type']) and $_REQUEST['redirect_type'] == 'metarefresh') or (isset($record->redirect_type) and $record->redirect_type == 'metarefresh'))?' selected="selected"':'');
    $values['redirect_type']['javascript'] = (((isset($_REQUEST['redirect_type']) and $_REQUEST['redirect_type'] == 'javascript') or (isset($record->redirect_type) and $record->redirect_type == 'javascript'))?' selected="selected"':'');

    if(isset($_REQUEST['delay'])) {
      $values['delay'] = (int) $_REQUEST['delay'];
    }
    else {
      $values['delay'] = $prli_link_meta->get_link_meta($record->id, 'delay', true);
    }

    if(isset($_REQUEST['google_tracking'])) {
      $values['google_tracking'] = true;
    }
    else {
      $values['google_tracking'] = (($prli_link_meta->get_link_meta($record->id, 'google_tracking', true) == 1)?true:false);
    }

    return apply_filters('prli_setup_edit_vars', $values, $record);
  }

  public static function save_cpt_link() {
    global $post, $post_id, $typenow, $prli_link, $prli_link_meta;

    # Skip ajax
    if(defined('DOING_AJAX')) {
      return $post_id;
    }

    # Skip non-post requests & non-admin requests
    if(!PrliUtils::is_post_request() || !PrliUtils::is_authorized()) {
      return $post_id;
    }

    # Please only run this code when we're dealing with a Link CPT
    if($typenow !== PrliLink::$cpt) {
      return $post_id;
    }

    # Verify nonce
    if(!wp_verify_nonce(isset($_POST[PrliLink::$nonce_str]) ? $_POST[PrliLink::$nonce_str] : '', PrliLink::$nonce_str . wp_salt())) {
      return $post_id;
    }

    $link_id = isset($_POST['link_id']) ? (int) $_POST['link_id'] : 0;

    $_POST['name'] = $_POST['post_title'];
    $_POST['url'] = isset($_POST['prli_url']) && is_string($_POST['prli_url']) ? $_POST['prli_url'] : '';
    $_POST['description'] = isset($_POST['prli_description']) && is_string($_POST['prli_description']) ? $_POST['prli_description'] : '';
    $_POST['link_cpt_id'] = $post->ID;

    if($link_id) {
      do_action('prli_before_update_link', $link_id);
      $link_id = $prli_link->update( $link_id, $_POST );
    }
    else {
      $link_id = $prli_link->create( $_POST );
    }

    do_action('prli_update_link', $link_id);
  }

  public function transition_cpt_status($new_status, $old_status, $post) {
    global $prli_link;

    if($new_status != $old_status) {
      $link_id = $prli_link->get_link_from_cpt($post->ID);

      if(empty($link_id)) { return; }

      // No need to enable the link again going from draft to published.
      if($new_status == 'publish' && $old_status == 'draft') { return; }

      if($new_status == 'publish' || $new_status == 'draft') {
        $prli_link->enable_link($link_id);
      }
      else {
        $prli_link->disable_link($link_id);
      }
    }
  }

  /**
   * Redirect to the links list after saving a link
   *
   * @param  string $location
   * @param  int    $post_id
   * @return string
   */
  public function redirect_post_location($location, $post_id) {
    if(get_post_type($post_id) == PrliLink::$cpt) {
      $args = array(
        'post_type' => PrliLink::$cpt,
        'message' => stripos($location, 'message=6') === false ? 1 : 6
      );

      if(!empty($_REQUEST['prettypay_link'])) {
        $args['prettypay'] = 1;
      }

      $location = add_query_arg($args, admin_url('edit.php'));
    }

    return $location;
  }

  /**
   * Add a message that the link has been created or updated after redirecting to the links list
   */
  public function link_saved_admin_notice() {
    $screen = get_current_screen();

    if($screen instanceof WP_Screen && $screen->id == 'edit-pretty-link' && isset($_GET['message'])) {
      $message = (int) $_GET['message'];

      if($message == 1) {
        $message = !empty($_REQUEST['prettypay']) ? __('PrettyPay™ Link updated.', 'pretty-link') : __('Pretty Link updated.', 'pretty-link');
        printf('<div class="notice notice-success is-dismissible"><p>%s</p></div>', esc_html($message));
      }
      elseif($message == 6) {
        $message = !empty($_REQUEST['prettypay']) ? __('PrettyPay™ Link created.', 'pretty-link') : __('Pretty Link created.', 'pretty-link');
        printf('<div class="notice notice-success is-dismissible"><p>%s</p></div>', esc_html($message));
      }
    }
  }

  public function fee_admin_notice() {
    global $plp_update;

    if(isset($_GET['prettypay']) && $_GET['prettypay'] == '1') {
      $license = $plp_update->get_license_info();
      $license = is_array($license) && isset($license['license_key']) && is_array($license['license_key']);

      $is_dismissed = get_transient('prli_dismiss_notice_fee_3');

      if(!$is_dismissed && !$license && PrliStripeConnect::is_active()) {
        include_once PRLI_VIEWS_PATH . "/prettypay/fee_notice.php";
      }
    }
  }

  public static function delete_cpt_link($post_id) {
    global $prli_link;

    $link_id = $prli_link->get_link_from_cpt($post_id);

    if(empty($link_id)) { return; }

    // CPT is already deleted by now so don't try again
    return $prli_link->destroy($link_id, 'dont_delete_cpt');
  }

  public function maybe_cleanup_visitor_locks() {
    $cleanup = get_transient('prli_cleanup_visitor_locks');

    if(empty($cleanup)) {
      set_transient('prli_cleanup_visitor_locks', 1, DAY_IN_SECONDS);
      $this->cleanup_visitor_locks();
    }
  }

  /** Delete visitor locks so we don't explode the size of peoples' databases */
  public function cleanup_visitor_locks() {
    global $wpdb;

    //|   1127004 | _transient_timeout_prli_visitor_58b12712690d5           | 1488004892    | no       |
    //|   1127005 | _transient_prli_visitor_58b12712690d5                   | 58b12712690d5 | no       |

    $q = $wpdb->prepare("
        SELECT option_name
          FROM {$wpdb->options}
         WHERE option_name LIKE %s
           AND option_value < %s
         ORDER BY option_value
      ",
      '_transient_timeout_prli_visitor_%',
      time()
    );

    $timeouts = $wpdb->get_col($q);

    foreach($timeouts as $i => $timeout_key) {
      // figure out the transient_key from the timeout_key
      $transient_key = preg_replace(
        '/^_transient_timeout_prli_visitor_/',
        '_transient_prli_visitor_',
        $timeout_key
      );

      $tq = $wpdb->prepare("
          DELETE FROM {$wpdb->options}
           WHERE option_name IN (%s,%s)
        ",
        $timeout_key,
        $transient_key
      );

      $res = $wpdb->query($tq);
    }
  }

  public function intervals( $schedules ) {
    $schedules['prli_cleanup_visitor_locks_interval'] = array(
      'interval' => HOUR_IN_SECONDS,
      'display' => esc_html__('Pretty Link Cleanup Visitor Locks', 'pretty-link'),
    );

    return $schedules;
  }

  public function ajax_validate_pretty_link() {
    global $prli_link;

    check_ajax_referer('validate_pretty_link','security');

    if(!PrliUtils::is_post_request()) {
      PrliUtils::exit_with_status(403,esc_html__('Forbidden', 'pretty-link'));
    }

    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    $_POST['url'] = isset($_POST['prli_url']) && is_string($_POST['prli_url']) ? $_POST['prli_url'] : '';
    $errors = $prli_link->validate($_POST, $id);

    $errors = apply_filters('prli_validate_link', $errors);

    $message = esc_html__('Success!', 'pretty-link');
    if(!empty($errors)) {
      $message = '<div>' . esc_html__('Fix the following errors:', 'pretty-link') . '</div><ul>';
      foreach($errors as $error) {
        $message .= "<li>{$error}</li>";
      }
      $message .= '</ul>';
    }

    $response = array(
      'valid' => empty($errors),
      'message' => $message
    );

    PrliUtils::exit_with_status(200,json_encode($response));
  }

  public function columns($columns) {
    if(isset($_REQUEST['post_status']) && $_REQUEST['post_status'] == 'trash') {
      return $columns;
    }

    global $plp_update;

    $categories_label  = esc_html__('Categories', 'pretty-link');
    $tags_label        = esc_html__('Tags', 'pretty-link');
    $keywords_label    = esc_html__('Keywords', 'pretty-link');

    if ($plp_update->is_installed()) {
      $category_key = 'taxonomy-pretty-link-category';
      $tag_key = 'taxonomy-pretty-link-tag';
    } else {
      $category_key = 'pro-pretty-link-category';
      $tag_key = 'pro-pretty-link-tag';
      $categories_label = $categories_label . ' ' .
        sprintf( '<span class="prli-pro-only-indicator" title="%1$s"><a href="%2$s">%3$s</a></span>',
          __('Upgrade to a Pretty Links premium plan to get Link Categories', 'pretty-link'),
          admin_url( 'edit.php?post_type=pretty-link&page=pretty-link-upgrade-categories' ),
          __('Pro', 'pretty-link')
      );
      $tags_label = $tags_label . ' ' .
        sprintf( '<span class="prli-pro-only-indicator" title="%1$s"><a href="%2$s">%3$s</a></span>',
          __('Upgrade to a Pretty Links premium plan to get Link Tags', 'pretty-link'),
          admin_url( 'edit.php?post_type=pretty-link&page=pretty-link-upgrade-tags' ),
          __('Pro', 'pretty-link')
      );
      $keywords_label = $keywords_label . ' ' .
        sprintf( '<span class="prli-pro-only-indicator" title="%1$s"><a href="%2$s">%3$s</a></span>',
          __('Upgrade to a Pretty Links premium plan to get Keyword Replacements', 'pretty-link'),
          admin_url( 'edit.php?post_type=pretty-link&page=pretty-link-updates#prli_upgrade' ),
          __('Pro', 'pretty-link')
        );
    }

    $columns = array(
      'cb' => '<input type="checkbox" />',
      'settings' => esc_html__('Settings', 'pretty-link'),
      'title' => esc_html__('Link Title', 'pretty-link'),
      //'slug' => esc_html__('Slug', 'pretty-link'),
      'target' => esc_html__('Target', 'pretty-link'),
      $category_key => $categories_label,
      $tag_key => $tags_label,
      'keywords' => $keywords_label,
      'clicks' => esc_html__('Clicks', 'pretty-link'),
      'date' => esc_html__('Date', 'pretty-link'),
      'links' => esc_html__('Pretty Links', 'pretty-link')
    );

    return apply_filters('prli_admin_links_columns', $columns);
  }

  public function sortable_columns($columns) {
    if(isset($_REQUEST['post_status']) && $_REQUEST['post_status'] == 'trash') {
      return $columns;
    }

    $columns['title'] = 'title';
    //$columns['slug'] = 'slug';
    $columns['target'] = 'target';
    $columns['clicks'] = array('clicks', true); // desc first
    $columns['date'] = 'date';

    return $columns;
  }

  public function custom_columns($column, $post_id) {
    global $prli_link, $prli_blogurl;

    $link_id = $prli_link->get_link_from_cpt($post_id);
    $link = $prli_link->getOne($link_id, OBJECT, true);

    // This will happen if the link is trashed
    if(empty($link)) { return $column; }

    $struct = PrliUtils::get_permalink_pre_slug_uri();
    $pretty_link_url = "{$prli_blogurl}{$struct}{$link->slug}";

    if(!empty($link)) {
      if('settings' == $column) {
        PrliLinksHelper::link_list_icons($link);
      }
      elseif('keywords' == $column) {
        $pro_only = apply_filters(
          'prli_link_column_keywords',
          '—',
          $link_id
        );
        echo $pro_only;
      }
      elseif('pro-pretty-link-category' == $column) {
        $pro_only = apply_filters(
          'prli_link_column_categories',
          '—',
          $link_id
        );
        echo $pro_only;
      }
      elseif('pro-pretty-link-tag' == $column) {
        $pro_only = apply_filters(
          'prli_link_column_tags',
          '—',
          $link_id
        );
        echo $pro_only;
      }
      elseif('clicks' == $column) {
        PrliLinksHelper::link_list_clicks($link);
      }
      elseif('links' == $column) {
        PrliLinksHelper::link_list_url_clipboard($link);
      }
      // elseif('slug' == $column) {
        // echo esc_html(stripslashes($link->slug));
      // }
      elseif('target' == $column) {
        printf(
          '<a href="%s" target="_blank">%s</a>',
          esc_url($link->url),
          esc_url($link->url)
        );
      }

      do_action('prli_admin_links_column_values', $column, $link);
    }
  }

  /**
   * Get the columns that are hidden by default for the links table
   *
   * @param   array      $hidden
   * @param   WP_Screen  $screen
   * @return  array
   */
  public function default_hidden_columns($hidden, $screen) {
    if ($screen && $screen->id == 'edit-pretty-link') {
      $hidden[] = 'target';
    }

    return $hidden;
  }

  public function quick_bulk_edit_add($column, $post_type) {
    if($column != 'settings' || $post_type != PrliLink::$cpt) { return; }

    ?>
      <fieldset class="inline-edit-col-right inline-edit-prli-links-<?php echo "{$column}"; ?>">
        <div class="inline-edit-group">
          <label>
            <span class="title"><?php echo esc_html(__('No Follow', 'pretty-link')); ?></span>
            <select name="prli_quick_edit_nofollow">
              <option value="no-change"> - <?php echo esc_html(__('No Change', 'pretty-link')); ?> - </option>
              <option value="on"><?php echo esc_html(__('Enabled', 'pretty-link')); ?></option>
              <option value="off"><?php echo esc_html(__('Disabled', 'pretty-link')); ?></option>
            </select>
            <br/>
            <span class="title"><?php echo esc_html(__('Sponsored', 'pretty-link')); ?></span>
            <select name="prli_quick_edit_sponsored">
              <option value="no-change"> - <?php echo esc_html(__('No Change', 'pretty-link')); ?> - </option>
              <option value="on"><?php echo esc_html(__('Enabled', 'pretty-link')); ?></option>
              <option value="off"><?php echo esc_html(__('Disabled', 'pretty-link')); ?></option>
            </select>
            <br/>
            <span class="title"><?php echo esc_html(__('Tracking', 'pretty-link')); ?></span>
            <select name="prli_quick_edit_tracking">
              <option value="no-change"> - <?php echo esc_html(__('No Change', 'pretty-link')); ?> - </option>
              <option value="on"><?php echo esc_html(__('Enabled', 'pretty-link')); ?></option>
              <option value="off"><?php echo esc_html(__('Disabled', 'pretty-link')); ?></option>
            </select>
          </label>
        </div>
      </fieldset>
      <fieldset class="inline-edit-col-right">
        <!-- here to capture the tags field in bulk edit. WP uses JS to append tag field to last fieldset. -->
        <span id="prli_bulk_edit_spinner"><img src="<?php echo admin_url('images/wpspin_light.gif'); ?>" /></span>
      </fieldset>
    <?php
  }

  public function save_quick_edit($post_id, $post) {
    global $prli_link;

    if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) { return $post_id; }
    if(isset($post->post_type) && $post->post_type == 'revision') { return $post_id; }
    if(!isset($_POST['action']) || $_POST['action'] != 'inline-save') { return $post_id; } // This action is set when doing Quick Edit save

    if($post->post_type == PrliLink::$cpt) {
      $id = $prli_link->get_link_from_cpt($post->ID);
      $link = $prli_link->getOne($id);

      $tracking = ($_POST['prli_quick_edit_tracking'] == 'no-change') ? '' : ( ($_POST['prli_quick_edit_tracking'] == 'on') ? true : false );
      $nofollow = ($_POST['prli_quick_edit_nofollow'] == 'no-change') ? '' : ( ($_POST['prli_quick_edit_nofollow'] == 'on') ? true : false );
      $sponsored = ($_POST['prli_quick_edit_sponsored'] == 'no-change') ? '' : ( ($_POST['prli_quick_edit_sponsored'] == 'on') ? true : false );

      prli_update_pretty_link(
        $link->id,
        $link->url,
        $link->slug,
        sanitize_text_field(stripslashes($_POST['post_title'])),
        $link->description,
        null,// group_id deprecated
        $tracking,
        $nofollow,
        $sponsored,
        $link->redirect_type,
        $link->param_forwarding,
        '' // param_struct deprecated
      );
    }
  }

  public function save_bulk_edit() {
    if( ! check_ajax_referer( 'prli_admin_link_list_nonce', false, false ) || ! PrliUtils::is_authorized() ) {
      wp_send_json_error( esc_html__('Security check failed.', 'pretty-link'), 403 );
    }

    global $prli_link;

    $post_ids = (isset($_POST['post_ids']) && !empty($_POST['post_ids'])) ? $_POST['post_ids'] : array();

    if(!empty($post_ids) && is_array($post_ids)) {
      foreach($post_ids as $post_id) {
        $post_type = get_post_type($post_id);

        if($post_type != PrliLink::$cpt) { return; }

        $tracking = ($_POST['tracking'] == 'no-change') ? '' : ( ($_POST['tracking'] == 'on') ? true : false );
        $nofollow = ($_POST['nofollow'] == 'no-change') ? '' : ( ($_POST['nofollow'] == 'on') ? true : false );
        $sponsored = ($_POST['sponsored'] == 'no-change') ? '' : ( ($_POST['sponsored'] == 'on') ? true : false );

        if($tracking === '' && $nofollow === '' && $sponsored === '') { return; } // Nothing to change

        $id = $prli_link->get_link_from_cpt($post_id);
        $link = $prli_link->getOne($id);

        prli_update_pretty_link(
          $link->id,
          $link->url,
          $link->slug,
          $link->name,
          $link->description,
          null,// group_id deprecated
          $tracking,
          $nofollow,
          $sponsored,
          $link->redirect_type,
          $link->param_forwarding,
          '' // param_struct deprecated
        );
      }
    }
  }

  /**
  * Append row actions to list page
  * @see add_filter('post_row_actions')
  * @param array $actions Current row actions
  * @param WP_Post current Post
  * @return array filtered row actions
  */
  public function add_row_actions($actions, $post) {
    global $prli_link;

    if($post->post_type === PrliLink::$cpt) {
      $id = $prli_link->get_link_from_cpt($post->ID);
      $link = $prli_link->getOne($id);

      if(empty($link)) { return $actions; }

      $new_actions = array();
      $new_actions['edit']  = $actions['edit'];
      $new_actions['trash'] = $actions['trash'];
      $new_actions['inline hide-if-no-js'] = $actions['inline hide-if-no-js'];
      $new_actions['reset'] = PrliLinksHelper::link_action_reset($link, __('Reset', 'pretty-link'));

      if( $link->redirect_type !== 'pixel' ) {
        $new_actions['tweet'] = PrliLinksHelper::link_action_tweet($link, __('Tweet', 'pretty-link'));
        $new_actions['email'] = PrliLinksHelper::link_action_email($link, __('Email', 'pretty-link'));

        if($link->redirect_type !== 'prettypay_link_stripe') {
          $new_actions['url'] = PrliLinksHelper::link_action_visit_target($link, __('Target &raquo;', 'pretty-link'));
        }

        $new_actions['pl'] = PrliLinksHelper::link_action_visit_pretty_link($link, $link->prettypay_link ? __('PrettyPay™ Link &raquo;', 'pretty-link') : __('Pretty Link &raquo;', 'pretty-link'));
      }

      $plp_update = new PrliUpdateController();

      if($plp_update->is_installed()) {
        global $plp_options, $prli_link_meta;

        $plp_links_ctrl = new PlpLinksController();

        if ($plp_options->generate_qr_codes) {
          $new_actions['qr'] = $plp_links_ctrl->qr_code_link($link->id);
        }

        $dynamic_redirection = $prli_link_meta->get_link_meta($link->id, 'prli_dynamic_redirection', true);

        if ($dynamic_redirection == 'rotate') {
          $enable_split_test = $prli_link_meta->get_link_meta($link->id, 'prli-enable-split-test', true);

          if ($enable_split_test) {
            $new_actions['report'] = sprintf(
              '<a href="%s" title="%s">%s</a>',
              esc_url(admin_url('admin.php?page=plp-reports&action=display-split-test-report&id=') . $link->id),
              esc_attr__('View Split Test Report', 'pretty-link'),
              esc_html__('View Split Test Report', 'pretty-link')
            );
          }
        }

        if($plp_options->enable_link_health && !in_array($link->redirect_type, array('pixel', 'prettypay_link_stripe'), true)) {
          $new_actions['link_health'] = $plp_links_ctrl->health_status_link($link->id);
        }
      }

      $actions = $new_actions;
    }

    return $actions;
  }

  public function add_post_status_body_class($body_class) {
    $current_screen = get_current_screen();

    if($current_screen instanceof WP_Screen && $current_screen->post_type == PrliLink::$cpt && $current_screen->id == 'edit-pretty-link') {
      $post_status = isset($_GET['post_status']) ? esc_attr(sanitize_text_field(wp_unslash($_GET['post_status']))) : '';

      if($post_status) {
        $body_class .= " prli-post-status-$post_status ";
      }
    }

    return $body_class;
  }

  public function ajax_reset_pretty_link() {
    global $prli_link;

    check_ajax_referer('reset_pretty_link','security');

    if(!PrliUtils::is_post_request()) {
      PrliUtils::exit_with_status(403,esc_html__('Forbidden', 'pretty-link'));
    }

    $prli_link->reset( $_POST['id'] );

    $response = array(
      'message' => esc_html__("Your Pretty Link was Successfully Reset", 'pretty-link')
    );

    PrliUtils::exit_with_status(200,json_encode($response));
  }

  public function ajax_quick_create() {
    if (!PrliUtils::is_post_request() || !isset($_POST['url'], $_POST['slug']) || !is_string($_POST['url']) || !is_string($_POST['slug'])) {
      wp_send_json_error(array('message' => __('Bad request', 'pretty-link')));
    }

    if (!PrliUtils::is_authorized()) {
      wp_send_json_error(array('message' => __('Insufficient permissions', 'pretty-link')));
    }

    if (!check_ajax_referer('prli_quick_create', false, false)) {
      wp_send_json_error(array('message' => __('Security check failed', 'pretty-link')));
    }

    global $prli_link, $prli_options;

    $errors = $prli_link->validate($_POST);

    if (count($errors)) {
      wp_send_json_error(array('message' => $errors[0]));
    }

    $_POST['redirect_type'] = $prli_options->link_redirect_type;

    if ($prli_options->link_track_me) {
      $_POST['track_me'] = 'on';
    }

    if ($prli_options->link_nofollow) {
      $_POST['nofollow'] = 'on';
    }

    if ($prli_options->link_sponsored) {
      $_POST['sponsored'] = 'on';
    }

    $link_id = $prli_link->create($_POST);
    $link = $prli_link->getOne($link_id);

    if (!$link) {
      wp_send_json_error(array('message' => __('An error occurred creating the link', 'pretty-link')));
    }

    $location = add_query_arg(array(
      'post_type' => PrliLink::$cpt,
      'message' => 6
    ), admin_url('edit.php'));

    wp_send_json_success([
      'redirect' => esc_url_raw($location)
    ]);
  }

  /**
   * Filter groups if the user is running the free version of Pretty Links
   *
   * @since 1.1.0
   * @return void
   */
  public function filter_links_by_legacy_groups() {
    global $typenow, $wp_query, $prli_link, $plp_update;

    if( $typenow == PrliLink::$cpt && !$plp_update->is_installed() ) {
      $groups = $prli_link->get_all_legacy_groups();

      if(empty($groups)) { return; }

      $current_group = 'all';
      if( isset( $_GET['group'] ) && is_numeric( $_GET['group'] ) ) {
        $current_group = (int) $_GET['group']; // Check if option has been selected
      }

      ?>
      <select name="group" id="group">
        <option
          value="all"
          <?php selected( 'all', $current_group ); ?>>
            <?php esc_html_e( 'All Groups (Legacy)', 'pretty-link' ); ?>
        </option>
        <?php foreach( $groups as $group ): ?>
          <option
            value="<?php echo esc_attr( $group->id ); ?>"
            <?php selected( $group->id, $current_group ); ?>>
              <?php echo esc_html( stripslashes($group->name) ); ?>
          </option>
        <?php endforeach; ?>
      </select>
      <?php
    }
  }

  /**
   * Add the link click stats to the SELECT part of the post query
   *
   * @param  string $fields
   * @param WP_Query $query Wordpress Query.
   * @return string
   */
  public function add_clicks_to_select($fields, $query) {
    global $typenow, $prli_click, $prli_options, $prli_link_meta;
    $is_pl_query = PrliLinksHelper::is_pretty_link_query($query);
    if ( ! $is_pl_query ) {
      return $fields;
    }
    if( $typenow == PrliLink::$cpt ) {
      if($prli_options->extended_tracking != 'count') {
        $op = $prli_click->get_exclude_where_clause( ' AND' );

        $fields .= ",
            (
              SELECT COUNT(*)
                FROM {$prli_click->table_name} AS cl
               WHERE cl.link_id = li.id
               {$op}
            ) as clicks
          ";
      }
      else {
        $fields .= ",
            (
              SELECT lm.meta_value
                FROM {$prli_link_meta->table_name} AS lm
               WHERE lm.meta_key=\"static-clicks\"
                 AND lm.link_id=li.id LIMIT 1
            ) as clicks
          ";
      }
    }

    return $fields;
  }
  /**
   * PrettyPay links Filters.
   *
   * @param string $where The current WHERE clause of the query.
   * @param WP_Query $query The current WP_Query object.
   * @return string The modified WHERE clause.
   */
  public function prettypay_links_filter($where, $query) {
    global $typenow;
    $is_pl_query = PrliLinksHelper::is_pretty_link_query($query);
    if ( ! $is_pl_query ) {
      return $where;
    }
    if($typenow == PrliLink::$cpt && is_admin()) {
      if(!empty($_REQUEST['prettypay'])) {
        $where .= " AND li.prettypay_link = 1";
      }
      else {
        $where .= " AND li.prettypay_link <> 1";
      }
    }

    return $where;
  }
  /**
   * Join for searching.
   *
   * @param string $join The current join clause.
   * @param WP_Query $query The current WP_Query object.
   * @return string The modified join clause.
   */
  public function join_links_to_posts($join, $query) {
    global $wpdb, $typenow;
    $is_pl_query = PrliLinksHelper::is_pretty_link_query($query);
    if ( ! $is_pl_query ) {
      return $join;
    }
    if( $typenow == PrliLink::$cpt ) {
      $join .= "JOIN {$wpdb->prefix}prli_links AS li ON {$wpdb->posts}.ID = li.link_cpt_id ";
    }

    return $join;
  }

  public function search_links_table($where, $query) {
    global $wp_query, $wpdb, $typenow;
    $is_pl_query = PrliLinksHelper::is_pretty_link_query($query);
    if ( ! $is_pl_query ) {
      return $where;
    }
    if( $typenow == PrliLink::$cpt && ! empty( $wp_query->query_vars['s'] ) ){
      $search_terms = explode( ' ', $wp_query->query_vars['s'] );
      $search_clauses = array();

      foreach( $search_terms as $search_term ){
        $search_term      = '%' . $wpdb->esc_like( $search_term ) . '%';
        $search_clauses[] = " ({$wpdb->posts}.post_title LIKE '$search_term' OR {$wpdb->posts}.post_excerpt LIKE '$search_term' OR {$wpdb->posts}.post_content LIKE '$search_term' ) ";
      }
      $where             .= ' AND ' . implode( ' AND ', $search_clauses );
      $exact_search_terms = '%' . $wpdb->esc_like( $wp_query->query_vars['s'] ) . '%';
      $where             .= " OR li.url LIKE '$exact_search_terms' OR li.slug LIKE '$exact_search_terms'";
    }
    return $where;
  }

  // Where clause for searching link groups
  public function where_links_belong_to_legacy_group( $where, $query ) {
    global $wp_query, $wpdb, $typenow;
    $is_pl_query = PrliLinksHelper::is_pretty_link_query($query);
    if ( ! $is_pl_query ) {
      return $where;
    }
    if( $typenow == PrliLink::$cpt &&
        isset($_GET['group']) &&
        is_numeric($_GET['group']) &&
        empty($wp_query->query_vars['s']) ) {
        // possible because we've already joined the links to posts
        $where .= $wpdb->prepare(" AND li.group_id=%d", (int) $_GET['group']);
    }

    return $where;
  }

  // Override the views since we need separate counts for PrettyPay and non-PrettyPay links
  public function modify_quick_links($views) {
    global $prli_link, $plp_update;

    $num_links = $prli_link->count_links((int) !empty($_REQUEST['prettypay']));
    $total_links = array_sum((array) $num_links);

    // Subtract post types that are not included in the admin all list.
    foreach(get_post_stati(array('show_in_admin_all_list' => false)) as $state) {
      $total_links -= $num_links->$state;
    }

    $all_label = sprintf(
      /* translators: %s: number of links */
      esc_html__('All %s', 'pretty-link'),
      sprintf(
        '<span class="count">(%s)</span>',
        number_format_i18n($total_links)
      )
    );

    $trash_label = sprintf(
      /* translators: %s: number of links in the trash */
      esc_html__('Trash %s', 'pretty-link'),
      sprintf(
        '<span class="count">(%s)</span>',
        number_format_i18n($num_links->trash)
      )
    );

    $all_args = array('post_type' => PrliLink::$cpt);
    $trash_args = array('post_type' => PrliLink::$cpt, 'post_status' => 'trash');

    if(!empty($_REQUEST['prettypay'])) {
      $all_args['prettypay'] = 1;
      $trash_args['prettypay'] = 1;
    }

    $views = array(
      'all' => sprintf(
        '<a href="%s"%s>%s</a>',
        esc_url(add_query_arg($all_args, 'edit.php')),
        !isset($_REQUEST['post_status']) || $_REQUEST['post_status'] != 'trash' ? ' class="current" aria-current="page"' : '',
        $all_label
      )
    );

    if(!empty($num_links->trash)) {
      $views['trash' ] = sprintf(
        '<a href="%s"%s>%s</a>',
        esc_url(add_query_arg($trash_args, 'edit.php')),
        isset($_REQUEST['post_status']) && $_REQUEST['post_status'] == 'trash' ? ' class="current" aria-current="page"' : '',
        $trash_label
      );
    }

    if(!PrliUtils::is_authorized()) {
      return $views;
    }

    if(!$plp_update->is_installed()) {
      $views['prli_broken_links'] = sprintf(
          '<a href="#" id="prli-broken-links"><span class="tooltip">%s</span></a>',
          esc_html__('Find Broken Links', 'pretty-link')
      );
    }

    return apply_filters('prli_quick_links', $views);
  }

  // Add custom sort orderbys
  public function custom_link_sort_orderby($orderby, $query) {
    global $wp_query, $wpdb, $typenow;
    $is_pl_query = PrliLinksHelper::is_pretty_link_query($query);
    if ( ! $is_pl_query ) {
      return $orderby;
    }
    if( $typenow == PrliLink::$cpt &&
        isset($_GET['orderby']) && isset($_GET['order']) ) {

      $order = strtoupper($_GET['order'])=='ASC' ? 'ASC' : 'DESC';

      if($_GET['orderby']=='slug') {
        $orderby = "
          li.slug {$order}
        ";
      }
      elseif($_GET['orderby']=='date') {
        $orderby = "
          li.created_at {$order}
        ";
      }
      elseif($_GET['orderby']=='title') {
        $orderby = "
          li.name {$order}
        ";
      }
      elseif($_GET['orderby']=='target') {
        $orderby = "
          li.url {$order}
        ";
      }
      elseif($_GET['orderby']=='clicks') {
        $orderby = "
          CAST(clicks AS unsigned) {$order}
        ";
      }
    }

    return $orderby;
  }

  public function add_new_prettypay_link_url($url, $path) {
    if($path == 'post-new.php?post_type=pretty-link') {
      if(!empty($_REQUEST['prettypay'])) {
        $url = add_query_arg(array('prettypay' => 1), $url);
      }
      else {
        global $prli_link;
        $post_id = isset($_GET['post']) ? (int) $_GET['post'] : 0;

        if($post_id > 0 && $prli_link->is_post_prettypay_link($post_id)) {
          $url = add_query_arg(array('prettypay' => 1), $url);
        }
      }
    }

    return $url;
  }

  public function highlight_prettypay_menu_item($submenu_file) {
    if(!empty($_REQUEST['prettypay'])) {
      $submenu_file = 'edit.php?post_type=pretty-link&amp;prettypay=1';
    }
    else {
      global $typenow, $pagenow, $prli_link;

      if($typenow == PrliLink::$cpt && $pagenow == 'post.php') {
        $post_id = isset($_GET['post']) ? (int) $_GET['post'] : 0;

        if($post_id > 0 && $prli_link->is_post_prettypay_link($post_id)) {
          $submenu_file = 'edit.php?post_type=pretty-link&amp;prettypay=1';
        }
      }
    }

    return $submenu_file;
  }

  public function maybe_change_post_type_labels($current_screen) {
    if($current_screen->post_type == PrliLink::$cpt) {
      global $wp_post_types, $prli_link;

      if(isset($wp_post_types[PrliLink::$cpt]) && $wp_post_types[PrliLink::$cpt] instanceof WP_Post_Type) {
        $post_type_object = $wp_post_types[PrliLink::$cpt];

        if(isset($post_type_object->labels) && is_object($post_type_object->labels)) {
          $is_prettypay = false;

          if(!empty($_REQUEST['prettypay'])) {
            $is_prettypay = true;
          }
          elseif(isset($_GET['post'], $_GET['action']) && $_GET['action'] == 'edit') {
            $is_prettypay = $prli_link->is_post_prettypay_link((int) $_GET['post']);
          }

          if($is_prettypay) {
            $labels = array(
              'name' => esc_html__('PrettyPay™ Links', 'pretty-link'),
              'singular_name' => esc_html__('PrettyPay™ Link', 'pretty-link'),
              'add_new_item' => esc_html__('Add New PrettyPay™ Link', 'pretty-link'),
              'edit_item' => esc_html__('Edit PrettyPay™ Link', 'pretty-link'),
              'new_item' => esc_html__('New PrettyPay™ Link', 'pretty-link'),
              'view_item' => esc_html__('View PrettyPay™ Link', 'pretty-link'),
              'search_items' => esc_html__('Search PrettyPay™ Links', 'pretty-link'),
              'not_found' => esc_html__('No PrettyPay™ Links found', 'pretty-link'),
              'not_found_in_trash' => esc_html__('No PrettyPay™ Links found in Trash', 'pretty-link'),
              'parent_item_colon' => esc_html__('Parent PrettyPay™ Link:', 'pretty-link'),
            );

            foreach($labels as $key => $label) {
              if(isset($post_type_object->labels->$key)) {
                $post_type_object->labels->$key = $label;
              }
            }
          }
        }
      }
    }
  }

  public static function show_prettypay_popup() {
    $account_email = get_option( PrliAuthConnectHelper::OPTION_KEY_AUTH_ACCOUNT_EMAIL );
    $secret = get_option( PrliAuthConnectHelper::OPTION_KEY_AUTH_ACCOUNT_SECRET );
    $site_uuid = get_option( PrliAuthConnectHelper::OPTION_KEY_AUTH_ACCOUNT_SITE_UUID );
    $id = PrliStripeConnect::get_method_id();

    if( $account_email && $secret && $site_uuid ) {
      $stripe_connect_url = PrliStripeConnect::get_stripe_connect_url();
    } else {
      $return_url = admin_url('edit.php?post_type=pretty-link&page=pretty-link-options&nav_action=payments');
      $stripe_connect_url = PrliAuthenticatorController::get_auth_connect_url( $return_url, array(
        'stripe_connect' => 'true',
        'method_id' => $id
      ));
    }

    include_once PRLI_VIEWS_PATH . "/admin/popups/prettypay.php";
  }
/**
 * Updates the priority of the ACF meta box.
 *
 * @param int $priority The current priority of the meta box.
 * @return string The updated priority of the meta box.
 */
  public function update_acf_meta_box_priority( $priority ) {
    global $typenow;
    if ( $typenow == PrliLink::$cpt ) {
      $priority = 'default';
    }
    return $priority;
  }
}
