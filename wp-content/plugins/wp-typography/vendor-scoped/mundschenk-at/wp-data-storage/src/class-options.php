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
 * Implements an interface to the WordPress Options API.
 *
 * @since 1.0.0
 *
 * @author Peter Putzer <github@mundschenk.at>
 */
class Options
{
    /**
     * The prefix added to all keys.
     *
     * @var string
     */
    private string $prefix;
    /**
     * Create new Options instance.
     *
     * @param string $prefix The prefix automatically added to option names.
     */
    public function __construct($prefix)
    {
        $this->prefix = $prefix;
    }
    /**
     * Retrieves an option value.
     *
     * @since 2.0.0 Parameter `$default` renamed to `$default_value`.
     *
     * @param string $option        The option name (without the plugin-specific prefix).
     * @param mixed  $default_value Optional. Default value to return if the option does not exist. Default null.
     * @param bool   $raw           Optional. Use the raw option name (i.e. don't call get_name). Default false.
     *
     * @return mixed Value set for the option.
     */
    public function get(string $option, $default_value = null, bool $raw = \false)
    {
        $value = \get_option($raw ? $option : $this->get_name($option), $default_value);
        if (is_array($default_value) && '' === $value) {
            $value = [];
        }
        return $value;
    }
    /**
     * Sets or updates an option.
     *
     * @param string $option   The option name (without the plugin-specific prefix).
     * @param mixed  $value    The value to store.
     * @param bool   $autoload Optional. Whether to load the option when WordPress
     *                         starts up. For existing options, $autoload can only
     *                         be updated using update_option() if $value is also
     *                         changed. Default true.
     * @param bool   $raw      Optional. Use the raw option name (i.e. don't call get_name). Default false.
     *
     * @return bool False if value was not updated and true if value was updated.
     */
    public function set(string $option, $value, bool $autoload = \true, bool $raw = \false): bool
    {
        return \update_option($raw ? $option : $this->get_name($option), $value, $autoload);
    }
    /**
     * Deletes an option.
     *
     * @param string $option The option name (without the plugin-specific prefix).
     * @param bool   $raw    Optional. Use the raw option name (i.e. don't call get_name). Default false.
     *
     * @return bool True, if option is successfully deleted. False on failure.
     */
    public function delete(string $option, bool $raw = \false): bool
    {
        return \delete_option($raw ? $option : $this->get_name($option));
    }
    /**
     * Retrieves the complete option name to use.
     *
     * @param  string $option The option name (without the plugin-specific prefix).
     *
     * @return string
     */
    public function get_name(string $option): string
    {
        return "{$this->prefix}{$option}";
    }
}
