<?php

/**
 *  This file is part of WordPress Settings UI.
 *
 *  Copyright 2018-2024 Peter Putzer.
 *
 *  This program is free software; you can redistribute it and/or
 *  modify it under the terms of the GNU General Public License
 *  as published by the Free Software Foundation; either version 2
 *  of the License, or (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 *  ***
 *
 *  @package mundschenk-at/wp-settings-ui
 *  @license http://www.gnu.org/licenses/gpl-2.0.html
 */
namespace WP_Typography\Vendor\Mundschenk\UI;

use WP_Typography\Vendor\Mundschenk\Data_Storage\Options;
/**
 * An interface for HTML controls.
 *
 * @phpstan-type Control_Arguments array{
 *     tab_id: string,
 *     section: string,
 *     default: string|int,
 *     option_values?: string[],
 *     short?: ?string,
 *     label?: ?string,
 *     help_text?: ?string,
 *     inline_help?: bool,
 *     attributes?: array<string,string>
 * }
 */
interface Control
{
    /**
     * Creates a new control.
     *
     * @param Options $options      Options API handler.
     * @param ?string $options_key  Database key for the options array. Passing null means that the control ID is used instead.
     * @param string  $id           Control ID (equivalent to option name). Required.
     * @param array   $args {
     *    Optional and required arguments.
     *
     *    @type string      $tab_id        Tab ID. Required.
     *    @type string      $section       Section ID. Required.
     *    @type string|int  $default       The default value. Required, but may be an empty string.
     *    @type array       $option_values The allowed values. Required.
     *    @type string|null $short         Optional. Short label. Default null.
     *    @type string|null $label         Optional. Label content with the position of the control marked as %1$s. Default null.
     *    @type string|null $help_text     Optional. Help text. Default null.
     *    @type bool        $inline_help   Optional. Display help inline. Default false.
     *    @type array       $attributes    Optional. Default [],
     * }
     *
     * @throws \InvalidArgumentException Missing argument.
     *
     * @phpstan-param Control_Arguments $args
     */
    public function __construct(Options $options, ?string $options_key, string $id, array $args);
    /**
     * Retrieve the current value for the control.
     * May be overridden by subclasses.
     *
     * @return mixed
     */
    public function get_value();
    /**
     * Render the HTML representation of the control.
     */
    public function render(): void;
    /**
     * Retrieve default value.
     *
     * @return string|int
     */
    public function get_default();
    /**
     * Retrieve control ID.
     *
     * @return string
     */
    public function get_id(): string;
    /**
     * Retrieves the label. If the label text contains a string placeholder, it
     * is replaced by the control element markup.
     *
     * @return string
     */
    public function get_label(): string;
    /**
     * Register the control with the settings API.
     *
     * @param string $option_group Application-specific prefix.
     */
    public function register(string $option_group): void;
    /**
     * Groups another control with this one.
     *
     * @param Control $control Any control.
     */
    public function add_grouped_control(Control $control): void;
    /**
     * Registers this control as grouped with another one.
     *
     * @since 2025.1 Marked as internal.
     *
     * @param Control $control Any control.
     *
     * @internal
     */
    public function group_with(Control $control): void;
    /**
     * Sanitizes an option value.
     *
     * @param  mixed $value The unslashed post variable.
     *
     * @return mixed        The sanitized value.
     */
    public function sanitize($value);
}
