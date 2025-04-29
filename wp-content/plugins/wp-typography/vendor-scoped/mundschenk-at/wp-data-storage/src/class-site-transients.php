<?php

/**
 * This file is part of mundschenk-at/wp-data-storage.
 *
 * Copyright 2018-2024 Peter Putzer.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or ( at your option ) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * @package mundschenk-at/wp-data-storage/tests
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */
namespace WP_Typography\Vendor\Mundschenk\Data_Storage;

/**
 * Implements an interface to the WordPress Site Transients API.
 *
 * @since 1.0.0
 *
 * @author Peter Putzer <github@mundschenk.at>
 */
class Site_Transients extends Transients
{
    const TRANSIENT_SQL_PREFIX = '_site_transient_';
    /**
     * Retrieves a list of transients set by the plugin from the options table.
     *
     * @return string[]
     */
    public function get_keys_from_database(): array
    {
        // If we are not running on multisite, fall back to the parent implementation.
        if (!is_multisite()) {
            return parent::get_keys_from_database();
        }
        /**
         * WordPress database handler.
         *
         * @var \wpdb
         */
        global $wpdb;
        $results = $wpdb->get_results(
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
            $wpdb->prepare('SELECT meta_key FROM %i WHERE meta_key like %s and site_id = %d', $wpdb->sitemeta, self::TRANSIENT_SQL_PREFIX . "{$this->get_prefix()}%", \get_current_network_id()),
            \ARRAY_A
        ) ?? [];
        /**
         * Retrieve the list of transients.
         *
         * @var string[] $meta_keys
         */
        $meta_keys = \wp_list_pluck($results, 'meta_key');
        return \str_replace(self::TRANSIENT_SQL_PREFIX, '', $meta_keys);
    }
    /**
     * Retrieves a cached value.
     *
     * @param string $key The cache key.
     * @param bool   $raw Optional. Use the raw key name (i.e. don't call get_key). Default false.
     *
     * @return mixed
     */
    public function get(string $key, bool $raw = \false)
    {
        return \get_site_transient($raw ? $key : $this->get_key($key));
    }
    /**
     * Sets an entry in the cache and stores the key.
     *
     * @param string $key       The cache key.
     * @param mixed  $value     The value to store.
     * @param int    $duration  Optional. The duration in seconds. Default 0 (no expiration).
     * @param bool   $raw       Optional. Use the raw key name (i.e. don't call get_key). Default false.
     *
     * @return bool True if the cache could be set successfully.
     */
    public function set(string $key, $value, int $duration = 0, bool $raw = \false): bool
    {
        return \set_site_transient($raw ? $key : $this->get_key($key), $value, $duration);
    }
    /**
     * Deletes an entry from the cache.
     *
     * @param string $key The cache key root.
     * @param bool   $raw Optional. Use the raw key name (i.e. don't call get_key). Default false.
     *
     * @return bool True on successful removal, false on failure.
     */
    public function delete(string $key, bool $raw = \false): bool
    {
        return \delete_site_transient($raw ? $key : $this->get_key($key));
    }
}
