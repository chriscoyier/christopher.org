<?php
if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); }

class PrliLink {
  public $table_name;
  public static $cpt = 'pretty-link';
  public static $nonce_str = 'pretty-link-nonce';

  public function __construct() {
    global $wpdb;
    $this->table_name = "{$wpdb->prefix}prli_links";
  }

  public static function get_count() {
    global $wpdb, $prli_link;
    $q = "SELECT COUNT(*) FROM {$prli_link->table_name} WHERE link_status='enabled'";
    return $wpdb->get_var($q);
  }

  public static function get_first_date() {
    global $wpdb, $prli_link;
    $q = "SELECT min(created_at) FROM {$prli_link->table_name} WHERE link_status='enabled'";
    return $wpdb->get_var($q);
  }

  public function create( $values ) {
    global $wpdb, $prli_link_meta;
    static $slugs_done;

    if(!isset($slugs_done) || !is_array($slugs_done)) { $slugs_done = array(); }

    if(!empty($values['slug'])) {
      if(isset($slugs_done[$values['slug']]) && $slugs_done[$values['slug']] > 0) {
        return $slugs_done[$values['slug']]; // Return existing link id so we don't create a duplicate
      }
    }

    $now = current_time('mysql');

    $values = $this->sanitize($values);
    $values['created_at'] = $values['updated_at'] = $now;

    if($values['link_cpt_id'] == 0) { ## Autocreate CPT

      $post_id = $this->create_cpt_for_link($values['name'], $now, $now);

      if($post_id == 0 || is_wp_error($post_id)) {
        error_log("Unable to create CPT Post for Link");
      }

      $values['link_cpt_id'] = $post_id;
    }

    if(empty($values['name'])) {
      if($values['redirect_type'] == 'pixel') {
        $values['name'] = $values['slug'];
      }
      else {
        $values['name'] = PrliUtils::get_page_title($values['url'],$values['slug']);
      }

      // Update the post_title field directly so we don't activate the action
      // that ultimately calls this function lest we get into an infinite loop
      $q = $wpdb->prepare("UPDATE {$wpdb->posts} SET post_title=%s WHERE ID=%d", $values['name'], $values['link_cpt_id']);
      $wpdb->query($q);
    }

    $query_results = $wpdb->insert($this->table_name, $values);

    $link_id = ($query_results ? $wpdb->insert_id : false);

    // Static caching - already here during this request
    // Prevents dups
    $slugs_done[$values['slug']] = $link_id;

    //If JS or MetaRefresh are the default types we need to set this to 0 or the redirects will fail
    if($link_id) {
      $prli_link_meta->update_link_meta($link_id, 'delay', 0);
    }

    $this->update_cpt_title($values['link_cpt_id'], $values['name']);
    $this->update_cpt_status($values['link_cpt_id']);
    $this->update_cpt_post_date($values['link_cpt_id'], $now);

    do_action('prli-create-link', $link_id, $values);

    return $link_id;
  }

  public function update( $id, $values ) {
    global $wpdb, $prli_link;

    $now = current_time('mysql');

    $values = $this->sanitize($values);
    $values['updated_at'] = $now;

    $title = isset($values['name'])?trim($values['name']):'';

    if(empty($title)) {
      error_log("REDIRECT TYPE: " . $values['redirect_type']);
      if($values['redirect_type'] == 'pixel') {
        $title = $values['slug'];
      }
      else {
        $title = PrliUtils::get_page_title($values['url'],$values['slug']);
      }
    }

    $query_results = $wpdb->update($this->table_name, $values, array('id' => $id));

    $link = $this->getOne($id);
    $this->update_cpt_title($link->link_cpt_id, $title);
    $this->update_cpt_status($link->link_cpt_id);
    $this->update_cpt_post_modified($link->link_cpt_id, $now);

    return $id;
  }

  public function disable_link($id) {
    return $this->update_link_status( $id, 'disabled' );
  }

  public function enable_link($id) {
    return $this->update_link_status( $id, 'enabled' );
  }

