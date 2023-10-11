<?php

if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); }

class PrliAddonUpdates {
  public $slug, $main_file, $installed_version, $name, $description, $update_ctrl;

  public function __construct($slug, $main_file, $installed_version, $name, $description) {
    $this->slug                   = $slug;
    $this->main_file              = $main_file;
    $this->installed_version      = $installed_version;
    $this->name                   = $name;
    $this->description            = $description;
    $this->update_ctrl            = new PrliUpdateController();

    add_filter('pre_set_site_transient_update_plugins', array($this, 'queue_update'));
    add_action("in_plugin_update_message-$main_file", array($this, 'check_incorrect_edition'));
    add_action('prli_plugin_edition_changed', array($this, 'clear_update_transient'));
    add_filter('plugins_api', array($this, 'plugin_info'), 11, 3);
    add_action('prli_license_activated_before_queue_update', array($this, 'clear_update_transient'));
    add_action('prli_license_deactivated_before_queue_update', array($this, 'clear_update_transient'));
  }

  public function queue_update($transient) {
    if(empty($transient->checked)) {
      return $transient;
    }

    $update_info = get_site_transient('prli_update_info_' . $this->slug);

    if(!is_array($update_info)) {
      $args = array();

      if($this->update_ctrl->edge_updates || (defined('PRETTYLINK_EDGE') && PRETTYLINK_EDGE)) {
        $args['edge'] = 'true';
      }

      if(empty($this->update_ctrl->mothership_license)) {
        // Just here to query for the current version
        try {
          $version_info = $this->update_ctrl->send_mothership_request("/versions/latest/{$this->slug}", $args);
          $curr_version = $version_info['version'];
          $download_url = '';
        }
        catch(\Exception $e) {
          if(isset($transient->response[$this->main_file])) {
            unset($transient->response[$this->main_file]);
          }

          return $transient;
        }
      }
      else {
        try {
          $args['domain'] = urlencode(PrliUtils::site_domain());

          $license_info = $this->update_ctrl->send_mothership_request("/versions/info/{$this->slug}/{$this->update_ctrl->mothership_license}", $args);
          $curr_version = $license_info['version'];
          $download_url = $license_info['url'];

          if(PrliUtils::is_incorrect_edition_installed()) {
            $download_url = '';
          }
        }
        catch(\Exception $e) {
          try {
            // Just here to query for the current version
            $version_info = $this->update_ctrl->send_mothership_request("/versions/latest/{$this->slug}", $args);
            $curr_version = $version_info['version'];
            $download_url = '';
          } catch (\Exception $e) {
            if(isset($transient->response[$this->main_file])) {
              unset($transient->response[$this->main_file]);
            }

            return $transient;
          }
        }
      }

      set_site_transient(
        'prli_update_info_' . $this->slug,
        compact('curr_version', 'download_url'),
        (12 * HOUR_IN_SECONDS)
      );
    }
    else {
      $curr_version = isset($update_info['curr_version']) ? $update_info['curr_version'] : $this->installed_version;
      $download_url = isset($update_info['download_url']) ? $update_info['download_url'] : '';
    }

    if(isset($curr_version) && version_compare($curr_version, $this->installed_version, '>')) {
      global $wp_version;

      $transient->response[$this->main_file] = (object) array(
        'id' => $this->main_file,
        'slug' => $this->slug,
        'plugin' => $this->main_file,
        'new_version' => $curr_version,
        'url' => 'https://prettylinks.com/',
        'package' => $download_url,
        'tested' => $wp_version
      );
    }
    else {
      unset($transient->response[$this->main_file]);

      // Enables the "Enable auto-updates" link
      $transient->no_update[$this->main_file] = (object) array(
        'id' => $this->main_file,
        'slug' => $this->slug,
        'plugin' => $this->main_file,
        'new_version' => $this->installed_version,
        'url' => 'https://prettylinks.com/',
        'package' => ''
      );
    }

    return $transient;
  }

  public function plugin_info($api, $action, $args) {
    global $wp_version;

    if(!isset($action) || $action != 'plugin_information') {
      return $api;
    }

    if(!isset($args->slug) || $args->slug != $this->slug) {
      return $api;
    }

    $args = array();

    if($this->update_ctrl->edge_updates || (defined('PRETTYLINK_EDGE') && PRETTYLINK_EDGE)) {
      $args['edge'] = 'true';
    }

    if(empty($this->update_ctrl->mothership_license)) {
      try {
        // Just here to query for the current version
        $version_info = $this->update_ctrl->send_mothership_request("/versions/latest/{$this->slug}", $args);
        $curr_version = $version_info['version'];
        $version_date = $version_info['version_date'];
        $download_url = '';
      }
      catch(\Exception $e) {
        return $api;
      }
    }
    else {
      try {
        $args['domain'] = urlencode(PrliUtils::site_domain());

        $license_info = $this->update_ctrl->send_mothership_request("/versions/info/{$this->slug}/{$this->update_ctrl->mothership_license}", $args);
        $curr_version = $license_info['version'];
        $version_date = $license_info['version_date'];
        $download_url = $license_info['url'];
      }
      catch(\Exception $e) {
        try {
          // Just here to query for the current version
          $version_info = $this->update_ctrl->send_mothership_request("/versions/latest/{$this->slug}", $args);
          $curr_version = $version_info['version'];
          $version_date = $version_info['version_date'];
          $download_url = '';
        }
        catch(\Exception $e) {
          return $api;
        }
      }
    }

    return (object) array(
      'slug' => $this->slug,
      'name' => esc_html($this->name),
      'author' => PRLI_AUTHOR,
      'author_profile' => PRLI_AUTHOR_URI,
      'contributors' => array(
        'caseproof' => array(
          'profile' => PRLI_AUTHOR_URI,
          'avatar' => 'https://secure.gravatar.com/avatar/762b61e36276ff6dc0d7b03b8c19cfab?s=96&d=monsterid&r=g',
          'display_name' => PRLI_AUTHOR
        )
      ),
      'homepage' => 'https://prettylinks.com/',
      'version' => $curr_version,
      'new_version' => $curr_version,
      'requires' => '5.2',
      'requires_php' => '5.2.0',
      'tested' => $wp_version,
      'compatibility' => array($wp_version => array($curr_version => array(100, 0, 0))),
      'rating' => '100.00',
      'num_ratings' => '1',
      'added' => '2012-12-02',
      'last_updated' => $version_date,
      'tags' => array(
        'affiliate links' => 'affiliate links',
        'link branding' => 'link branding',
        'link shortner' => 'link shortner',
        'link tracking' => 'link tracking',
        'redirects' => 'redirects'
      ),
      'sections' => array(
        'description' => '<p>' . $this->description . '</p>',
        'faq' => '<p>' . sprintf(esc_html__('You can access in-depth information about Pretty Links at %1$sthe Pretty Links User Manual%2$s.', 'pretty-link'), '<a href="https://prettylinks.com/docs/">', '</a>') . '</p>'
      ),
      'download_link' => $download_url
    );
  }

  public function check_incorrect_edition() {
    if(PrliUtils::is_incorrect_edition_installed()) {
      printf(
        /* translators: %1$s: open link tag, %2$s: close link tag */
        ' <strong>' . esc_html__('To restore automatic updates, %1$sinstall the correct edition%2$s of Pretty Links.', 'pretty-link') . '</strong>',
        sprintf('<a href="%s">', esc_url(admin_url('edit.php?post_type=pretty-link&page=pretty-link-updates'))),
        '</a>'
      );
    }
  }

  public function clear_update_transient() {
    delete_site_transient('prli_update_info_' . $this->slug);
  }
}