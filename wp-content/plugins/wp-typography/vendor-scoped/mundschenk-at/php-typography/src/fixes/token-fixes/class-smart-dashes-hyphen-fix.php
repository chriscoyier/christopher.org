<?php

/**
 *  This file is part of PHP-Typography.
 *
 *  Copyright 2018 Peter Putzer.
 *
 *  This program is free software; you can redistribute it and/or modify
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
namespace WP_Typography\Vendor\PHP_Typography\Fixes\Token_Fixes;

use WP_Typography\Vendor\PHP_Typography\Fixes\Token_Fix;
use WP_Typography\Vendor\PHP_Typography\Settings;
use WP_Typography\Vendor\PHP_Typography\Text_Parser\Token;
use WP_Typography\Vendor\PHP_Typography\U;
/**
 * Replaces hyphen-minus with proper hyphen.
 *
 * @author Peter Putzer <github@mundschenk.at>
 *
 * @since 6.3.0
 */
class Smart_Dashes_Hyphen_Fix extends Abstract_Token_Fix
{
    /**
     * Creates a new fix instance.
     *
     * @param bool $feed_compatible Optional. Default false.
     */
    public function __construct($feed_compatible = \false)
    {
        parent::__construct(Token_Fix::MIXED_WORDS, $feed_compatible);
    }
    /**
     * Apply the tweak to a given textnode.
     *
     * @param Token[]       $tokens   Required.
     * @param Settings      $settings Required.
     * @param bool          $is_title Optional. Default false.
     * @param \DOMText|null $textnode Optional. Default null.
     *
     * @return Token[] An array of tokens.
     */
    public function apply(array $tokens, Settings $settings, $is_title = \false, \DOMText $textnode = null)
    {
        if (!empty($settings[Settings::SMART_DASHES])) {
            foreach ($tokens as $index => $text_token) {
                // Handled here because we need to know we are inside a word and not a URL.
                $tokens[$index] = $text_token->with_value(\str_replace('-', U::HYPHEN, $text_token->value));
            }
        }
        return $tokens;
    }
}
/**
 * Replaces hyphen-minus with proper hyphen.
 *
 * @author Peter Putzer <github@mundschenk.at>
 *
 * @since 6.3.0
 */
\class_alias('WP_Typography\Vendor\PHP_Typography\Fixes\Token_Fixes\Smart_Dashes_Hyphen_Fix', 'PHP_Typography\Fixes\Token_Fixes\Smart_Dashes_Hyphen_Fix', \false);
