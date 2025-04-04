<?php

/**
 * Uninstall hooks for plugin - called on uninstall.
 *
 * @method appip_deinstall
 * @see uninstall.php
 */
	function appip_deinstall() {
		//moved to uninstall.php as it should be.
	}

/**
 * Install hook for plugin - called on activation. Creates the plugin database
 * and sets default options.
 *
 * @method appip_install
 */
	function appip_install () {
		global $wpdb;
		$curappipver = get_option("apipp_version");
		$dbversion = get_option("apipp_dbversion");
		$appiptable = $wpdb->prefix . 'amazoncache';
        $charset_collate = $wpdb->get_charset_collate();
		if((int) get_option('apipp_amazon_cache_sec', '0' ) == 0 )
			add_option("apipp_amazon_cache_sec", 3600);
		if($curappipver == ''){
			$createSQL = "CREATE TABLE IF NOT EXISTS $appiptable (`Cache_id` int(10) NOT NULL auto_increment, `URL` text NOT NULL, `updated` datetime default NULL, `body` longtext, PRIMARY KEY (`Cache_id`), UNIQUE KEY `URL` (`URL`(255)), KEY `Updated` (`updated`)) $charset_collate;";
	      	$wpdb->query($createSQL);
			add_option("apipp_version", APIAP_PLUGIN_VER);
			add_option("apipp_dbversion", APIAP_DBASE_VER);
		}
		$checkTable = $wpdb->get_var("SHOW TABLES LIKE '{$appiptable}'");
		if($curappipver == '' || $checkTable != $appiptable){
			$createSQL = "CREATE TABLE IF NOT EXISTS $appiptable (`Cache_id` int(10) NOT NULL auto_increment, `URL` text NOT NULL, `updated` datetime default NULL, `body` longtext, PRIMARY KEY (`Cache_id`), UNIQUE KEY `URL` (`URL`(190)), KEY `Updated` (`updated`)) $charset_collate";
			$temp = $wpdb->query($createSQL);
			$checkTable = $wpdb->get_var("SHOW TABLES LIKE '{$appiptable}'");
			if($checkTable == ''){
				add_option("apipp_db_trouble", 'true');	// new in 5.0.0 to check if database is created or not.
			}
			add_option("apipp_version", APIAP_PLUGIN_VER);
			add_option("apipp_dbversion", APIAP_DBASE_VER);
		}elseif($dbversion != APIAP_DBASE_VER){
			$alterSQL = "ALTER TABLE `{$appiptable}` CHANGE `body` `body` LONGTEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;";
	      	$testif = $wpdb->query($alterSQL);
			update_option("apipp_version", APIAP_PLUGIN_VER);
			update_option("apipp_dbversion", APIAP_DBASE_VER);
		}
	}
