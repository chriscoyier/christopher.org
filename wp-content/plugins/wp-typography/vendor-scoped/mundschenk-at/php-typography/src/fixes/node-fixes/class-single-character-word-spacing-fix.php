<?php

/**
 *  This file is part of PHP-Typography.
 *
 *  Copyright 2017-2022 Peter Putzer.
 *
 *  This program is free software; you can redistribute it and/or modify modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License along
 *  with this program; if not, write to the Free Software Foundation, Inc.,
 *  51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 *
 *  ***
 *
 *  @package mundschenk-at/php-typography
 *  @license http://www.gnu.org/licenses/gpl-2.0.html
 */
namespace WP_Typography\Vendor\PHP_Typography\Fixes\Node_Fixes;

use WP_Typography\Vendor\PHP_Typography\DOM;
use WP_Typography\Vendor\PHP_Typography\RE;
use WP_Typography\Vendor\PHP_Typography\Settings;
use WP_Typography\Vendor\PHP_Typography\Strings;
use WP_Typography\Vendor\PHP_Typography\U;
/**
 * Prevent single character words from being alone (if enabled).
 *
 * @author Peter Putzer <github@mundschenk.at>
 *
 * @since 5.0.0
 * @since 6.0.0 The replacement now assumes decoded ampersands (i.e. plain "&" instead of "&amp;").
 */
class Single_Character_Word_Spacing_Fix extends Abstract_Node_Fix
{
    const REGEX = '/
		(?:
			(\s)
			(\w|&)
			[' . RE::NORMAL_SPACES . ']
			(?=\w)
		)
	/x';
    /**
     * Apply the fix to a given textnode.
     *
     * @param \DOMText $textnode Required.
     * @param Settings $settings Required.
     * @param bool     $is_title Optional. Default false.
     */
    public function apply(\DOMText $textnode, Settings $settings, $is_title = \false)
    {
        if (empty($settings[Settings::SINGLE_CHARACTER_WORD_SPACING])) {
            return;
        }
        // Add $next_character and $previous_character for context.
        $previous_character = DOM::get_prev_chr($textnode);
        $next_character = DOM::get_next_chr($textnode);
        $node_data = "{$previous_character}{$textnode->data}{$next_character}";
        // Check encoding.
        $f = Strings::functions($node_data);
        if (empty($f)) {
            return;
        }
        // Replace spaces.
        $node_data = \preg_replace(self::REGEX . $f['u'], '$1$2' . U::NO_BREAK_SPACE, $node_data);
        // If we have adjacent characters remove them from the text.
        $textnode->data = self::remove_adjacent_characters($node_data, $f['strlen'], $f['substr'], $f['strlen']($previous_character), $f['strlen']($next_character));
    }
}
/**
 * Prevent single character words from being alone (if enabled).
 *
 * @author Peter Putzer <github@mundschenk.at>
 *
 * @since 5.0.0
 * @since 6.0.0 The replacement now assumes decoded ampersands (i.e. plain "&" instead of "&amp;").
 */
\class_alias('WP_Typography\Vendor\PHP_Typography\Fixes\Node_Fixes\Single_Character_Word_Spacing_Fix', 'PHP_Typography\Fixes\Node_Fixes\Single_Character_Word_Spacing_Fix', \false);
