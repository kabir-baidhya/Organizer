<?php 

namespace Gckabir\Organizer;



class OrganizerObjectTest extends TestCase 
{

	public static function setUpBeforeClass() {
		static::setUpConfigurations();
	}

	public function testGettingSourceCodeWorksAsExpectedForIndividualFiles() {

		$js = new Javascript('testbundle1', array(), '1.0');
		
		// Note:: Files are created by TestCase::createJSTestFiles()
		// test for all test files individually
		
		$code = $this->invokeMethod($js, 'getSourceCode', array('test2.js'));
		$this->assertEquals($code, '// test2.js code');

		$code = $this->invokeMethod($js, 'getSourceCode', array('test3.js'));
		$this->assertEquals($code, 'alert("Hello");');
	}

	public function testGettingSourceCodeWorksAsExpectedForFilesMatchingPattern() {

		$js = new Javascript('testbundle1', array(), '1.0');

		// test for all test files matched by pattern
		$code = $this->invokeMethod($js, 'getSourceCode', array('file*'));
		$this->assertEquals(trim($code), '// file1.js code');


		$expected = "\n// abc.test.js code".
					"\n// abc.xyz.js code";
		$code = $this->invokeMethod($js, 'getSourceCode', array('abc.*'));
		$this->assertEquals($code, $expected);

		$expected = "\n// test2.js code".
					"\nalert(\"Hello\");";
		$code = $this->invokeMethod($js, 'getSourceCode', array('test?.js'));
		$this->assertEquals($code, $expected);

	}


	public static function tearDownAfterClass() {
		static::clearUpTestData();
	}
}