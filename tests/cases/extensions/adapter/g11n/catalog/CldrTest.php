<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2010, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_cldr\tests\cases\extensions\adapter\g11n\catalog;

use \Exception;
use \lithium\core\Libraries;
use \li3_cldr\extensions\adapter\g11n\catalog\Cldr;

class CldrTest extends \lithium\test\Unit {

	public $adapter;

	protected $_path;

	public function setUp() {
		$library = Libraries::get('li3_cldr');
		$this->_path = $path = $library['path'] . '/resources/g11n';
		$this->adapter = new Cldr(compact('path'));
	}

	public function testPathMustExist() {
		try {
			new Cldr(array('path' => $this->_path));
			$result = true;
		} catch (Exception $e) {
			$result = false;
		}
		$this->assert($result);

		try {
			new Cldr(array('path' => "{$this->_path}/i_do_not_exist"));
			$result = false;
		} catch (Exception $e) {
			$result = true;
		}
		$this->assert($result);
	}

	public function testReadLanguage() {
		$result = $this->adapter->read('language', 'de', null);

		$this->assertEqual($result['be']['translated'], 'Weißrussisch');
		$this->assertEqual($result['en']['translated'], 'Englisch');
		$this->assertEqual($result['fr']['translated'], 'Französisch');

		$result = $this->adapter->read('language', 'de_CH', null);
		$this->assertEqual($result['be']['translated'], 'Weissrussisch');
	}

	public function testReadScript() {
		$result = $this->adapter->read('script', 'de', null);
		$this->assertEqual($result['Cher']['translated'], 'Cherokee');
		$this->assertEqual($result['Hans']['translated'], 'Vereinfachte Chinesische Schrift');
	}

	public function testReadTerritory() {
		$result = $this->adapter->read('territory', 'de', null);
		$this->assertEqual($result['US']['translated'], 'Vereinigte Staaten');
		$this->assertEqual($result['FR']['translated'], 'Frankreich');
	}

	public function testReadCurrency() {
		$result = $this->adapter->read('currency', 'de', null);
		$this->assertEqual($result['DKK']['translated'], 'Dänische Krone');
		$this->assertEqual($result['USD']['translated'], 'US-Dollar');
		$this->assertEqual($result['EUR']['translated'], 'Euro');
	}

	public function testReadValidation() {
		$result = $this->adapter->read('validation', 'en_CA', null);
		$expected = '/^[ABCEGHJKLMNPRSTVXY]\d[A-Z][ ]?\d[A-Z]\d$/';
		$this->assertEqual($result['postalCode']['translated'], $expected);

		$result = $this->adapter->read('validation', 'en', null);
		$this->assertNull($result);
	}

	public function testReadWithScope() {
		$this->adapter = new Cldr(array('path' => $this->_path, 'scope' => 'li3_docs'));

		$result = $this->adapter->read('script', 'de', null);
		$this->assertFalse($result);

		$result = $this->adapter->read('script', 'de', 'li3_docs');
		$this->assertEqual($result['Cher']['translated'], 'Cherokee');
		$this->assertEqual($result['Hans']['translated'], 'Vereinfachte Chinesische Schrift');
	}
}

?>