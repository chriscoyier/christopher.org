<?php
if(!defined('ABSPATH'))
  die('You are not allowed to call this page directly.');

class PrliClick {
  var $table_name;

  public function __construct() {
    global $wpdb;
    $this->table_name = "{$wpdb->prefix}prli_clicks";
  }

  public static function get_count() {
    global $wpdb, $prli_options, $prli_click;

    if($prli_options->extended_tracking != 'count') {
      $q = "SELECT COUNT(*) FROM {$prli_click->table_name}";
    }
    else {
      $q = "
        SELECT SUM(CAST(meta_value AS SIGNED))
          FROM {$wpdb->prefix}prli_link_metas
         WHERE meta_key='static-clicks'
      ";
    }

    return $wpdb->get_var($q);
  }

  public static function get_first_date() {
    global $wpdb, $prli_options, $prli_click;

    if($prli_options->extended_tracking != 'count') {
      $q = "SELECT min(created_at) FROM {$prli_click->table_name}";
      return $wpdb->get_var($q);
    }
    else {
      return false;
    }
  }

  public function get_exclude_where_clause( $where = '', $abbr = 'cl') {
    global $prli_options;
    $exclude_list = trim($prli_options->prli_exclude_ips);
    $filter_bots  = (int)$prli_options->filter_robots;
    $return_stmt = '';

    if(empty($exclude_list) and $filter_bots == 0)
      return $return_stmt;

    $return_stmt .= (empty($where)?'':' AND');

    if(!empty($exclude_list)) {
      $exclude_ips = explode(',',$exclude_list);
      for($i = 0; $i < count($exclude_ips); $i++) {
        $exclude_ip = trim(preg_replace('#\*#','%%',$exclude_ips[$i]));

        if($i > 0)
          $return_stmt .= ' AND';

        $return_stmt .= " {$abbr}.ip NOT LIKE '{$exclude_ip}'";
      }
    }

    if($filter_bots != 0) {
      $return_stmt .= (empty($exclude_list)?' (':' AND (');
      $whitelist = trim($prli_options->whitelist_ips);

      if(!empty($whitelist)) {
        $whitelist_ips = explode(',',$whitelist);

        for($i = 0; $i <= count($whitelist_ips); $i++) {
          if($i == count($whitelist_ips)) {
            $return_stmt .= ' OR';
            break;
          }

          $whitelist_ip = trim(preg_replace('#\*#','%%',$whitelist_ips[$i]));

          if($i > 0)
            $return_stmt .= ' OR';

          $return_stmt .= " {$abbr}.ip LIKE '{$whitelist_ip}'";
        }

      }

      $return_stmt .= " {$abbr}.robot=0 )";
    }

    return $return_stmt;
  }

  public function getOne($id) {
    global $wpdb, $prli_link, $prli_utils;

    $query = 'SELECT cl.*, (SELECT count(*) FROM '. $this->table_name .' cl2 WHERE cl2.ip = cl.ip) as ip_count, (SELECT count(*) FROM '. $this->table_name .' cl3 WHERE cl3.vuid = cl.vuid) as vuid_count, li.name as link_name FROM ' . $this->table_name . ' cl, ' . $prli_link->table_name . ' li WHERE li.id = cl.link_id AND id=' . $id . $prli_utils->prepend_and_or_where(' AND',$this->get_exclude_where_clause());

    return $wpdb->get_row($query);
  }

  // SELECT cl.*,li.name as link_name FROM wp_prli_clicks cl, wp_prli_links li WHERE li.id = cl.link_id ORDER BY created_at DESC
  public function getAll($where = '', $order = '', $include_stats = false, $limit = '') {
    global $wpdb, $prli_link, $prli_utils;

    $where .= $this->get_exclude_where_clause( $where );
    $where = $prli_utils->prepend_and_or_where(' AND', $where);
    $limit = (empty($limit)?'':" LIMIT {$limit}");
    if($include_stats)
      $query = 'SELECT cl.*, (SELECT count(*) FROM '. $this->table_name .' cl2 WHERE cl2.ip = cl.ip) as ip_count, (SELECT count(*) FROM '. $this->table_name .' cl3 WHERE cl3.vuid = cl.vuid) as vuid_count, li.name as link_name FROM ' . $this->table_name . ' cl, ' . $prli_link->table_name . ' li WHERE li.id = cl.link_id' . $where . $order . $limit;
    else
      $query = 'SELECT cl.*, li.name as link_name FROM ' . $this->table_name . ' cl, ' . $prli_link->table_name . ' li WHERE li.id = cl.link_id' . $where . $order . $limit;

    return $wpdb->get_results($query);
  }

