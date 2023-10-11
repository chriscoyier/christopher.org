<?php
if(!defined('ABSPATH'))
  die('You are not allowed to call this page directly.');

class PrliGroup
{
  var $table_name;

  public function __construct()
  {
    global $wpdb;
    $this->table_name = "{$wpdb->prefix}prli_groups";
  }

  public function create( $values )
  {
    global $wpdb;

    $query = "INSERT INTO {$this->table_name} (name,description,created_at) VALUES (%s, %s, NOW())";
    $query = $wpdb->prepare( $query, $values['name'], $values['description'] );
    $query_results = $wpdb->query($query);
    return $wpdb->insert_id;
  }

  public function update( $id, $values )
  {
    global $wpdb;

    $query = "UPDATE {$this->table_name} SET name = %s, description = %s WHERE id = %d";
    $query = $wpdb->prepare($query, $values['name'], $values['description'], $id);
    $query_results = $wpdb->query($query);
    return $query_results;
  }

  public function destroy( $id )
  {
    global $wpdb, $prli_link;

    // Disconnect the links from this group
    $query = $wpdb->prepare("UPDATE {$prli_link->table_name} SET group_id = NULL WHERE group_id = %d", $id);
    $wpdb->query($query);

    $destroy = $wpdb->prepare("DELETE FROM {$this->table_name} WHERE id = %d", $id);
    return $wpdb->query($destroy);
  }

  public function getOne( $id, $include_stats = false )
  {
      global $wpdb, $prli_link, $prli_click;

      if($include_stats)
        $query = 'SELECT gr.*, (SELECT COUNT(*) FROM ' . $prli_link->table_name . ' li WHERE li.group_id = gr.id) as link_count FROM ' . $this->table_name . ' gr WHERE id=' . $id;
      else
        $query = 'SELECT gr.* FROM ' . $this->table_name . ' gr WHERE id=' . $id;
      return $wpdb->get_row($query);
  }

  public function getAll( $where = '', $order_by = '', $return_type = OBJECT, $include_stats = false )
  {
      global $wpdb, $prli_utils, $prli_link, $prli_click;

      if($include_stats)
        $query = 'SELECT gr.*, (SELECT COUNT(*) FROM ' . $prli_link->table_name . ' li WHERE li.group_id = gr.id) as link_count FROM ' . $this->table_name . ' gr' . $prli_utils->prepend_and_or_where(' WHERE', $where) . $order_by;
      else
        $query = 'SELECT gr.* FROM ' . $this->table_name . " gr" . $prli_utils->prepend_and_or_where(' WHERE', $where) . $order_by;
      return $wpdb->get_results($query, $return_type);
  }

  // Pagination Methods
  public function getRecordCount($where="")
  {
      global $wpdb, $prli_utils;
      $query = 'SELECT COUNT(*) FROM ' . $this->table_name . $prli_utils->prepend_and_or_where(' WHERE', $where);
      return $wpdb->get_var($query);
  }

  public function getPageCount($p_size, $where="")
  {
      return ceil((int)$this->getRecordCount($where) / (int)$p_size);
  }

  public function getPage($current_p,$p_size, $where = "", $order_by = '')
  {
      global $wpdb, $prli_link, $prli_utils, $prli_click;
      $end_index = $current_p * $p_size;
      $start_index = $end_index - $p_size;
      $query = 'SELECT gr.*, (SELECT COUNT(*) FROM ' . $prli_link->table_name . ' li WHERE li.group_id = gr.id) as link_count FROM ' . $this->table_name . ' gr' . $prli_utils->prepend_and_or_where(' WHERE', $where) . $order_by .' LIMIT ' . $start_index . ',' . $p_size;
      $results = $wpdb->get_results($query);
      return $results;
  }

  // Set defaults and grab get or post of each possible param
  public function get_params_array()
  {
    $values = array(
       'action'     => sanitize_key(stripslashes(isset($_GET['action'])?$_GET['action']:(isset($_POST['action'])?$_POST['action']:'list'))),
       'id'         => (isset($_GET['id'])?(int)$_GET['id']:(isset($_POST['id'])?(int)$_POST['id']:'')),
       'paged'      => (isset($_GET['paged'])?(int)$_GET['paged']:(isset($_POST['paged'])?(int)$_POST['paged']:1)),
       'group'      => (isset($_GET['group'])?(int)$_GET['group']:(isset($_POST['group'])?(int)$_POST['group']:'')),
       'search'     => sanitize_text_field(stripslashes(isset($_GET['search'])?$_GET['search']:(isset($_POST['search'])?$_POST['search']:''))),
       'sort'       => sanitize_key(stripslashes(isset($_GET['sort'])?$_GET['sort']:(isset($_POST['sort'])?$_POST['sort']:''))),
       'sdir'       => sanitize_key(stripslashes(isset($_GET['sdir'])?$_GET['sdir']:(isset($_POST['sdir'])?$_POST['sdir']:'')))
    );

    return $values;
  }

  public function validate( $values )
  {
    global $wpdb, $prli_utils;

    $errors = array();
    if( empty($values['name']) )
      $errors[] = __('Group must have a name.', 'pretty-link');

    return $errors;
  }
}