  public function update_link_status( $id, $link_status='enabled' ) {
    global $wpdb;

    $q = $wpdb->prepare("
        UPDATE {$this->table_name}
           SET link_status=%s
         WHERE id=%d
      ",
      $link_status,
      $id
    );

    return $wpdb->query($q);
  }

  public function update_group($id, $value, $group_id) {
    global $wpdb;

    if (isset($value)) {
      $query = $wpdb->prepare(
        "UPDATE {$this->table_name} SET group_id = %d WHERE id = %d",
        $group_id,
        $id
      );
    } else {
      $query = $wpdb->prepare(
        "UPDATE {$this->table_name} SET group_id = NULL WHERE id = %d",
        $id
      );
    }

    $query_results = $wpdb->query($query);

    return $query_results;
  }

  public function destroy($id, $delete_mode='delete_cpt') {
    global $wpdb, $prli_click, $prli_link_meta;

    do_action('prli_delete_link', $id);

    $link = $this->getOne($id);
    if($delete_mode=='delete_cpt' && $link->link_cpt_id > 0) {
      wp_delete_post($link->link_cpt_id, true);
    }

    $metas = $wpdb->prepare("DELETE FROM {$prli_link_meta->table_name} WHERE link_id=%d",$id);
    $reset = $wpdb->prepare("DELETE FROM {$prli_click->table_name} WHERE link_id=%d",$id);
    $destroy = $wpdb->prepare("DELETE FROM {$this->table_name} WHERE id=%d",$id);

    $wpdb->query($metas);
    $wpdb->query($reset);

    return $wpdb->query($destroy);
  }

  public function reset($id) {
    global $wpdb, $prli_click, $prli_link_meta;

    $prli_link_meta->delete_link_meta($id, 'static-clicks');
    $prli_link_meta->delete_link_meta($id, 'static-uniques');

    $reset = $wpdb->prepare("DELETE FROM {$prli_click->table_name} WHERE link_id=%d", $id);

    return $wpdb->query($reset);
  }

  public function getOneFromSlug( $slug, $return_type = OBJECT, $include_stats = false, $exclude_disabled=true ) {
    return $this->get_one_by( 'slug', $slug, $return_type, $include_stats, $exclude_disabled );
  }

  public function getOne( $id, $return_type = OBJECT, $include_stats = false, $exclude_disabled=true ) {
    return $this->get_one_by( 'id', $id, $return_type, $include_stats, $exclude_disabled );
  }

  public function get_one_by( $field, $val, $return_type = OBJECT, $include_stats = false, $exclude_disabled=true ) {
    global $wpdb, $prli_click, $prli_options, $prli_link_meta, $prli_blogurl;

    $struct = PrliUtils::get_permalink_pre_slug_uri();

    if($include_stats) {
      $query = "SELECT li.*, CONCAT(\"{$prli_blogurl}\",\"{$struct}\",li.slug) AS pretty_url, ";

      $op = $prli_click->get_exclude_where_clause( ' AND' );

      if($prli_options->extended_tracking != 'count') {
        $query .= "
          (
            SELECT COUNT(*)
              FROM {$prli_click->table_name} AS cl
             WHERE cl.link_id = li.id
             {$op}
          ) as clicks,
          (
            SELECT COUNT(*) FROM {$prli_click->table_name} AS cl
             WHERE cl.link_id = li.id
               AND cl.first_click <> 0
              {$op}
          ) as uniques
        ";
      }
      else {
        $query .= "
          (
            SELECT lm.meta_value
              FROM {$prli_link_meta->table_name} AS lm
             WHERE lm.meta_key=\"static-clicks\"
               AND lm.link_id=li.id LIMIT 1
          ) as clicks,
          (
            SELECT lm.meta_value FROM {$prli_link_meta->table_name} AS lm
             WHERE lm.meta_key=\"static-uniques\"
               AND lm.link_id=li.id
             LIMIT 1
          ) as uniques
        ";
      }
    }
    else {
      $query = "SELECT li.*, CONCAT(\"{$prli_blogurl}\",\"{$struct}\",li.slug) AS pretty_url";
    }

    $query .= "
      FROM {$this->table_name} AS li
      WHERE {$field}=%s
    ";

    if($exclude_disabled) {
      $query .= "
          AND link_status='enabled'
      ";
    }

    $query = $wpdb->prepare($query, $val);
    $link  = $wpdb->get_row($query, $return_type);

    //get_row returns a null if not found - we don't want nulls
    if(!$link) { $link = false; }

    if($include_stats and $link and $prli_options->extended_tracking == 'count') {
      $link->clicks  = $prli_link_meta->get_link_meta($link->id,'static-clicks',true);
      $link->uniques = $prli_link_meta->get_link_meta($link->id,'static-uniques',true);
    }

    return $link;
  }