  // Delete all of the clicks from the database.
  public function clearAllClicks() {
    global $wpdb, $prli_link_meta;

    $query = $wpdb->prepare("DELETE FROM {$prli_link_meta->table_name} WHERE meta_key=%s OR meta_key=%s", 'static-clicks', 'static-uniques');
    $wpdb->query($query);

    $query = "TRUNCATE TABLE {$this->table_name}";
    return $wpdb->query($query);
  }

  /* This will delete all the clicks in the database by their age measured in days. */
  public function clear_clicks_by_age_in_days($days) {
    global $wpdb;

    $days_in_seconds = $days * 24 * 60 * 60;
    $oldest_time     = time() - $days_in_seconds;

    $num_records = $this->getRecordCount( " UNIX_TIMESTAMP(created_at) < {$oldest_time}" );

    if($num_records) {
      $query = "DELETE FROM {$this->table_name} WHERE UNIX_TIMESTAMP(created_at) < %d";
      $query = $wpdb->prepare( $query, $oldest_time );

      $wpdb->query($query);
    }

    return $num_records;
  }

  public function get_distinct_ip_count($where = '') {
    global $wpdb, $prli_link, $prli_utils;

    $where .= $this->get_exclude_where_clause( $where );
    $where = $prli_utils->prepend_and_or_where(' WHERE', $where);
    $query = 'SELECT COUNT(DISTINCT ip) FROM ' . $this->table_name . ' cl'. $where;

    return $wpdb->get_var($query);
  }

  // Pagination Methods
  public function getRecordCount($where = '') {
    global $wpdb, $prli_link, $prli_utils;
    $where .= $this->get_exclude_where_clause( $where );
    $where = $prli_utils->prepend_and_or_where(' WHERE', $where);
    $query = 'SELECT COUNT(*) FROM ' . $this->table_name . ' cl'. $where;

    return $wpdb->get_var($query);
  }

  public function getPageCount($p_size, $where = '') {
    return ceil((int)$this->getRecordCount($where) / (int)$p_size);
  }

  public function getPage($current_p, $p_size, $where = '', $order = '', $include_stats = false) {
    global $wpdb, $prli_link, $prli_utils;

    $end_index = $current_p * $p_size;
    $start_index = $end_index - $p_size;
    $where .= $this->get_exclude_where_clause( $where );
    $where = $prli_utils->prepend_and_or_where(' AND', $where);
    if($include_stats)
      $query = 'SELECT cl.*, (SELECT count(*) FROM '. $this->table_name .' cl2 WHERE cl2.ip = cl.ip) as ip_count, (SELECT count(*) FROM '. $this->table_name .' cl3 WHERE cl3.vuid = cl.vuid) as vuid_count, li.name as link_name FROM ' . $this->table_name . ' cl, ' . $prli_link->table_name . ' li WHERE li.id = cl.link_id' . $where . $order . ' LIMIT ' . $start_index . ',' . $p_size . ';';
    else
      $query = 'SELECT cl.*, li.name as link_name FROM ' . $this->table_name . ' cl, ' . $prli_link->table_name . ' li WHERE li.id = cl.link_id' . $where . $order . ' LIMIT ' . $start_index . ',' . $p_size . ';';
    $results = $wpdb->get_results($query);

    return $results;
  }

  public function generateUniqueVisitorId() {
    return uniqid();
  }

