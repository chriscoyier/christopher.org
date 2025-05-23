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
namespace WP_Typography\Vendor\Mundschenk\UI\Controls;

use WP_Typography\Vendor\Mundschenk\UI\Abstract_Control;
use WP_Typography\Vendor\Mundschenk\Data_Storage\Options;
/**
 * A control displaying read-only text.
 *
 * @phpstan-type Display_Text_Arguments array{
 *     tab_id: string,
 *     section?: string,
 *     short?: ?string,
 *     label?: ?string,
 *     help_text?: ?string,
 *     inline_help?: bool,
 *     attributes?: array<string,string>,
 *     outer_attributes?: array<string,string>,
 *     settings_args?: array<string,string>
 * }
 * @phpstan-type Complete_Display_Text_Arguments array{
 *     tab_id: string,
 *     section?: string,
 *     tab_id: string,
 *     section: string,
 *     elements: string[],
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
class Display_Text extends Abstract_Control
{
    const ALLOWED_ATTRIBUTES = ['id' => [], 'name' => [], 'class' => [], 'aria-describedby' => []];
    const ALLOWED_HTML = ['div' => self::ALLOWED_ATTRIBUTES, 'span' => self::ALLOWED_ATTRIBUTES, 'p' => self::ALLOWED_ATTRIBUTES, 'ul' => self::ALLOWED_ATTRIBUTES, 'ol' => self::ALLOWED_ATTRIBUTES, 'li' => self::ALLOWED_ATTRIBUTES, 'a' => ['class' => [], 'href' => [], 'rel' => [], 'target' => []], 'code' => [], 'strong' => [], 'em' => [], 'sub' => [], 'sup' => []];
    /**
     * The HTML elements to display.
     *
     * @var string[]
     */
    protected $elements;
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
     *    @type array       $elements         The HTML elements to display (including the outer tag). Required.
     *    @type string|null $short            Optional. Short label. Default null.
     *    @type bool        $inline_help      Optional. Display help inline. Default false.
     *    @type array       $attributes       Optional. Default [],
     *    @type array       $outer_attributes Optional. Default [],
     *    @type array       $settings_args    Optional. Default [],
     * }
     *
     * @phpstan-param Display_Text_Arguments $args
     */
    public function __construct(Options $options, ?string $options_key, string $id, array $args)
    {
        /**
         * Fill in missing mandatory arguments.
         *
         * @phpstan-var Complete_Display_Text_Arguments $args
         */
        $args = $this->prepare_args($args, ['elements']);
        // Handle extra elements.
        $this->elements = $args['elements'];
        $args['default'] = '';
        $args['label'] = null;
        $args['sanitize_callback'] = static function () {
            return '';
        };
        parent::__construct($options, $options_key, $id, $args);
    }
    /**
     * Retrieves the current value for the control. In this case, the method always returns ''.
     *
     * @return string
     */
    public function get_value(): string
    {
        return '';
    }
    /**
     * Retrieves the control-specific HTML markup.
     *
     * @return string
     */
    protected function get_element_markup(): string
    {
        return \wp_kses(\implode('', $this->elements), self::ALLOWED_HTML);
    }
}