  public function find_first_target_url($target_url) {
    global $wpdb;
    $query_str = "SELECT id FROM {$this->table_name} WHERE link_status='enabled' AND url=%s LIMIT 1";
    $query = $wpdb->prepare($query_str,$target_url);
    return $wpdb->get_var($query);
  }

  public function get_or_create_pretty_link_for_target_url( $target_url, $group=0 ) {
    global $wpdb;
    $query_str = "SELECT * FROM {$this->table_name} WHERE link_status='enabled' AND url=%s LIMIT 1";
    $query = $wpdb->prepare($query_str,$target_url);
    $pretty_link = $wpdb->get_row($query);

    if(empty($pretty_link) or !$pretty_link) {
      $pl_insert_id = prli_create_pretty_link( $target_url, '', '', '', $group );
      $pretty_link = $this->getOne($pl_insert_id);
    }

    if( !isset($pretty_link) or empty($pretty_link) or !$pretty_link )
      return false;
    else
      return $pretty_link;
  }

  public function is_pretty_link($url, $check_domain=true) {
    global $prli_blogurl;

    if( !$check_domain or preg_match( '#^' . preg_quote( $prli_blogurl ) . '#', $url ) ) {
      $uri = preg_replace('#' . preg_quote($prli_blogurl) . '#', '', $url);

      // Resolve WP installs in sub-directories
      preg_match('#^(https?://.*?)(/.*)$#', $prli_blogurl, $subdir);

      $struct = PrliUtils::get_permalink_pre_slug_regex();

      $subdir_str = (isset($subdir[2])?$subdir[2]:'');

      $match_str = '#^'.$subdir_str.'('.$struct.')([^\?]*?)([\?].*?)?$#';

      if(preg_match($match_str, $uri, $match_val)) {
        // Match longest slug -- this is the most common
        if( $pretty_link_found = $this->is_pretty_link_slug( $match_val[2] ) )
          return compact('pretty_link_found');
      }
    }

    return false;
  }

  public function is_pretty_link_slug($slug) {
    return apply_filters('prli-check-if-slug', $this->getOneFromSlug( rawurldecode($slug) ), rawurldecode($slug));
  }

  public function get_link_min( $id, $return_type = OBJECT ) {
    global $wpdb, $prli_link_meta;
    $query_str = "SELECT * FROM {$this->table_name} WHERE link_status='enabled' AND id=%d";
    $query = $wpdb->prepare($query_str, $id);
    $res = $wpdb->get_row($query, $return_type);

    //Load in some meta data too
    if($res && $return_type == ARRAY_A) {
      $res['delay'] = (int)$prli_link_meta->get_link_meta($id, 'delay', true);
      $res['google_tracking'] = (int)$prli_link_meta->get_link_meta($id, 'google_tracking', true);
    }

    return $res;
  }

