# check-wp-requirements

![Build Status](https://github.com/mundschenk-at/check-wp-requirements/actions/workflows/ci.yml/badge.svg)
[![Latest Stable Version](https://poser.pugx.org/mundschenk-at/check-wp-requirements/v/stable)](https://packagist.org/packages/mundschenk-at/check-wp-requirements)
[![Coverage](https://sonarcloud.io/api/project_badges/measure?project=mundschenk-at_check-wp-requirements&metric=coverage)](https://sonarcloud.io/dashboard?id=mundschenk-at_check-wp-requirements)
[![License](https://poser.pugx.org/mundschenk-at/check-wp-requirements/license)](https://packagist.org/packages/mundschenk-at/check-wp-requirements)

A helper class for WordPress plugins to check PHP version and other requirements.

## Requirements

*   PHP 7.4.0 or above
*   WordPress 5.2 or higher.

## Installation

The best way to use this package is through Composer:

```BASH
$ composer require mundschenk-at/check-wp-requirements
```

## Basic Usage

1.  Create a `\Mundschenk\WP_Requirements` object and set the requirements in the constructor.
2.  Call the `\Mundschenk\WP_Requirements::check()` method and start your plugin normally if it
    returns `true`.

```PHP
// Set up autoloader.
require_once __DIR__ . '/vendor/autoload.php';

/**
 * Load the plugin after checking for the necessary PHP version.
 *
 * It's necessary to do this here because main class relies on namespaces.
 */
function run_your_plugin() {

	$requirements = new \Mundschenk\WP_Requirements( 'Your Plugin Name', __FILE__, 'your-textdomain', [
		'php'       => '8.1.0',
		'multibyte' => true,
		'utf-8'     => false,
	] );

	if ( $requirements->check() ) {
		// Autoload the rest of your classes.

		// Create and start the plugin.
		...
	}
}
run_your_plugin();
```

## License

check-wp-requirements is licensed under the GNU General Public License 2 or later - see the [LICENSE](LICENSE) file for details.
