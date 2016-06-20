<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2010, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_cldr\extensions\adapter\g11n\catalog;

use \Exception;
use \SimpleXmlElement;
use \lithium\util\Inflector;
use \lithium\g11n\Locale;

/**
 * The `Cldr` class is an adapter which allows reading from the Common Locale Data Repository
 * maintained by the Unicode Consortium.
 *
 * The directory the `'path'` configuration setting is pointing to should contain the contents
 * from the CLDR as distributed with i.e. `core.zip` which can be downloaded from the unicode site.
 *
 * The directory as configured by the `'path'` setting which equals the `common` directory
 * from `core.zip` should be structured according to the following example.
 *
 * {{{
 * | - `main`
 * | - `supplemental`
 * | - ...
 * }}}
 *
 * @link http://unicode.org/cldr
 * @link http://unicode.org/Public/cldr/1.7.0/core.zip
 */
class Cldr extends \lithium\g11n\catalog\Adapter {

	/**
	 * Constructor.
	 *
	 * @param array $config Available configuration options are:
	 *        - `'path'`: The path to the directory holding the data.
	 *        - `'scope'`: Scope to use.
	 * @return object
	 */
	public function __construct(array $config = array()) {
		$defaults = array('path' => null, 'scope' => null);
		parent::__construct($config + $defaults);
	}

	/**
	 * Initializer.  Checks if the configured path exists.
	 *
	 * @return void
	 * @throws \Exception
	 */
	protected function _init() {
		parent::_init();
		if (!is_dir($this->_config['path'])) {
			throw new Exception("Cldr directory does not exist at `{$this->_config['path']}`");
		}
	}

	/**
	 * Reads data.
	 *
	 * @param string $category A category. The following categories are supported:
	 *               - `'currency'`
	 *               - `'language'`
	 *               - `'script'`
	 *               - `'territory'`
	 *               - `'validation'`
	 * @param string $locale A locale identifier.
	 * @param string $scope The scope for the current operation.
	 * @return array|void
	 */
	public function read($category, $locale, $scope) {
		if ($scope != $this->_config['scope']) {
			return null;
		}
		$path = $this->_config['path'];

		switch ($category) {
			case 'currency':
				return $this->_readCurrency($path, $locale);
			case 'language':
			case 'script':
				return $this->_readList($path, $category, $locale);
			case 'territory':
				return $this->_readTerritory($path, $locale);
			case 'validation':
				return $this->_readValidation($path, $locale);
		}
	}

	protected function _readValidation($path, $locale) {
		if ($locale === 'root' || !$territory = Locale::territory($locale)) {
			return null;
		}
		$data = array();

		$file = "{$path}/supplemental/postalCodeData.xml";
		$query  = "/supplementalData/postalCodeData";
		$query .= "/postCodeRegex[@territoryId=\"{$territory}\"]";

		$nodes = $this->_parseXml($file, $query);
		$regex = (string) current($nodes);

		return $this->_merge($data, array(
			'id' => 'postalCode',
			'translated' => "/^{$regex}$/"
		));
	}

	protected function _readList($path, $category, $locale) {
		$plural = Inflector::pluralize($category);

		$file = "{$path}/main/{$locale}.xml";
		$query = "/ldml/localeDisplayNames/{$plural}/{$category}";

		$nodes = $this->_parseXml($file, $query);
		$data = array();

		foreach ($nodes as $node) {
			$data = $this->_merge($data, array(
				'id' => (string) $node['type'],
				'translated' => (string) $node
			));
		}
		return $data;
	}

	protected function _readCurrency($path, $locale) {
		$file = "{$path}/main/{$locale}.xml";
		$query = "/ldml/numbers/currencies/currency";

		$nodes = $this->_parseXml($file, $query);
		$data = array();

		foreach ($nodes as $node) {
			$displayNames = $node->xpath('displayName');

			$data = $this->_merge($data, array(
				'id' => (string) $node['type'],
				'translated' => (string) current($displayNames)
			));
		}
		return $data;
	}

	protected function _readTerritory($path, $locale) {
		$file = "{$path}/main/{$locale}.xml";
		$query = "/ldml/localeDisplayNames/territories/territory";

		$nodes = $this->_parseXml($file, $query);
		$data = array();

		foreach ($nodes as $node) {
			$attributes = $node->attributes();

			if (isset($attributes['alt']) && (string) $attributes['alt'] === 'variant') {
				continue;
			}
			if (isset($attributes['draft'])) {
				continue;
			}

			$data = $this->_merge($data, array(
				'id' => (string) $node['type'],
				'translated' => (string) $node
			));
		}
		return $data;
	}

	/**
	 * Parses a XML file and retrieves data from it using an XPATH query
	 * and a given closure.
	 *
	 * If possible will activate small nodes allocation optimization
	 * when interfacing with libxml. Some options are available only
	 * when using libxml with version >= 2.6.21.
	 *
	 * @param string $file Absolute path to the XML file.
	 * @param string $query An XPATH query to select items.
	 * @return array
	 */
	protected function _parseXml($file, $query) {
		if (!file_exists($file)) {
			return array();
		}
		$options = defined('LIBXML_COMPACT') ? LIBXML_COMPACT : 0;

		$document = new SimpleXmlElement($file, $options, true);
		return $document->xpath($query);
	}
}

?>