  public function get_counts_by_days($start_timestamp, $end_timestamp, $link_id = "all", $type = "all", $group = '') {
    global $wpdb, $prli_link;

    $search_where = '';
    $query = "SELECT DATE(cl.created_at) as cldate,COUNT(*) as clcount FROM ".$this->table_name." cl WHERE cl.created_at BETWEEN '".date("Y-n-j",$start_timestamp)." 00:00:00' AND '".date("Y-n-j",$end_timestamp)." 23:59:59'".$search_where.$this->get_exclude_where_clause( ' AND' );

    if($link_id != "all")
      $query .= " AND link_id=$link_id";

    if(!empty($group))
      $query .= " AND link_id IN (SELECT id FROM " . $prli_link->table_name . " WHERE group_id=$group)";

    if($type == "unique")
      $query .= " AND first_click=1";

    $query .= ' GROUP BY DATE(cl.created_at)';

    $clicks_array = $wpdb->get_results($query);

    $temp_array = array();
    $counts_array = array();
    $dates_array = array();

    // Refactor Array for use later on
    foreach($clicks_array as $c)
      $temp_array[$c->cldate] = $c->clcount;

    // Get the dates array
    for($c = $start_timestamp; $c <= $end_timestamp; $c += 60*60*24)
      $dates_array[] = date("Y-m-d",$c);

    // Make sure counts array is in order and includes zero click days
    foreach($dates_array as $date_str) {
      if(isset($temp_array[$date_str]))
        $counts_array[$date_str] = $temp_array[$date_str];
      else
        $counts_array[$date_str] = 0;
    }

    return $counts_array;
  }

  public function setupClickLineGraph($start_timestamp, $end_timestamp, $link_id = "all", $type = "all", $group = '', $title_only = false) {
    global $wpdb, $prli_utils, $prli_link;

    if($link_id == 'all') {
      $link_slug = __('all links', 'pretty-link');
    }
    else {
      $link_slug = "'/".$wpdb->get_var($wpdb->prepare("SELECT slug FROM {$prli_link->table_name} WHERE id = %d", $link_id))."'";
    }

    if($type == "all") {
      $type_string = __('All clicks', 'pretty-link');
    }
    else {
      $type_string = __('Unique hits', 'pretty-link');
    }

    if($title_only) {
      return __('Pretty Link:', 'pretty-link').' '.$type_string.' '.__('on', 'pretty-link').' '.$link_slug.' '.__('between', 'pretty-link').' '.date("Y-n-j", $start_timestamp).' '.__('and', 'pretty-link').' '.date("Y-n-j", $end_timestamp);
    }

    $dates_array = $this->get_counts_by_days($start_timestamp, $end_timestamp,$link_id, $type,$group);

    $chart_data = array('cols' => array(array("label" => __('Date', 'pretty-link'), 'type' => 'string'), array("label" => __('Clicks', 'pretty-link'), 'type' => 'number')));

    foreach($dates_array as $key => $value) {
      $chart_data['rows'][] = array('c' => array(array('v' => $key, 'f' => null), array('v' => (int)$value, 'f' => null)));
    }

    return json_encode($chart_data);
  }

  // Set defaults and grab get or post of each possible param
  public function get_params_array() {
    $values = array(
       'paged'  => (isset($_GET['paged'])?(int)$_GET['paged']:(isset($_POST['paged'])?(int)$_POST['paged']:1)),
       'l'      => (isset($_GET['l'])?(int)$_GET['l']:(isset($_POST['l'])?(int)$_POST['l']:'all')),
       'group'  => (isset($_GET['group'])?(int)$_GET['group']:(isset($_POST['group'])?(int)$_POST['group']:'')),
       'ip'     => sanitize_text_field(stripslashes(isset($_GET['ip'])?$_GET['ip']:(isset($_POST['ip'])?$_POST['ip']:''))),
       'vuid'   => sanitize_key(stripslashes(isset($_GET['vuid'])?$_GET['vuid']:(isset($_POST['vuid'])?$_POST['vuid']:''))),
       'sdate'  => sanitize_text_field(stripslashes(isset($_GET['sdate'])?$_GET['sdate']:(isset($_POST['sdate'])?$_POST['sdate']:''))),
       'edate'  => sanitize_text_field(stripslashes(isset($_GET['edate'])?$_GET['edate']:(isset($_POST['edate'])?$_POST['edate']:''))),
       'type'   => sanitize_text_field(stripslashes(isset($_GET['type'])?$_GET['type']:(isset($_POST['type'])?$_POST['type']:'all'))),
       'search' => sanitize_text_field(stripslashes(isset($_GET['search'])?$_GET['search']:(isset($_POST['search'])?$_POST['search']:''))),
       'sort'   => sanitize_key(stripslashes(isset($_GET['sort'])?$_GET['sort']:(isset($_POST['sort'])?$_POST['sort']:''))),
       'sdir'   => sanitize_key(stripslashes(isset($_GET['sdir'])?$_GET['sdir']:(isset($_POST['sdir'])?$_POST['sdir']:'')))
    );

    return $values;
  }

}
