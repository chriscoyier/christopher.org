<?php

/**
 * This file is part of mundschenk-at/wp-data-storage.
 *
 * Copyright 2017-2024 Peter Putzer.
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
 * Implements an interface to the WordPress Transients API.
 *
 * @since 1.0.0
 * @since 2.8.0 Obsolete method `maybe_fix_object` removed.
 *
 * @author Peter Putzer <github@mundschenk.at>
 */
class Transients extends Abstract_Cache
{
    /**
     * The transient prefix.
     *
     * @var string
     */
    const TRANSIENT_SQL_PREFIX = '_transient_';
    /**
     * The incrementor transient key.
     *
     * @var string
     */
    protected string $incrementor_key;
    /**
     * Create new cache instance.
     *
     * @param string $prefix The prefix automatically added to transient names.
     */
    public function __construct(string $prefix)
    {
        $this->incrementor_key = $prefix . 'transients_incrementor';
        $incrementor = $this->get($this->incrementor_key, \true);
        $incrementor = \is_int($incrementor) ? $incrementor : 0;
        $this->incrementor = $incrementor;
        parent::__construct($prefix);
    }
    /**
     * Invalidate all cached elements by resetting the incrementor.
     */
    public function invalidate(): void
    {
        if (!\wp_using_ext_object_cache()) {
            // Clean up old transients.
            foreach ($this->get_keys_from_database() as $old_transient) {
                $this->delete($old_transient, \true);
            }
        }
        // Update incrementor.
        $this->incrementor = time();
        $this->set($this->incrementor_key, $this->incrementor, 0, \true);
    }
    /**
     * Retrieves a list of transients set by the plugin from the options table.
     *
     * @return string[]
     */
    public function get_keys_from_database(): array
    {
        /**
         * WordPress database handler.
         *
         * @var \wpdb
         */
        global $wpdb;
        $results = $wpdb->get_results(
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
            $wpdb->prepare('SELECT option_name FROM %i WHERE option_name like %s', $wpdb->options, static::TRANSIENT_SQL_PREFIX . "{$this->get_prefix()}%"),
            \ARRAY_A
        ) ?? [];
        /**
         * Retrieve the list of transients.
         *
         * @var string[] $option_names
         */
        $option_names = \wp_list_pluck($results, 'option_name');
        return \str_replace(static::TRANSIENT_SQL_PREFIX, '', $option_names);
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
        return \get_transient($raw ? $key : $this->get_key($key));
    }
    /**
     * Retrieves a cached large object.
     *
     * @since 2.0.0 Parameter `$allowed_classes` added.
     *
     * @param string         $key             The cache key.
     * @param class-string[] $allowed_classes An array of allowed class names.
     *
     * @return mixed
     */
    public function get_large_object(string $key, array $allowed_classes)
    {
        $encoded = $this->get($key);
        if (!\is_string($encoded)) {
            return \false;
        }
        $uncompressed = @\gzdecode(\base64_decode($encoded));
        // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
        if (\false === $uncompressed) {
            return \false;
        }
        $maybe_object = @\unserialize($uncompressed, ['allowed_classes' => $allowed_classes]);
        // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_unserialize
        if (!\is_object($maybe_object) || \__PHP_Incomplete_Class::class === \get_class($maybe_object)) {
            return \false;
        }
        return $maybe_object;
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
        return \set_transient($raw ? $key : $this->get_key($key), $value, $duration);
    }
    /**
     * Sets a transient for a large PHP object. The object will be stored in
     * serialized and gzip encoded form using Base64 encoding to ensure binary safety.
     *
     * @param string $key       The cache key.
     * @param mixed  $value     The value to store.
     * @param int    $duration  Optional. The duration in seconds. Default 0 (no expiration).
     *
     * @return bool True if the cache could be set successfully.
     */
    public function set_large_object(string $key, $value, int $duration = 0): bool
    {
        $compressed = \gzencode(\serialize($value));
        // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize
        if (\false === $compressed) {
            return \false;
            // @codeCoverageIgnore
        }
        // base64_encode() is used to safely store the gzipped serialized object
        // in the WordPress database.
        return $this->set($key, \base64_encode($compressed), $duration);
        // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
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
        return \delete_transient($raw ? $key : $this->get_key($key));
    }
}
