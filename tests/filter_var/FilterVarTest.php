<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace tests\filter_var;


use PHPUnit\Framework\TestCase;

/**
 * Class FilterVarTest
 */
class FilterVarTest extends TestCase {


    /**
     * testFilterVarValidateRegex
     * @param $input
     * @param $expectedResult
     * @dataProvider dataProvider
     */
    public function testFilterVarValidateRegex($input, $expectedResult)
    {
        $regex = '/^ab[1-5]$/';
        $options = array('options' => array('regexp' => $regex));
        // note that the return value is either false or the filtered data, so the result is not strictly boolean
        self::assertEquals($expectedResult, filter_var($input, FILTER_VALIDATE_REGEXP, $options));
    }

    public function dataProvider() {
        return [
            'basic success test' => ['ab3', 'ab3'],
            'basic fail test' => ['ev9', false]
            ];
    }

    /**
     * testFilterVarWithFlags
     * @param $input
     * @param $expectedResult
     * @dataProvider dataProviderInteger
     */
    public function testFilterVarWithFlags($input, $expectedResult) {
        // note that 'options' is one array fro specifying min / max and 'flags' is a separate array.  Collectively
        // these two arrays are referred to as 'options' which is confusing but true....
        $options = array('options' => array(
            'min_range' => 10,
            'max_range' => 100),
            'flags' => FILTER_FLAG_ALLOW_HEX | FILTER_FLAG_ALLOW_OCTAL
        );
        self::assertEquals($expectedResult, filter_var($input, FILTER_VALIDATE_INT, $options));
    }

    public function dataProviderInteger() {
        return [
          'basic integer test' => ['15', 15],
          'basic hex test' => ['0x30', 48],
          'basic octal test' => ['060', 48]
        ];
    }

}