  public function getAll($where = '', $order_by = '', $return_type = OBJECT, $include_stats = false) {
    global $wpdb, $prli_click, $prli_group, $prli_link_meta, $prli_options, $prli_utils, $prli_blogurl;

    $struct = PrliUtils::get_permalink_pre_slug_regex();

    $op = $prli_click->get_exclude_where_clause( ' AND' );

    if($include_stats) {
      $query = "SELECT li.*, CONCAT(\"{$prli_blogurl}\",\"{$struct}\",li.slug) AS pretty_url, ";
      if($prli_options->extended_tracking != 'count') {
        $query .= "
          (
            SELECT COUNT(*)
              FROM {$prli_click->table_name} AS cl
             WHERE cl.link_id = li.id
              {$op}
          ) as clicks,
          (
            SELECT COUNT(*)
              FROM {$prli_click->table_name} AS cl
             WHERE cl.link_id = li.id
               AND cl.first_click <> 0
             {$op}
          ) as uniques
        ";
      }
      else {
        $query .= "
          (
            SELECT lm.meta_value
              FROM {$prli_link_meta->table_name} AS lm
             WHERE lm.meta_key=\"static-clicks\"
               AND lm.link_id=li.id
             LIMIT 1
          ) as clicks,
          (
            SELECT lm.meta_value
              FROM {$prli_link_meta->table_name} AS lm
             WHERE lm.meta_key=\"static-uniques\"
               AND lm.link_id=li.id
             LIMIT 1
          ) as uniques
        ";
      }
    }
    else {
      $query = "SELECT li.*,CONCAT(\"{$prli_blogurl}\",\"{$struct}\",li.slug) AS pretty_url ";
    }

    $query .= "FROM {$this->table_name} AS li
     WHERE li.link_status='enabled'
           " . $prli_utils->prepend_and_or_where(' AND', $where) .
           $order_by;

    return $wpdb->get_results($query, $return_type);
  }

  public function generateValidSlug($num_chars = 4) {
    global $wpdb, $plp_update, $plp_options;
    $slug_prefix = '';

    //Maybe use a different number of characters?
    if($plp_update->is_installed()) {
      if(!empty($plp_options->base_slug_prefix)) {
        $slug_prefix = $plp_options->base_slug_prefix . '/';
      }

      $num_chars = $plp_options->num_slug_chars;
    }

    $slug = $slug_prefix . PrliUtils::gen_random_string($num_chars);

    // Intentionally not checking to see if link is enabled or disabled
    $query = "SELECT slug FROM {$this->table_name}";
    $slugs = $wpdb->get_col($query,0);

    // It is highly unlikely that we'll ever see 2 identical random slugs
    // but just in case, here's some code to prevent collisions
    while(in_array($slug, $slugs) || is_wp_error(PrliUtils::is_slug_available($slug))) {
      $slug = $slug_prefix . PrliUtils::gen_random_string($num_chars);
    }

    return apply_filters('prli-auto-generated-slug', $slug, $slugs, $num_chars);
  }

  public function get_pretty_link_url($slug, $esc_url=false) {
    global $prli_blogurl;

    $link = $this->getOneFromSlug($slug);
    $pretty_link_url = $prli_blogurl . PrliUtils::get_permalink_pre_slug_uri() . $link->slug;
    if($esc_url) {
      $pretty_link_url = esc_url($pretty_link_url);
    }
    else {
      $pretty_link_url = $pretty_link_url;
    }

    if( ( !isset($link->param_forwarding) || empty($link->param_forwarding) || $link->param_forwarding=='off' ) &&
        ( isset($link->redirect_type) && $link->redirect_type == 'pixel' ) ) {
      return '&lt;img src="'.$pretty_link_url.'" width="1" height="1" style="display: none" /&gt;';
    }
    else {
      return $pretty_link_url;
    }
  }

  /**
   * Sanitize the given link options and return them
   *
   * @param  array $values
   * @return array
   */
  public function sanitize($values) {
    $sanitized = array(
      'redirect_type' => isset($values['redirect_type']) && is_string($values['redirect_type']) ? sanitize_key($values['redirect_type']) : '307',
      'url' => isset($values['url']) && is_string($values['url']) ? esc_url_raw(trim($values['url'])) : '',
      'slug' => isset($values['slug']) && is_string($values['slug']) ? sanitize_text_field($values['slug']) : '',
      'name' => isset($values['name']) && is_string($values['name']) ? sanitize_text_field($values['name']) : '',
      'description' => isset($values['description']) && is_string($values['description']) ? sanitize_textarea_field($values['description']) : '',
      'group_id' => isset($values['group_id']) && is_numeric($values['group_id']) ? (int) $values['group_id'] : null,
      'nofollow' => isset($values['nofollow']) ? 1 : 0,
      'sponsored' => isset($values['sponsored']) ? 1 : 0,
      'param_forwarding' => isset($values['param_forwarding']) ? 1 : 0,
      'track_me' => isset($values['track_me']) ? 1 : 0,
      'link_cpt_id' => isset($values['link_cpt_id']) && is_numeric($values['link_cpt_id']) ? (int) $values['link_cpt_id'] : 0
    );

    return $sanitized;
  }

