<?php

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

global $plp_update;

if($plp_update->is_installed_and_activated()) {
  require PRLI_VIEWS_PATH . '/admin/onboarding/parts/category-pro.php';
} else {
  require PRLI_VIEWS_PATH . '/admin/onboarding/parts/category-lite.php';
}

?>