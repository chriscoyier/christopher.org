<?php
/**
 * @license GPL-3.0
 *
 * Modified by Team Caseproof using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace Prli\Caseproof\GrowthTools;

use Prli\Caseproof\GrowthTools\Helper\AddonHelper;

/**
 * Main plugin application.
 *
 * @see \Prli\Caseproof\GrowthTools\instance() Instead of instantiating this class directly,
 *                                       retrieve the main instance using this function.
 */
class App
{
    /**
     * Configuration for the App.
     *
     * @var Config
     */
    protected Config $config;

    /**
     * Constructor.
     *
     * @param Config $config Config object.
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->addHooks();
    }

    /**
     * Registers WordPress hooks necessary to bootstrap the plugin.
     */
    public function addHooks(): void
    {
        add_action('admin_menu', [$this, 'addMenu'], 9999);
        add_action('wp_ajax_caseproof_growth_tool_plugin_action_' . $this->config->instanceId, [$this, 'pluginAction']);
    }

    /**
     * Add menu into WordPress admin.
     */
    public function addMenu()
    {
        add_submenu_page(
            $this->config->parentMenuSlug ?? 'tools.php',
            __('Growth Tools', 'cspf-growth-tools'),
            __('Growth Tools', 'cspf-growth-tools'),
            'install_plugins',
            $this->config->menuSlug ?? 'growth-tools',
            [$this, 'renderPage']
        );
    }

    /**
     * Add inline CSS.
     *
     * @param string $inlineCSS CSS styles as string.
     */
    protected function addInlineCSS(string $inlineCSS)
    {
        wp_add_inline_style('caseproof_grtl-growth-tools-style', $inlineCSS);
    }

    /**
     * Render html page.
     *
     * @return void
     */
    public function renderPage()
    {
        wp_enqueue_script('caseproof_grtl-growth-tools-script', $this->config->assetsUrl . '/main.min.js', []);
        wp_enqueue_style('caseproof_grtl-growth-tools-style', $this->config->assetsUrl . '/main.min.css', []);
        $inlineCSS = $this->config->customInlineCSS;
        $this->addInlineCSS(is_callable($inlineCSS) ? $inlineCSS() : $inlineCSS);
        $growthToolsData = $this->config->getPluginsConfig();
        $pluginsStatus = $this->config->getPluginsStatus();

        $labels = [
            'notinstalled' => esc_html(__('Not Installed', 'cspf-growth-tools')),
            'installed' => esc_html(__('Installed', 'cspf-growth-tools')),
            'activated' => esc_html(__('Active', 'cspf-growth-tools')),
            'active' => esc_html(__('Activate', 'cspf-growth-tools')),
            'deactive' => esc_html(__('Deactivate', 'cspf-growth-tools')),
            'install' => esc_html(__('Install', 'cspf-growth-tools')),
        ];
        $ajaxAction = 'caseproof_growth_tool_plugin_action_' . $this->config->instanceId;
        $baseLogoUrl = $this->config->imageBaseUrl;
        $buttonCSS = $this->config->buttonCSSClasses;
        $headerHTML = $this->config->headerHtmlCallback;

        require "views/list.phtml";
    }

    /**
     * Ajax handler for install/activate plugin
     */
    public function pluginAction()
    {
        $growth_tools_data = $this->config->getPluginsConfig();
        if (empty($growth_tools_data)) {
            return;
        }

        $type = sanitize_text_field($_REQUEST['type']);
        $pluginMain = sanitize_text_field($_REQUEST['plugin']);

        if ($type === 'install') {
            foreach ($growth_tools_data['plugins'] as $plugin) {
                if ($plugin['main']['free'] === $pluginMain) {
                    $this->installAddon($plugin['download_url']);
                }
            }
        } elseif ($type === 'activate') {
            $this->activateAddon($pluginMain);
        } elseif ($type === 'deactivate') {
            $this->deactivateAddon($pluginMain);
        }
    }

    /**
     * Install plugin.
     *
     * @param string $link Download link.
     */
    protected function installAddon(string $link)
    {
        AddonHelper::installAddon($link);
    }

    /**
     * Activate plugin.
     *
     * @param string $file Name of the plugin.
     */
    protected function activateAddon(string $file)
    {
        AddonHelper::activateAddon($file);
    }

    /**
     * Deactivate plugin.
     *
     * @param string $file Name of the plugin.
     */
    protected function deactivateAddon(string $file)
    {
        AddonHelper::deactivateAddon($file);
    }

  /**
   * Render header contents.
   *
   * @return string
   */
    public static function getHeaderHtml(): string
    {
        return '<h1 class="wp-heading-inline">' . esc_html__('Growth Tools', 'cspf-growth-tools') . '</h1>';
    }
}
