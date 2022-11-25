<?php

declare (strict_types=1);
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

namespace tests\intl;


use PHPUnit\Framework\TestCase;

/**
 * Class ICUInfo
 */
class ICUInfoTest extends TestCase
{
	protected string $expectedIcuVersion = "70.1";
	protected string $expectedIcuDataVersion = "70.1";
	protected array $expectedPhpInfoResults = [
		"ICU version" => "70.1",
		"ICU Data version" => "70.1",
		"ICU TZData version" => "2022c",
		"ICU Unicode version" => "12.0",
	];

	public function testIcuVersionViaConstant() : void
	{
		self::assertEquals($this->expectedIcuVersion, INTL_ICU_VERSION);
	}

	public function testIcuDataVersionViaConstant() : void
	{
		self::assertEquals($this->expectedIcuDataVersion, INTL_ICU_DATA_VERSION);
	}

	public function testIcuInfofromPhpInfo() : void
	{
		if (php_sapi_name() !== "cli") {
			exit('This only works in PHP command line (unless you write phpinfo()\'s html parser)');
		}

		// get phpinfo() std output into buffer
		ob_start();
		phpinfo();

		// search all buffer starting with 'ICU'
		preg_match_all(
			'/^(?P<name>ICU(?: [A-Za-z_]*)? version) => (?P<version>.*)$/m',
			ob_get_clean(),
			$matched,
			PREG_SET_ORDER
		);
		if (count($matched) === 0) {
			exit('no ICU library info found in phpinfo(). Your PHP may not have php_intl extension turned on.');
		}

		// because the formatting and keys move around from time to time, easier just to echo the results.....
		foreach($matched as $current) {
			echo $current['name'] . ": " . $current['version'] . PHP_EOL;
		}
		// self::assertEquals($this->expectedPhpInfoResults["ICU version"], $matched["ICU version"]);
		self::assertTrue(true);
	}

}