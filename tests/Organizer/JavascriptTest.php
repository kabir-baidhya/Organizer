<?php 

namespace Gckabir\Organizer;


class JavascriptTest extends TestCase 
{
	public static function setUpBeforeClass() {
		$config = array(
			'cacheDir'	=> __DIR__.'/../TestData/_organizer-cache/',
			'javascript'	=> array(
				'basePath'	=> __DIR__.'/../TestData/js/'
				)
			);

		OZR::init($config);
	}

	public function testInstantiation() {

		$js = new Javascript('testbundle1', array(), '1.0');
		$this->assertInstanceOf('Gckabir\Organizer\Javascript', $js);
	}

	public function testMergeThrowsExceptionIfFileNotFound() {

		$js = new Javascript('filenotfoundtest', array(
			'foo.js'
			), '1.0');

		$this->setExpectedException('Gckabir\Organizer\FileNotFoundException');
		$js->merge();
	}

	public static function 	tearDownAfterClass() {
		$directory = OZR::getConfig('cacheDir');
		if(is_dir($directory)) {
			rmdir($directory);
		}
	}
}