  public function validate( $values , $id = null ) {
    global $wpdb, $prli_utils, $prli_blogurl;

    $values = $this->sanitize($values);

    $errors = array();
    if( empty($values['url']) && $values['redirect_type'] != 'pixel' ) {
      $errors[] = __("Target URL can't be blank", 'pretty-link');
    }

    if( $values['url'] == $prli_blogurl.PrliUtils::get_permalink_pre_slug_uri().$values['slug'] ) {
      $errors[] = __("Target URL must be different than the Pretty Link", 'pretty-link');
    }

    if( !empty($values['url']) && !PrliUtils::is_url($values['url']) ) {
      $errors[] = __("Link URL must be a correctly formatted url", 'pretty-link');
    }

    if( preg_match('/^[\?\&\#]+$/', $values['slug'] ) ) {
      $errors[] = __("Pretty Link slugs must not contain question marks, ampersands or number signs.", 'pretty-link');
    }

    if( preg_match('#/$#', $values['slug']) ) {
      $errors[] = __("Pretty Link slugs must not end with a slash (\"/\")", 'pretty-link');
    }

    $slug_is_available = PrliUtils::is_slug_available($values['slug'],$id);
    if(is_wp_error($slug_is_available)) {
      $errors[] = $slug_is_available->get_error_message();
    }

    return $errors;
  }

  public function get_link_slug($url) {
    global $prli_blogurl;
    $ugh = preg_quote($prli_blogurl.PrliUtils::get_permalink_pre_slug_uri(), '!');
    $mod = preg_replace("!^{$ugh}!", '', $url);
    return "\"{$mod}\"";
  }

  public function get_target_to_pretty_urls($urls=array(),$create_pretty_links=false) {
    global $wpdb, $prli_blogurl;

    if(empty($urls)) { return false; }

    $decoded_urls = array_map(
      function($url) {
        return esc_url_raw(html_entity_decode(rawurldecode($url)));
      },
      $urls
    );

    // Filter out urls that are already Pretty Links
    $where = "IN (" . implode( ',', array_map( array($this, 'get_link_slug'), $decoded_urls ) ) . ")";

    $query = "
      SELECT CONCAT(%s, li.slug) AS pretty_url
        FROM {$this->table_name} AS li
       WHERE li.link_status='enabled'
         AND li.slug {$where}
    ";
    $query = $wpdb->prepare($query, $prli_blogurl.PrliUtils::get_permalink_pre_slug_uri());
    $plinks = $wpdb->get_col($query);

    $decoded_urls = array_diff($decoded_urls, $plinks);

    if(empty($decoded_urls)) { return false; }

    $where = "IN (" . implode( ',', array_map(
      function($url) {
        return "\"{$url}\"";
      },
      $decoded_urls ) ) . ")";

    $query = "
      SELECT li.url AS target_url,
             CONCAT(%s, li.slug) AS pretty_url
        FROM {$this->table_name} AS li
       WHERE li.link_status='enabled'
    ";
    $query = $wpdb->prepare($query, $prli_blogurl.PrliUtils::get_permalink_pre_slug_uri());
    $query .= "         AND li.url {$where}";

    $results = (array)$wpdb->get_results($query);

    $prli_lookup = array();
    foreach($results as $url_hash) {
      if(isset($prli_lookup[$url_hash->target_url])) {
        $prli_lookup[$url_hash->target_url][] = $url_hash->pretty_url;
      }
      else {
        $prli_lookup[$url_hash->target_url] = array($url_hash->pretty_url);
      }
    }

    if($create_pretty_links) {
      foreach($decoded_urls as $url) {
        if(!isset($prli_lookup[$url])) {
          if( $id = prli_create_pretty_link( $url ) ) {
            $prli_lookup[$url] = array(prli_get_pretty_link_url($id));
          }
        }
      }
    }

    return $prli_lookup;
  }

