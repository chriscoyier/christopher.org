<?php
class Amazon_Product_Add_Cron_Support {
  function __construct() {
    add_filter( 'cron_schedules', array( $this, 'add_cron_interval' ) );
  }

  function add_cron_interval( $schedules ) {
	if(isset($schedules[ 'fifteen_minutes' ]))
		return $schedules;
    $schedules[ 'fifteen_minutes' ] = array(
      'interval' => 900,
      'display' => esc_html__( 'Every 15 Minutes.' ),
    );
    return $schedules;
  }
}
new Amazon_Product_Add_Cron_Support();