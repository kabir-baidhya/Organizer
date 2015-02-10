<?php

namespace Gckabir\Organizer\Misc;

use Gckabir\Organizer\TestCase;

class HelperTest extends TestCase
{
    public function testStartsWithIsWorking()
    {
        $this->assertTrue(Helper::startsWith('Hello World', 'Hello'));

        $this->assertFalse(Helper::startsWith('Foo Bar', 'Bar'));
    }

    public function testStartsWithIsCaseSensative()
    {
        $this->assertTrue(Helper::startsWith('TestString', 'Test'));

        $this->assertFalse(Helper::startsWith('stringOnTest', 'String'));
    }

    public function testEndsWithIsWorking()
    {
        $this->assertTrue(Helper::endsWith('Awesome', 'some'));

        $this->assertFalse(Helper::endsWith('WebDevelopment', 'Web'));
    }

    public function testEndsWithIsCaseSensative()
    {
        $this->assertTrue(Helper::endsWith('WebDesign', 'Design'));

        $this->assertFalse(Helper::endsWith('WebDesign', 'design'));
    }

    /**
     * @dataProvider providerStringsContainingWildcardCharacters
     */
    public function testHasWildCardsForWildCardStrings($string)
    {
        $this->assertTrue(Helper::hasWildCards($string));
    }

    /**
     * @dataProvider providerStringsNotContainingWildcardCharacters
     */
    public function testHasWildCardsForNonWildCardStrings($string)
    {
        $this->assertFalse(Helper::hasWildCards($string));
    }

    public function providerStringsContainingWildcardCharacters()
    {
        return [
            ['files.*'],
            ['*.*'],
            ['[abc]xyz.???'],
            ['?as?.*'],
            ['*'],
            ['a*'],
            ['vendor/*'],
            ['*.sh'],

        ];
    }

    public function providerStringsNotContainingWildcardCharacters()
    {
        return [
            ['nothing'],
            ['keyword'],
            ['identifiers'],
            ['service provider'],
            ['/etc/test/'],
        ];
    }
}