  public static function bookmarklet_link() {
    global $prli_options;
    $site_url = site_url();
    return "javascript:location.href='{$site_url}/index.php?action=prli_bookmarklet&k={$prli_options->bookmarklet_auth}&target_url='+escape(location.href);";
  }

  public function get_link_from_cpt($cpt_id) {
    global $wpdb;

    $q = $wpdb->prepare("
        SELECT id
          FROM {$this->table_name}
         WHERE link_cpt_id=%d
      ",
      $cpt_id
    );

    $link_id = $wpdb->get_var($q);

    if(empty($link_id)) {
      $link_id = 0;
    }

    return $link_id;
  }

  public function update_link_cpt($link_id, $cpt_id) {
    global $wpdb;

    $q = $wpdb->prepare("
        UPDATE {$this->table_name}
           SET link_cpt_id=%d
         WHERE id=%d
      ",
      $cpt_id, $link_id
    );

    return $wpdb->get_var($q);
  }

  /** We do this directly in the database because wp_insert_post is very slow */
  public function create_cpt_for_link($title, $created_at, $updated_at) {
    global $wpdb;

    if (empty($created_at)) {
      $created_at = current_time('mysql');
    }

    if (empty($updated_at)) {
      $updated_at = current_time('mysql');
    }

    $inserted = $wpdb->insert(
      $wpdb->posts,
      array(
        'post_author' => get_current_user_id(),
        'post_title' => stripslashes($title),
        'post_type' => PrliLink::$cpt,
        'post_status' => 'publish',
        'post_date' => $created_at,
        'post_date_gmt' => get_gmt_from_date($created_at),
        'post_modified' => $updated_at,
        'post_modified_gmt' => get_gmt_from_date($updated_at),
        'comment_status' => 'closed',
        'ping_status' => 'closed'
      ),
      array('%d','%s','%s','%s','%s','%s','%s','%s')
    );

    return $inserted ? $wpdb->insert_id : false;
  }

  public function update_cpt_title($cpt_id, $title) {
    global $wpdb;

    $q = $wpdb->prepare("
        UPDATE {$wpdb->posts}
           SET `post_title` = %s
         WHERE `ID` = %d
           AND `post_type` = %s
      ",
      stripslashes($title),
      $cpt_id,
      PrliLink::$cpt
    );

    return $wpdb->query($q);
  }

  public function update_cpt_status($cpt_id, $status='publish') {
    global $wpdb;

    $q = $wpdb->prepare("
        UPDATE {$wpdb->posts}
           SET `post_status` = %s
         WHERE `ID` = %d
           AND `post_type` = %s
      ",
      $status,
      $cpt_id,
      PrliLink::$cpt
    );

    return $wpdb->query($q);
  }

  public function update_cpt_post_date($cpt_id, $now=false) {
    global $wpdb;

    if(empty($now)) {
      $now = current_time('mysql');
    }

    $wpdb->update(
      $wpdb->posts,
      array(
        'post_date' => $now,
        'post_date_gmt' => get_gmt_from_date($now)
      ),
      array(
        'ID' => $cpt_id,
        'post_type' => PrliLink::$cpt
      ),
      array('%s', '%s'),
      array('%d', '%s')
    );
  }

  public function update_cpt_post_modified($cpt_id, $now=false) {
    global $wpdb;

    if(empty($now)) {
      $now = current_time('mysql');
    }

    $wpdb->update(
      $wpdb->posts,
      array(
        'post_modified' => $now,
        'post_modified_gmt' => get_gmt_from_date($now)
      ),
      array(
        'ID' => $cpt_id,
        'post_type' => PrliLink::$cpt
      ),
      array('%s', '%s'),
      array('%d', '%s')
    );
  }

  /** For legacy handling of groups filter */
  public function get_all_legacy_groups() {
    global $wpdb;

    $prli_db = new PrliDb();

    $groups_table = "{$wpdb->prefix}prli_groups";
    $groups = array();

    if($prli_db->table_exists($groups_table)) {
      $q = "SELECT * FROM {$groups_table} ORDER BY `name`";
      $groups = $wpdb->get_results($q);
    }

    return $groups;
  }
}
