<?php

use Prli\GroundLevel\Mothership\AbstractPluginConnection;

/**
 * Provides an interface for connecting the plugin to Mothership packages data
 */
class PrliMothershipPluginConnector extends AbstractPluginConnection
{
    private $plp_update = null;

    /**
     * Constructor.
     */
    public function __construct()
    {
        global $plp_update;
        $this->plp_update = $plp_update;
        $this->pluginId     = 'prettylink';
        $this->pluginPrefix = 'PRLI_';
    }

    /**
     * Gets the license activation status option.
     *
     * @return boolean The license activation status.
     */
    public function getLicenseActivationStatus(): bool
    {
        return $this->plp_update->is_activated();
    }

    /**
     * Updates the license activation status option.
     *
     * @param boolean $status The status to update.
     */
    public function updateLicenseActivationStatus(bool $status): void
    {
        update_option('prli_activated', $status);
    }

    /**
     * Gets the license key option.
     *
     * @return string The license key.
     */
    public function getLicenseKey(): string
    {
        return $this->plp_update->mothership_license;
    }

    /**
     * Updates the license key option.
     *
     * @param string $licenseKey The license key to update.
     */
    public function updateLicenseKey(string $licenseKey): void
    {
        $this->plp_update->set_mothership_license($licenseKey);
    }

    /**
     * Gets the domain option.
     *
     * @return string The domain.
     */
    public function getDomain(): string
    {
        return PrliUtils::site_domain();
    }
}