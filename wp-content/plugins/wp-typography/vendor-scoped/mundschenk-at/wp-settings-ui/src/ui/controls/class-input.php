<?php

/**
 *  This file is part of WordPress Settings UI.
 *
 *  Copyright 2017-2024 Peter Putzer.
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
namespace WP_Typography\Vendor\Mundschenk\UI\Controls;

use WP_Typography\Vendor\Mundschenk\UI\Abstract_Control;
use WP_Typography\Vendor\Mundschenk\Data_Storage\Options;
/**
 * HTML <input> element.
 *
 * @phpstan-type Input_Arguments array{
 *     input_type: string,
 *     tab_id: string,
 *     section?: string,
 *     default: string|int,
 *     option_values?: string[],
 *     short?: ?string,
 *     label?: ?string,
 *     help_text?: ?string,
 *     inline_help?: bool,
 *     attributes?: array<string,string>,
 *     outer_attributes?: array<string,string>,
 *     settings_args?: array<string,string>
 * }
 * @phpstan-type Complete_Input_Arguments array{
 *     input_type: string,
 *     tab_id: string,
 *     section: string,
 *     default: string|int,
 *     option_values?: string[],
 *     tab_id: string,
 *     section: string,
 *     short: ?string,
 *     label: ?string,
 *     help_text: ?string,
 *     inline_help: bool,
 *     attributes: array<string,string>,
 *     outer_attributes: array<string,string>,
 *     settings_args: array<string,string>,
 *     sanitize_callback: ?callable,
 * }
 */
abstract class Input extends Abstract_Control
{
    /**
     * The input type ('checkbox', ...).
     *
     * @var string
     */
    protected $input_type;
    /**
     * Create a new input control object.
     *
     * @param Options $options      Options API handler.
     * @param ?string $options_key  Database key for the options array. Passing null means that the control ID is used instead.
     * @param string  $id           Control ID (equivalent to option name). Required.
     * @param array   $args {
     *    Optional and required arguments.
     *
     *    @type string      $input_type       HTML input type ('checkbox' etc.). Required.
     *    @type string      $tab_id           Tab ID. Required.
     *    @type string      $section          Optional. Section ID. Default Tab ID.
     *    @type string|int  $default          The default value. Required, but may be an empty string.
     *    @type array       $option_values    Optional. The allowed values.
     *    @type string|null $short            Optional. Short label. Default null.
     *    @type string|null $label            Optional. Label content with the position of the control marked as %1$s. Default null.
     *    @type string|null $help_text        Optional. Help text. Default null.
     *    @type bool        $inline_help      Optional. Display help inline. Default false.
     *    @type array       $attributes       Optional. Default [],
     *    @type array       $outer_attributes Optional. Default [],
     *    @type array       $settings_args    Optional. Default [],
     * }
     *
     * @phpstan-param Input_Arguments $args
     */
    public function __construct(Options $options, ?string $options_key, string $id, array $args)
    {
        /**
         * Fill in missing mandatory arguments.
         *
         * @phpstan-var Complete_Input_Arguments $args
         */
        $args = $this->prepare_args($args, ['input_type', 'tab_id', 'default']);
        // Handle input type.
        $this->input_type = $args['input_type'];
        $args['sanitize_callback'] = $args['sanitize_callback'] ?? 'sanitize_text_field';
        parent::__construct($options, $options_key, $id, $args);
    }
    /**
     * Retrieves the value markup for this input.
     *
     * @param mixed $value The input value.
     *
     * @return string
     */
    protected function get_value_markup($value): string
    {
        return $value ? 'value="' . \esc_attr($value) . '" ' : '';
    }
    /**
     * Retrieves the control-specific HTML markup.
     *
     * @return string
     */
    protected function get_element_markup(): string
    {
        return '<input type="' . \esc_attr($this->input_type) . '" ' . "{$this->get_id_and_class_markup()} {$this->get_value_markup($this->get_value())}/>";
    }
}
