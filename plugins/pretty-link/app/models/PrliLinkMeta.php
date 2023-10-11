<?php
if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); }

class PrliLinkMeta {
  var $table_name;

  public function __construct() {
    global $wpdb;

    $this->table_name = "{$wpdb->prefix}prli_link_metas";
  }

  public function get_link_meta($link_id, $meta_key, $return_var = false) {
    global $wpdb;

    static $cached;

    if(!isset($cached)) { $cached = array(); }

    if(isset($cached[$link_id][$meta_key][(int)$return_var])) {
      return $cached[$link_id][$meta_key][(int)$return_var];
    }

    $query = $wpdb->prepare("
        SELECT meta_value
          FROM {$this->table_name}
         WHERE meta_key=%s
           AND link_id=%d
      ",
      $meta_key,
      $link_id
    );

    if($return_var) {
      $res = $wpdb->get_var("{$query} LIMIT 1");
    }
    else {
      $res = $wpdb->get_col("{$query} ORDER BY meta_order, id", 0);
    }

    $cached[$link_id][$meta_key][(int)$return_var] = $res;

    return $res;
  }

  // This is just an alias for update_link_meta
  public function add_link_meta($link_id, $meta_key, $meta_value) {
    return $this->update_link_meta($link_id, $meta_key, $meta_value);
  }

  public function update_link_meta($link_id, $meta_key, $meta_values) {
    global $wpdb;

    $this->delete_link_meta($link_id, $meta_key);

    if(!is_array($meta_values)) { $meta_values = array($meta_values); }

    $status = false;

    foreach($meta_values as $meta_order => $meta_value) {
      $status = $this->add_link_meta_item($link_id, $meta_key, $meta_value, $meta_order);
    }

    return $status;
  }

  // Add a single link meta item
  private function add_link_meta_item($link_id, $meta_key, $meta_value, $meta_order=0) {
    global $wpdb;

    $query = $wpdb->prepare("
        INSERT INTO {$this->table_name}
               (meta_key,meta_value,link_id,meta_order,created_at)
        VALUES (%s,%s,%d,%d,%s)
      ",
      $meta_key,
      $meta_value,
      $link_id,
      $meta_order,
      PrliUtils::db_now()
    );

    return $wpdb->query($query);
  }

  public function delete_link_meta($link_id, $meta_key) {
    global $wpdb;

    $query = $wpdb->prepare("
        DELETE FROM {$this->table_name}
         WHERE meta_key=%s
           AND link_id=%d
      ",
      $meta_key,
      $link_id
    );

    return $wpdb->query($query);
  }

  public function delete_link_metas($link_id) {
    global $wpdb;

    $query = $wpdb->prepare("
        DELETE FROM {$this->table_name}
         WHERE link_id=%d
      ",
      $link_id
    );

    return $wpdb->query($query);
  }

} //End class

