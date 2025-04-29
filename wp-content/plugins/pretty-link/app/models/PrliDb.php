<?php
if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); }

class PrliDb {
  public $groups, $clicks, $links, $linkmeta;

  public function __construct() {
    global $wpdb;

    $this->groups   = "{$wpdb->prefix}prli_groups";
    $this->clicks   = "{$wpdb->prefix}prli_clicks";
    $this->links    = "{$wpdb->prefix}prli_links";
    $this->linkmeta = "{$wpdb->prefix}prli_link_metas";
  }

  public function should_install() {
    global $prli_db_version;
    $old_db_version = get_option('prli_db_version');

    if($prli_db_version != $old_db_version) { return true; }

    return false;
  }

  /********* INSTALL PLUGIN ***********/
  public function prli_install() {
    global $wpdb, $prli_utils, $plp_update, $prli_db_version;

    $wpdb->query('SET SQL_BIG_SELECTS=1'); //This may be getting set back to 0 when SET MAX_JOIN_SIZE is executed
    $wpdb->query('SET MAX_JOIN_SIZE=18446744073709551615');
    //$wpdb->query('SET GLOBAL innodb_large_prefix=1'); //Will fail on some installs without proper privileges still

    // This was introduced in WordPress 3.5
    // $char_col = $wpdb->get_charset_collate(); //This doesn't work for most non english setups
    $char_col = "";
    $collation = $wpdb->get_row("SHOW FULL COLUMNS FROM {$wpdb->posts} WHERE field = 'post_content'");

    if(isset($collation->Collation)) {
      $charset = explode('_', $collation->Collation);

      if(is_array($charset) && count($charset) > 1) {
        $charset = $charset[0]; //Get the charset from the collation
        $char_col = "DEFAULT CHARACTER SET {$charset} COLLATE {$collation->Collation}";
      }
    }

    //Fine we'll try it your way this time
    if(empty($char_col)) { $char_col = $wpdb->get_charset_collate(); }

    $prli_utils->migrate_before_db_upgrade();

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    /* Create/Upgrade Clicks (Clicks) Table */
    $sql = "CREATE TABLE {$this->clicks} (
              id int(11) NOT NULL auto_increment,
              ip varchar(255) default NULL,
              browser varchar(255) default NULL,
              btype varchar(255) default NULL,
              bversion varchar(255) default NULL,
              os varchar(255) default NULL,
              referer varchar(255) default NULL,
              host varchar(255) default NULL,
              uri varchar(255) default NULL,
              robot tinyint default 0,
              first_click tinyint default 0,
              created_at datetime NOT NULL,
              link_id int(11) default NULL,
              vuid varchar(25) default NULL,
              PRIMARY KEY  (id),
              KEY link_id (link_id),
              KEY ip (ip(191)),
              KEY browser (browser(191)),
              KEY btype (btype(191)),
              KEY bversion (bversion(191)),
              KEY os (os(191)),
              KEY referer (referer(191)),
              KEY host (host(191)),
              KEY uri (uri(191)),
              KEY robot (robot),
              KEY first_click (first_click),
              KEY vuid (vuid)".
              // We won't worry about this constraint for now.
              //CONSTRAINT ".$clicks_table."_ibfk_1 FOREIGN KEY (link_id) REFERENCES $pretty_links_table (id)
            ") {$char_col};";

    dbDelta($sql);

    /* Create/Upgrade Pretty Links Table */
    $sql = "CREATE TABLE {$this->links} (
              id int(11) NOT NULL auto_increment,
              name varchar(255) default NULL,
              description text default NULL,
              url text default NULL,
              slug varchar(255) default NULL,
              nofollow tinyint(1) default 0,
              sponsored tinyint(1) default 0,
              track_me tinyint(1) default 1,
              param_forwarding varchar(255) default NULL,
              param_struct varchar(255) default NULL,
              redirect_type varchar(255) default '307',
              link_status varchar(64) default 'enabled',
              created_at datetime NOT NULL,
              updated_at datetime default NULL,
              group_id int(11) default NULL,
              link_cpt_id int(11) default 0,
              prettypay_link tinyint(1) default 0,
              PRIMARY KEY  (id),
              KEY link_cpt_id (link_cpt_id),
              KEY prettypay_link (prettypay_link),
              KEY group_id (group_id),
              KEY link_status (link_status),
              KEY nofollow (nofollow),
              KEY sponsored (sponsored),
              KEY track_me (track_me),
              KEY param_forwarding (param_forwarding(191)),
              KEY redirect_type (redirect_type(191)),
              KEY slug (slug(191)),
              KEY created_at (created_at),
              KEY updated_at (updated_at)
            ) {$char_col};";

    dbDelta($sql);

    $sql = "CREATE TABLE {$this->linkmeta} (
              id int(11) NOT NULL auto_increment,
              meta_key varchar(255) default NULL,
              meta_value longtext default NULL,
              meta_order int(4) default 0,
              link_id int(11) NOT NULL,
              created_at datetime NOT NULL,
              PRIMARY KEY  (id),
              KEY meta_key (meta_key(191)),
              KEY link_id (link_id)
            ) {$char_col};";

    dbDelta($sql);

    $prli_utils->migrate_after_db_upgrade();

    // If there are any post metas with a post_id of 0 get rid of them...
    $prli_utils->clear_unknown_post_metas();

    /***** SAVE OPTIONS *****/
    $prli_options = PrliOptions::get_options();
    $prli_options->store();

    /***** SAVE DB VERSION *****/
    update_option('prli_db_version', $prli_db_version);
    wp_cache_delete('alloptions', 'options');
  }

  public function table_exists($table) {
    global $wpdb;
    $q = $wpdb->prepare('SHOW TABLES LIKE %s', $table);
    $table_res = $wpdb->get_var($q);

    return is_null($table_res) ? false : (strtolower($table_res) == strtolower($table));
  }
}
