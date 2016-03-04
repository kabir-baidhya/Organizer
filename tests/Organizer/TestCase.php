<?php
/*
 * This file is part of the Organizer package.
 *
 * (c) Kabir Baidhya <kabeer182010@gmail.com>
 *
 */

namespace Gckabir\Organizer;

use DirectoryIterator;

class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * Call protected/private method of a class.
     *
     * @param object &$object    Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array  $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     */
    public function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    public static function clearExistingData($path)
    {
        $iterator = new DirectoryIterator($path);
        foreach ($iterator as $fileinfo) {
            if ($fileinfo->isFile()) {
                unlink($fileinfo->getPathname());
            }
        }
    }

    public static function setUpConfigurations()
    {
        OZR::init(array(
            'cacheDir' => __DIR__.'/../TestData/_organizer-cache/',
            'javascript' => array(
                'basePath' => __DIR__.'/../TestData/js/',
                ),

            'css' => array(
                'basePath' => __DIR__.'/../TestData/css/',
                ),
            ));

        $jsDir = OZR::getConfig('javascript')['basePath'];

        if (!is_dir($jsDir)) {
            mkdir($jsDir);
        }

        static::clearExistingData($jsDir);
        static::createJSTestFiles();
    }

    public static function clearUpTestData()
    {
        $jsDir = OZR::getConfig('javascript')['basePath'];

        if (is_dir($jsDir)) {
            static::clearExistingData($jsDir);
            rmdir($jsDir);
        }

        $cacheDir = OZR::getConfig('cacheDir');
        if (is_dir($cacheDir)) {
            rmdir($cacheDir);
        }
    }

    public static function createJSTestFiles()
    {
        $path = OZR::getConfig('javascript')['basePath'];
        $files = array(
            'file1.js' => '// file1.js code',
            'test2.js' => '// test2.js code',
            'test3.js' => 'alert("Hello");',
            'abc.xyz.js' => '// abc.xyz.js code',
            'abc.test.js' => '// abc.test.js code',
            );

        // Write test files
        foreach ($files as $file => $content) {
            file_put_contents($path.$file, $content);
        }
    }
}
