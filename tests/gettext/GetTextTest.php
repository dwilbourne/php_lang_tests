<?php

declare (strict_types=1);
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

namespace tests\gettext;

use PHPUnit\Framework\TestCase;

/**
 * Class GetTextTest
 */
class GetTextTest extends TestCase
{
    protected string $textDomain;
    protected string $textDomainDirectory;

    public function setUp() : void
    {
        /* gettext directory location relative to this file */
        $this->textDomainDirectory = dirname(__FILE__) . "/locale";
        $this->textDomain = "messages";
    }

    public function setEnvironment(string $locale) : void
    {

        if (defined('LC_MESSAGES')) {
            /* unix - linux */
            setlocale(LC_MESSAGES, $locale);
        } else {
            /* windows */
            if (!putenv("LC_ALL={$locale}")) $this->writePutEnvError("LC_ALL");
            if (!putenv("LANG={$locale}")) $this->writePutEnvError("LANG");
            if (!putenv("LANGUAGE={$locale}")) $this->writePutEnvError("LANGUAGE");
            if (!putenv("LC_MESSAGES={$locale}")) $this->writePutEnvError("LC_MESSAGES");
        }
        if (!bindtextdomain($this->textDomain, $this->textDomainDirectory)) {
            echo "bindtextdomain returned false\n";
        }
        if (empty(textdomain($this->textDomain))) {
            echo "textdomain returned no value\n";
        }
    }

    private function writePutEnvError(string $varname) : void
    {
        echo "putenv returned false while setting " . $varname . "\n";
    }

    public function testEnglish() : void
    {
        $locale = "en_US";
        $this->setEnvironment($locale);
        self::assertEquals("Hello", gettext("hello"));
    }

    public function testFrench() : void
    {
        $locale = "fr_FR";
        $this->setEnvironment($locale);
        self::assertEquals("Bonjour", gettext("hello"));
    }

    public function testFrenchPlural() : void
    {
        $locale = "fr_FR";
        $this->setEnvironment($locale);
        self::assertEquals("jour", ngettext("day", "days", 1));
        self::assertEquals("jours", ngettext("day", "days", 2));
        self::assertEquals("beaucoup de jours", ngettext("day", "days", 3));
    }

    public function testFrenchUnknownPluralizationIndex() : void
    {
        $locale = "fr_FR";
        $this->setEnvironment($locale);
        // there are only 3 pluralizations, so 5 is an invalid index
        self::assertEquals("jour", ngettext("day", "days", 5));
    }

    public function testNoSuchMessageStringSingular() : void
    {
        $locale = "en_US";
        $this->setEnvironment($locale);
        /* returns the argument to gettext */
        self::assertEquals("Goodbye", gettext("Goodbye"));
    }

    public function testNoSuchMsgStringPlural() : void
    {
        $locale = "fr_FR";
        $this->setEnvironment($locale);
        self::assertEquals("night", ngettext("night", "nights", 1));
        self::assertEquals("nights", ngettext("night", "nights", 2));
        self::assertEquals("nights", ngettext("night", "nights", 3));
    }
}
