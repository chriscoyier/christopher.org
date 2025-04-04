<?php
if(!defined('ABSPATH'))
  die('You are not allowed to call this page directly.');

  if(is_user_logged_in() and current_user_can('level_10'))
  {

    $filename = date("ymdHis",time()) . '_' . $link_name . '_pretty_link_clicks_' . $hmin . '-' . $hmax . '.csv';
    header("Content-Type: text/x-csv");
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header("Expires: ".gmdate("D, d M Y H:i:s", mktime(date("H")+2, date("i"), date("s"), date("m"), date("d"), date("Y")))." GMT");
    header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
    header("Cache-Control: no-cache, must-revalidate");
    header("Pragma: no-cache");

    echo '"Browser","Browser Version","Platform","IP","Visitor ID","Timestamp","Host","URI","Referrer","Link"' . "\n";
    foreach($clicks as $click)
    {

      $link = $prli_link->getOne($click->link_id);

      $click->btype = sanitize_text_field( PrliClicksHelper::esc_spreadsheet_cell( $click->btype ) );
      $click->bversion = sanitize_text_field( PrliClicksHelper::esc_spreadsheet_cell( $click->bversion ) );
      $click->os = sanitize_text_field( PrliClicksHelper::esc_spreadsheet_cell( $click->os ) );
      $click->ip = sanitize_text_field( PrliClicksHelper::esc_spreadsheet_cell( $click->ip ) );
      $click->vuid = sanitize_text_field( PrliClicksHelper::esc_spreadsheet_cell( $click->vuid ) );
      $click->created_at = sanitize_text_field( PrliClicksHelper::esc_spreadsheet_cell( $click->created_at ) );
      $click->host = sanitize_text_field( PrliClicksHelper::esc_spreadsheet_cell( $click->host ) );
      $click->uri = sanitize_text_field( PrliClicksHelper::esc_spreadsheet_cell( $click->uri ) );
      $click->referer = sanitize_text_field( PrliClicksHelper::esc_spreadsheet_cell( $click->referer ) );

      echo "\"$click->btype\",\"$click->bversion\",\"$click->os\",\"$click->ip\",\"$click->vuid\",\"$click->created_at\",\"$click->host\",\"$click->uri\",\"$click->referer\",\"" . ((empty($link->name))?$link->slug:$link->name) . "\"\n";
    }
  }
  else
    header("Location: " . $prli_blogurl);
