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

use WP_Typography\Vendor\Mundschenk\Data_Storage\Options;
/**
 * HTML <input> element.
 *
 * @phpstan-import-type Input_Arguments from Input
 */
class Hidden_Input extends Input
{
    /**
     * Create a new input control object.
     *
     * @param Options $options      Options API handler.
     * @param ?string $options_key  Database key for the options array. Passing null means that the control ID is used instead.
     * @param string  $id           Control ID (equivalent to option name). Required.
     * @param array   $args {
     *    Optional and required arguments.
     *
     *    @type string      $tab_id           Tab ID. Required.
     *    @type string      $section          Optional. Section ID. Default Tab ID.
     *    @type string|int  $default          The default value. Required, but may be an empty string.
     *    @type string|null $short            Optional. Short label. Default null.
     *    @type array       $attributes       Optional. Default [],
     *    @type array       $outer_attributes Optional. Default [],
     * }
     *
     * @throws \InvalidArgumentException Missing argument.
     *
     * @phpstan-param Input_Arguments $args
     */
    public function __construct(Options $options, ?string $options_key, string $id, array $args)
    {
        $args['input_type'] = 'hidden';
        $args['label'] = null;
        $args['help_text'] = null;
        $args['inline_help'] = \false;
        parent::__construct($options, $options_key, $id, $args);
    }
}
