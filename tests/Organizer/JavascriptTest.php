<?php

namespace Gckabir\Organizer;

class JavascriptTest extends TestCase
{
    public static function setUpBeforeClass()
    {
        static::setUpConfigurations();
    }

    public function testInstantiation()
    {
        $js = new Javascript('testbundle1', array(), '1.0');
        $this->assertInstanceOf('Gckabir\Organizer\Javascript', $js);
    }

    public function testMergeThrowsExceptionIfFileNotFound()
    {
        $js = new Javascript('filenotfoundtest', array(
            'foo.js',
            ), '1.0');

        $this->setExpectedException('Gckabir\Organizer\Exception\FileNotFoundException');

        $js->merge();
    }

    public function testSettingVariablesInBulkIsWorkingCorrectly()
    {
        $js = new Javascript('testbundle', array(), '1.0');
        $this->assertAttributeEquals(array(), 'variables', $js);
        $myVariables = array(
            'foo'    => 'Hello',
            'bar'    => ' World',
        );

        $js->vars($myVariables);

        $this->assertAttributeEquals($myVariables, 'variables', $js);

        $newVariables = array('foo'    => 'bar');
        $js->vars($newVariables);

        $this->assertAttributeNotEquals($myVariables, 'variables', $js);
        $this->assertAttributeEquals($newVariables, 'variables', $js);
    }

    public function testSettingVariablesIndividuallyIsWorking()
    {
        $js = new Javascript('testbundle', array(), '1.0');
        $this->assertAttributeEquals(array(), 'variables', $js);

        $myVariables = array(
            'foo'    => 'Hello',
            'bar'    => ' World',
        );

        $js->vars($myVariables);

        $js->setVar('foo', 'Hello World');
        $js->setVar('bar', '');
        $js->setVar('test', 'Testing');
        $this->assertAttributeNotEquals($myVariables, 'variables', $js);

        $expectedVariablesNow = array(
            'foo'    => 'Hello World',
            'bar'    => '',
            'test'    => 'Testing',
        );
        $this->assertAttributeEquals($expectedVariablesNow, 'variables', $js);
    }

    public function testMethodChaining()
    {
        $js = new Javascript('testbundle', array(), '1.0');

        $result = $js->vars(array('foo'    => 'bar'));

        $this->assertInstanceOf('Gckabir\Organizer\Javascript', $result);

        $result = $js->setVar('foo', 'Foo');

        $this->assertInstanceOf('Gckabir\Organizer\Javascript', $result);

        $result = $js->setVar('foo', 'Foo');
    }

    public function testGetTypeFunctionWorks()
    {
        $js = new Javascript('testbundle', array(), '1.0');
        $this->assertEquals('javascript', $js->getType());
    }

    public function testAddingNewScriptsWorksAsExpected()
    {
        $scripts = array(
            'file1.js',
            'file2.js',
        );
        $js = new Javascript('testbundle', $scripts, '1.0');

        $this->assertAttributeEquals($scripts, 'includes', $js);

        // Adding single file
        $js->add('file3.js');

        $scripts[] = 'file3.js';

        $this->assertAttributeEquals($scripts, 'includes', $js);

        // Try adding duplicate
        $js->add('file1.js');

        $this->assertAttributeEquals($scripts, 'includes', $js);

        $js->add(array(
            'file4.js',
            'file5.js',
        ));

        $this->assertAttributeNotEquals($scripts, 'includes', $js);

        $scripts[] = 'file4.js';
        $scripts[] = 'file5.js';

        $this->assertAttributeEquals($scripts, 'includes', $js);
    }

    public function testAddFunctionIgnoresDuplicateFiles()
    {
        $scripts = array(
            'file1.js',
            'file2.js',
            'file3.js',
        );

        $js = new Javascript('testbundle', $scripts, '1.0');

        // Try adding Duplicate seperately
        $js->add('file1.js');
        $js->add('file2.js');
        $js->add('file3.js');

        $this->assertAttributeEquals($scripts, 'includes', $js);

        // Try adding Duplicate files in bulk
        $js->add(array(
            'file2.js',
            'file1.js',
        ));

        $this->assertAttributeEquals($scripts, 'includes', $js);

        // Try adding mixuture with Duplicate files
        $js->add(array(
            'file4.js',
            'file3.js',
            'file5.js',
        ));

        $scripts[] = 'file4.js';
        $scripts[] = 'file5.js';

        $this->assertAttributeEquals($scripts, 'includes', $js);
    }

    public function testAddThrowsExceptionForInvalidParameter()
    {
        $js = new Javascript('testbundle', array(), '1.0');

        $this->setExpectedException('Gckabir\Organizer\Exception\OrganizerException');

        $js->add(array(5, 'hello.js', 2));
    }

    public function testAddingFilenameBeforeWorksProperly()
    {
        $js = new Javascript('testbundle', array(
            'file1.js',
        ), '1.0');
        $js->addBefore('file2.js');
        $js->add('file3.js');
        $js->addBefore('file4.js');
        $expectedScripts = array('file4.js', 'file2.js', 'file1.js', 'file3.js');

        $this->assertAttributeEquals($expectedScripts, 'includes', $js);
    }

    public function testAddingDirectCodeWorksProperly()
    {
        $js = new Javascript('testbundle', array(), '1.0');

        $js->add('file1.js');
        $js->addCode('alert("Hello World")');
        $js->add('file2.js');

        $expectedIncludes = array(
            'file1.js',
            (object) array(
                'code'    => 'alert("Hello World")',
                'type'    => 'embeded',
            ),
            'file2.js',
        );
        $this->assertAttributeEquals($expectedIncludes, 'includes', $js);
    }

    public static function tearDownAfterClass()
    {
        static::clearUpTestData();
    }
}
