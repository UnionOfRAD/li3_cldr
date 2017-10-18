<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2010, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_cldr\config;

use lithium\core\ConfigException;
use lithium\g11n\Catalog;

$insideLibrary = dirname(__DIR__) . '/resources/g11n';
$insideApp = LITHIUM_APP_PATH . '/resources/g11n/cldr';

if (is_dir($insideLibrary . '/main')) {
	$path = $insideLibrary;
} elseif (is_dir($insideApp . '/main')) {
	$path = $insideApp;
} else {
	$message = "CLDR resources could not be found at either `{$insideLibrary}` or `{$insideApp}`.";
	throw new ConfigException($message);
}
/**
 * Add the configuration for the cldr resource to the existing
 * `Catalog` configurations.
 */
Catalog::config(array(
	'cldr' => array(
		'adapter' => 'Cldr',
		'path' => $path
)) + Catalog::config());

?>