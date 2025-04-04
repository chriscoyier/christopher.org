<?php

/**
 *  This file is part of PHP-Typography.
 *
 *  Copyright 2017-2019 Peter Putzer.
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
namespace WP_Typography\Vendor\PHP_Typography;

/**
 * Named Unicode characters (in UTF-8 encoding).
 *
 * @author Peter Putzer <github@mundschenk.at>
 *
 * @since 5.0.0
 */
interface U
{
    const NO_BREAK_SPACE = " ";
    const NO_BREAK_NARROW_SPACE = " ";
    const COPYRIGHT = "©";
    const GUILLEMET_OPEN = "«";
    const SOFT_HYPHEN = "­";
    const REGISTERED_MARK = "®";
    const GUILLEMET_CLOSE = "»";
    const MULTIPLICATION = "×";
    const DIVISION = "÷";
    const FIGURE_SPACE = " ";
    const THIN_SPACE = " ";
    const HAIR_SPACE = " ";
    const ZERO_WIDTH_SPACE = "​";
    const HYPHEN_MINUS = '-';
    const HYPHEN = "‐";
    const NO_BREAK_HYPHEN = "‑";
    const EN_DASH = "–";
    const EM_DASH = "—";
    const SINGLE_QUOTE_OPEN = "‘";
    const SINGLE_QUOTE_CLOSE = "’";
    const APOSTROPHE = "ʼ";
    // This is the "MODIFIER LETTER APOSTROPHE".
    const SINGLE_LOW_9_QUOTE = "‚";
    const DOUBLE_QUOTE_OPEN = "“";
    const DOUBLE_QUOTE_CLOSE = "”";
    const DOUBLE_LOW_9_QUOTE = "„";
    const ELLIPSIS = "…";
    const SINGLE_PRIME = "′";
    const DOUBLE_PRIME = "″";
    const SINGLE_ANGLE_QUOTE_OPEN = "‹";
    const SINGLE_ANGLE_QUOTE_CLOSE = "›";
    const FRACTION_SLASH = "⁄";
    const SOUND_COPY_MARK = "℗";
    const SERVICE_MARK = "℠";
    const TRADE_MARK = "™";
    const MINUS = "−";
    const LEFT_CORNER_BRACKET = "「";
    const RIGHT_CORNER_BRACKET = "」";
    const LEFT_WHITE_CORNER_BRACKET = "『";
    const RIGHT_WHITE_CORNER_BRACKET = "』";
    const ZERO_WIDTH_JOINER = "‌";
    const ZERO_WIDTH_NON_JOINER = "‍";
}
/**
 * Named Unicode characters (in UTF-8 encoding).
 *
 * @author Peter Putzer <github@mundschenk.at>
 *
 * @since 5.0.0
 */
\class_alias('WP_Typography\\Vendor\\PHP_Typography\\U', 'PHP_Typography\\U', \false);
