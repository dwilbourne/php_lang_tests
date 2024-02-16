<?php

declare (strict_types=1);
/**
 * @package php_lang_tests
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

namespace tests\intl\locale;

use Locale;
use PHPUnit\Framework\TestCase;

class LocaleTest extends TestCase
{
    /**
     * @function testLocaleParse
     *
     * see the overview of Locales presented in the ICU documentation.  It is very good.
     *
     * https://unicode-org.github.io/icu/userguide/locale/#overview
     *
     * the right order of the parts is Language, script, region
     *
     */

    public function testLocaleParse(): void
    {
        /**
         * locale with all 3 parts: language, script and region
         */
        $testLocale = 'Fr_latn_ca';

        /**
         * language is canonicalized lower case
         */
        self::assertEquals('fr', Locale::getPrimaryLanguage($testLocale));

        /**
         * region is canonicalized to upper case
         */
        self::assertEquals('CA', Locale::getRegion($testLocale));

        /**
         * script is canonicalized so that the first letter is capitalized
         */
        self::assertEquals('Latn', Locale::getScript($testLocale));

        /**
         * 'DisplayLanguage' contains the spelled-out equivalent (and same for DisplayScript and DisplayRegion)
         */
        self::assertEquals('French', Locale::getDisplayLanguage($testLocale));

        /**
         * parsing results in an array with a length of three
         */
        $array = Locale::parseLocale($testLocale);
        self::assertEquals(3, count($array));

        /**
         * you can rely on the array being in the same order as the locale string
         */
        $keys = ['language', 'script', 'region'];
        self::assertEquals($keys, array_keys($array));

        /**
         * let's look at a more standard two-part locale which we read as "english in the US".  The script component
         * is missing.  But instead of parsing the second part of the identifier as the script, it knows there are
         * only two parts and instead interprets the last part (the US) as the region, e.g. it assumes that the
         * script was not included in the string.
         */
        $testLocale = 'en_US';
        self::assertEquals('en', Locale::getPrimaryLanguage($testLocale));
        self::assertEquals('US', Locale::getRegion($testLocale));
        self::assertEquals('', Locale::getScript($testLocale));

        /**
         * what about a locale with nothing more than a language?
         */
        $testLocale = 'en';
        self::assertEquals('en', Locale::getPrimaryLanguage($testLocale));
        self::assertEquals('', Locale::getRegion($testLocale));
        self::assertEquals('', Locale::getScript($testLocale));

        /**
         * use the canonicalize method to output the locale as a properly formed string
         */
        $testLocale = 'En_uS';
        self::assertEquals('en_US', Locale::canonicalize($testLocale));


        $testLocale = 'Latn_fr_ca';

        /**
         * the order is important of course:  it expects Language-Script-Region.  So in this example, because the
         * order is incorrect, it thinks that 'Latn' is the language. (well OK, it's a dead language :).
         *
         * The parser is just picking up characters, not validating them against any sort of table yet.
         */
        self::assertEquals('latn', Locale::getPrimaryLanguage($testLocale));

        /**
         * if it can't find a key in its lookup table, getDisplayLanguage just returns the string as it is
         */
        self::assertEquals('latn', Locale::getDisplayLanguage($testLocale));

        $testLocale = '';
        Locale::setDefault('de_DE');
        /**
         * returns default language if argument is empty;
         */
        self::assertEquals('de', Locale::getPrimaryLanguage($testLocale));

        /**
         * parseLocale has the same behavior
         */
        $array = Locale::parseLocale($testLocale);
        self::assertEquals('de', $array['language']);

        /**
         * ever wonder what it does with a badly formed locale? Ouch
         */
        $testLocale = 'Latn_fr_ca@currency=euro@collation=bluto';
        $array = Locale::parseLocale($testLocale);

        /** canonocalizes it to lower case because it thinks it's the langauage */
        self::assertEquals('latn', $array['language']);
        /** thinks 'FR' is the region */
        self::assertEquals('FR', $array['region']);
        /** thinks CA (canonicalized to upper case) is variant0 */
        self::assertEquals('CA', $array['variant0']);

        /** notice that parseLocale removes keywords */
        self::assertEquals(3, count($array));
    }

    public function testKeywords(): void
    {
        $testLocale = 'Latn_fr_ca@currency=euro';
        $expectedResult = ['currency' => 'euro'];
        self::assertEquals($expectedResult, Locale::getKeywords($testLocale));

        /**
         * unlike parseLocale, this method gives up and returns false
         */
        $testLocale = 'Latn_fr_ca@currency=euro@collation=bluto';
        $expectedResult = false;
        self::assertEquals($expectedResult, Locale::getKeywords($testLocale));
    }
}
