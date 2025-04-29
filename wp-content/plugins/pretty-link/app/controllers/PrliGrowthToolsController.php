<?php

class PrliGrowthToolsController {
  public function load_hooks()
  {
    if (version_compare(phpversion(), '7.4', '>=') && class_exists('\Prli\Caseproof\GrowthTools\App')) {
      add_action('admin_enqueue_scripts', function () {
        $screen = get_current_screen();
        if ($screen->id == 'pretty-link_page_pretty-link-growth-tools') {
          wp_enqueue_style('prli-admin-global', PRLI_CSS_URL . '/admin_global.css', array(), PRLI_VERSION);
        }
      });
      $config = new \Prli\Caseproof\GrowthTools\Config([
        'parentMenuSlug' => 'edit.php?post_type=' . PrliLink::$cpt,
        'instanceId'     => 'pretty-link',
        'menuSlug'     => 'pretty-link-growth-tools',
        'buttonCSSClasses' => ['pretty-link-cta-button', 'pretty-link-cta-gt-button'],
      ]);
      new \Prli\Caseproof\GrowthTools\App($config);
    }
  }
}
