<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2010, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

use \lithium\g11n\Catalog;

/**
 * Add the configuration for the cldr resource to the existing
 * `Catalog` configurations.
 */
Catalog::config(array(
	'cldr' => array(
		'adapter' => 'Cldr',
		'path' => dirname(__DIR__) . '/resources/g11n'
)) + Catalog::config());

?>