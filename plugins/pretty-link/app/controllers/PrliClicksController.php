<?php
if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); }

class PrliClicksController extends PrliBaseController {
  public $max_rows_per_file;

  public function __construct() {
    $this->max_rows_per_file = 5000;
  }

  public function load_hooks() {
    add_action('init', array($this,'route_scripts'));
    add_action('admin_init', array($this, 'auto_trim_clicks'));
  }

  public function route() {
    if(isset($_REQUEST['action']) and $_REQUEST['action'] == 'csv') {
      $this->csv();
    } else {
      $this->admin_page();
    }
  }

  public function route_scripts() {
    if( isset($_GET['action']) && $_GET['action'] == 'prli_download_csv_hit_report' ) {
      $this->click_report();
      exit;
    }
  }

  public function auto_trim_clicks() {
    global $prli_options, $prli_click;

    if($prli_options->auto_trim_clicks) {
      $last_run = get_option('prli_auto_trim_clicks_last_run', 0);
      $now      = time();

      //Run once per day at most
      if(($now - $last_run) > 86400) {
        $prli_click->clear_clicks_by_age_in_days(90);
        update_option('prli_auto_trim_clicks_last_run', time());
        wp_cache_delete('alloptions', 'options');
      }
    }
  }

  public function admin_page() {
    global $wpdb, $prli_options, $prli_click, $prli_link, $prli_utils, $page_size;

    $page_params = '';

    $params = $prli_click->get_params_array();
    $page_size = (isset($_REQUEST['size']) && is_numeric($_REQUEST['size']) && !empty($_REQUEST['size']))?(int)$_REQUEST['size']:10;
    $current_page = $params['paged'];

    $start_timestamp = $prli_utils->get_start_date($params);
    $end_timestamp = $prli_utils->get_end_date($params);

    $start_timestamp = mktime(0, 0, 0, date('n', $start_timestamp), date('j', $start_timestamp), date('Y', $start_timestamp));
    $end_timestamp   = mktime(0, 0, 0, date('n', $end_timestamp),   date('j', $end_timestamp),   date('Y', $end_timestamp)  );

    $sdyear = date('Y',$start_timestamp);
    $sdmon  = date('n',$start_timestamp);
    $sddom  = date('j',$start_timestamp);

    $edyear = date('Y',$end_timestamp);
    $edmon  = date('n',$end_timestamp);
    $eddom  = date('j',$end_timestamp);

    $where_clause = $wpdb->prepare(
      " cl.created_at BETWEEN '%d-%d-%d 00:00:00' AND '%d-%d-%d 23:59:59'",
      $sdyear,$sdmon,$sddom,$edyear,$edmon,$eddom );

    if(!empty($params['sdate']) and preg_match('/^\d\d\d\d-\d\d-\d\d$/', $params['sdate'])) {
      $page_params .= "&sdate={$params['sdate']}";
    }

    if(!empty($params['edate']) and preg_match('/^\d\d\d\d-\d\d-\d\d$/', $params['edate'])) {
      $page_params .= "&edate={$params['edate']}";
    }

    if(!empty($params['l']) and $params['l'] != 'all') {
      $where_clause .= (($params['l'] != 'all') ? $wpdb->prepare(' AND cl.link_id=%d', $params['l']):'');
      $link_name = $wpdb->get_var($wpdb->prepare("SELECT name FROM {$wpdb->prefix}prli_links WHERE id=%d", $params['l']));
      $link_slug = $wpdb->get_var($wpdb->prepare("SELECT slug FROM {$wpdb->prefix}prli_links WHERE id=%d", $params['l']));

      $page_params .= "&l={$params['l']}";
    }
    else if(!empty($params['ip'])) {
      $link_name = __('IP Address: ', 'pretty-link') . esc_html($params['ip']);
      $where_clause .= $wpdb->prepare(" AND cl.ip=%s", $params['ip']);
      $page_params .= "&ip={$params['ip']}";
    }
    else if(!empty($params['vuid'])) {
      $link_name = __('Visitor: ', 'pretty-link') . esc_html($params['vuid']);
      $where_clause .= $wpdb->prepare(" AND cl.vuid=%s",$params['vuid']);
      $page_params .= "&vuid={$params['vuid']}";
    }
    else {
      $link_name = __('All Links', 'pretty-link');
      $where_clause .= "";
      $page_params .= "";
    }

    if($params['type'] == 'unique') {
      $where_clause .= ' AND first_click=1';
      $page_params .= '&type=unique';
    }

    $click_vars = PrliClicksHelper::get_click_sort_vars($params,$where_clause);
    $sort_params = $page_params . $click_vars['sort_params'];
    $page_params .= $click_vars['page_params'];
    $sort_str = $click_vars['sort_str'];
    $sdir_str = $click_vars['sdir_str'];
    $search_str = $click_vars['search_str'];

    $where_clause = $click_vars['where_clause'];
    $order_by = $click_vars['order_by'];
    $count_where_clause = $click_vars['count_where_clause'];

    $record_count = $prli_click->getRecordCount($count_where_clause);
    $page_count = $prli_click->getPageCount($page_size,$count_where_clause);
    $clicks = $prli_click->getPage($current_page,$page_size,$where_clause,$order_by,true);
    $page_last_record = $prli_utils->getLastRecordNum($record_count,$current_page,$page_size);
    $page_first_record = $prli_utils->getFirstRecordNum($record_count,$current_page,$page_size);

    require_once(PRLI_VIEWS_PATH.'/clicks/list.php');
  }

