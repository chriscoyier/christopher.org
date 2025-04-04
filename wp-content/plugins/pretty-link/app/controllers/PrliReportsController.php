<?php
if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); }

class PrliReportsController extends PrliBaseController {
  public function load_hooks() {
    // Nothing yet
  }

  public static function chart_data() {
    global $prli_siteurl, $prli_click, $prli_utils;

    $params = $prli_click->get_params_array();
    $first_click = $prli_utils->getFirstClickDate();

    // Adjust for the first click
    if(isset($first_click)) {
      $min_date = (int)((time()-$first_click)/60/60/24);

      if($min_date < 30)
        $start_timestamp = $prli_utils->get_start_date($params,$min_date);
      else
        $start_timestamp = $prli_utils->get_start_date($params,30);

      $end_timestamp = $prli_utils->get_end_date($params);
    }
    else {
      $min_date = 0;
      $start_timestamp = time();
      $end_timestamp = time();
    }

    $link_id = $params['l'];
    $type = $params['type'];
    $group = '';
    $show_chart = (!isset($_GET['ip']) and !isset($_GET['vuid']));

    return array(
      'show_chart' => $show_chart,
      'min_date' => $min_date * -1,
      'chart' => $prli_click->setupClickLineGraph($start_timestamp, $end_timestamp, $link_id, $type, $group),
      'titles' => $prli_click->setupClickLineGraph($start_timestamp, $end_timestamp, $link_id, $type, $group, true)
    );
  }
}

