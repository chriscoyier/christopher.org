<?php

/**
 *  This file is part of PHP-Typography.
 *
 *  Copyright 2017 Peter Putzer.
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
namespace WP_Typography\Vendor\PHP_Typography\Fixes;

use WP_Typography\Vendor\PHP_Typography\Settings;
use WP_Typography\Vendor\PHP_Typography\Text_Parser\Token;
/**
 * All fixes that apply to parsed text tokens should implement this interface.
 *
 * @author Peter Putzer <github@mundschenk.at>
 *
 * @since 5.0.0
 */
interface Token_Fix
{
    const MIXED_WORDS = 1;
    const COMPOUND_WORDS = 2;
    const WORDS = 3;
    const OTHER = 4;
    /**
     * Apply the fix to a given textnode.
     *
     * @param Token[]       $tokens   Required.
     * @param Settings      $settings Required.
     * @param bool          $is_title Optional. Default false.
     * @param \DOMText|null $textnode Optional. Default null.
     *
     * @return Token[] An array of tokens.
     */
    public function apply(array $tokens, Settings $settings, $is_title = \false, \DOMText $textnode = null);
    /**
     * Determines whether the fix should be applied to (RSS) feeds.
     *
     * @return bool
     */
    public function feed_compatible();
    /**
     * Retrieves the target token array for this fix.
     *
     * @return int
     */
    public function target();
}
/**
 * All fixes that apply to parsed text tokens should implement this interface.
 *
 * @author Peter Putzer <github@mundschenk.at>
 *
 * @since 5.0.0
 */
\class_alias('WP_Typography\Vendor\PHP_Typography\Fixes\Token_Fix', 'PHP_Typography\Fixes\Token_Fix', \false);
