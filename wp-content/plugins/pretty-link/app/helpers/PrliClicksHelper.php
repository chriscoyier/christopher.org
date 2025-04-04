<?php
if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); }

class PrliClicksHelper {
  public static function get_click_sort_vars($params,$where_clause = '') {
    global $wpdb;

    $count_where_clause = $where_clause;
    $page_params = '';
    $order_by = '';

    // These will have to work with both get and post
    $sort_str   = $params['sort'];
    $sdir_str   = $params['sdir'];
    $search_str = $params['search'];

    // Insert search string
    if(!empty($search_str)) {
      $search_params = explode(' ', $search_str);

      $first_pass = true;
      foreach($search_params as $search_param) {
        if($first_pass) {
          if($where_clause != '') {
            $where_clause .= ' AND';
            $count_where_clause .= ' AND';
          }

          $first_pass = false;
        }
        else {
          $where_clause .= ' AND';
          $count_where_clause .= ' AND';
        }

        $search_param = $sp = '%' . $wpdb->esc_like($search_param) . '%';

        $where_clause .= $wpdb->prepare(
          ' ( cl.ip LIKE %s OR
              cl.vuid LIKE %s OR
              cl.btype LIKE %s OR
              cl.bversion LIKE %s OR
              cl.host LIKE %s OR
              cl.referer LIKE %s OR
              cl.uri LIKE %s OR
              cl.created_at LIKE %s',
          $sp, $sp, $sp, $sp, $sp, $sp, $sp, $sp );

        $count_where_clause .= $wpdb->prepare(
          ' ( cl.ip LIKE %s OR
              cl.vuid LIKE %s OR
              cl.btype LIKE %s OR
              cl.bversion LIKE %s OR
              cl.host LIKE %s OR
              cl.referer LIKE %s OR
              cl.uri LIKE %s OR
              cl.created_at LIKE %s',
          $sp, $sp, $sp, $sp, $sp, $sp, $sp, $sp );

        $count_where_clause .= ' )';
        $where_clause .= $wpdb->prepare( ' OR li.name LIKE %s )', $sp );
      }

      $page_params .= "&search=" . urlencode($search_str);
    }

    // Have to create a separate var so sorting doesn't get screwed up
    $sort_params = $page_params;

    // make sure page params stay correct
    if(!empty($sort_str)) { $page_params .="&sort={$sort_str}"; }

    if(!empty($sdir_str)) { $page_params .= "&sdir={$sdir_str}"; }

    if(empty($count_where_clause)) { $count_where_clause = $where_clause; }

    // Add order by clause
    switch($sort_str) {
      case 'ip':
      case 'vuid':
      case 'btype':
      case 'bversion':
      case 'host':
      case 'referer':
      case 'uri':
        $order_by .= " ORDER BY cl.{$sort_str}";
        break;
      case 'link':
        $order_by .= ' ORDER BY li.name';
        break;
      default:
        $order_by .= ' ORDER BY cl.created_at';
    }

    // Toggle ascending / descending
    if((empty($sort_str) && empty($sdir_str)) || $sdir_str == 'desc') {
      $order_by .= ' DESC';
      $sdir_str = 'desc';
    }
    else {
      $sdir_str = 'asc';
    }

    return compact( 'count_where_clause', 'sort_str', 'sdir_str', 'search_str',
                    'where_clause', 'order_by', 'sort_params', 'page_params' );
  }

  /**
   * Remove nefarious characters from a cell value
   *
   * @param  string $value Cell value
   *
   * @return string
   */
  public static function esc_spreadsheet_cell( $value ) {
    return preg_replace( '/^[-=+@]*/', '', $value );
  }
}