  public function click_report() {
    global $wpdb, $prli_click, $prli_link;

    if(isset($_GET['l'])) {
      $l = (int) $_GET['l'];
      $where_clause = $wpdb->prepare(" link_id=%d",$l );
      $link_name = $wpdb->get_var($wpdb->prepare("SELECT name FROM {$wpdb->prefix}prli_links WHERE id=%d",$l));
      $link_slug = $wpdb->get_var($wpdb->prepare("SELECT slug FROM {$wpdb->prefix}prli_links WHERE id=%d",$l));
    }
    else if(isset($_GET['ip'])) {
      $ip = stripslashes($_GET['ip']);
      if (filter_var($ip, FILTER_VALIDATE_IP)) {
        $ip = PrliClicksHelper::esc_spreadsheet_cell($ip);
        $link_name = "ip_addr_{$ip}";
        $where_clause = $wpdb->prepare(" cl.ip=%s", $ip);
      }
    }
    else if(isset($_GET['vuid'])) {
      $vuid = sanitize_key(stripslashes($_GET['vuid']));
      $link_name = "visitor_{$vuid}";
      $where_clause = $wpdb->prepare(" cl.vuid=%s",$vuid);
    }
    else {
      $link_name = "all_links";
      $where_clause = "";
    }

    $link_name = stripslashes($link_name);
    $link_name = preg_replace('#[ ,]#','',$link_name);

    $record_count = $prli_click->getRecordCount($where_clause);
    $page_count   = (int)ceil($record_count / $this->max_rows_per_file);
    $prli_page = isset($_GET['prli_page']) ? (int) $_GET['prli_page'] : 1;
    $hmin = 0;

    if($prli_page) {
      $hmin = ($prli_page - 1) * $this->max_rows_per_file;
    }

    if($prli_page==$page_count) {
      $hmax = $record_count;
    }
    else {
      $hmax = ($prli_page * $this->max_rows_per_file) - 1;
    }

    $hlimit = "{$hmin},{$this->max_rows_per_file}";
    $clicks = $prli_click->getAll($where_clause,'',false,$hlimit);

    require_once PRLI_VIEWS_PATH . '/clicks/csv.php';
  }

  public function csv() {
    global $wpdb, $prli_blogurl, $prli_link, $prli_click, $prli_group;

    $param_string = $where_clause = '';

    if(isset($_GET['l'])) {
      $l = (int) $_GET['l'];
      $where_clause = $wpdb->prepare(' link_id=%d', $l);
      $link_name = $wpdb->get_var($wpdb->prepare("SELECT name FROM {$wpdb->prefix}prli_links WHERE id=%d", $l));
      $link_slug = $wpdb->get_var($wpdb->prepare("SELECT slug FROM {$wpdb->prefix}prli_links WHERE id=%d", $l));
      $param_string .= "l={$l}";
    }
    else if(isset($_GET['ip'])) {
      $ip = stripslashes($_GET['ip']);
      if (filter_var($ip, FILTER_VALIDATE_IP)) {
        $ip = PrliClicksHelper::esc_spreadsheet_cell($_GET['ip']);
        $link_name = "ip_addr_{$ip}";
        $where_clause = $wpdb->prepare(' cl.ip=%s', $ip);
        $param_string .= "ip={$ip}";
      }
    }
    else if(isset($_GET['vuid'])) {
      $vuid = sanitize_key(stripslashes($_GET['vuid']));
      $link_name = "visitor_{$vuid}";
      $where_clause = $wpdb->prepare(' cl.vuid=%s', $vuid);
      $param_string .= "vuid={$vuid}";
    }
    else {
      $link_name = 'all_links';
    }

    $hit_record_count = $prli_click->getRecordCount($where_clause);
    $hit_page_count   = (int)ceil($hit_record_count / $this->max_rows_per_file);

    $param_string   = (empty($param_string)?'':"&{$param_string}");
    $hit_report_url = "{$prli_blogurl}/index.php?action=prli_download_csv_hit_report{$param_string}";

    $max_rows_per_file = $this->max_rows_per_file;

    require_once PRLI_VIEWS_PATH . '/clicks/csv_download.php';
  }
}
