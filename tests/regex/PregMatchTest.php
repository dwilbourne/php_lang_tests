<?php
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version 1.0
 */

namespace tests\regex;


use PHPUnit\Framework\TestCase;

class PregMatchTest extends TestCase
{
    function testPregMatchNegation()
    {
        $pattern = '/^[^a-zA-Z_\x80-\xff]$/';
        $subject = "##";
        // preg_match returns 0 if the subject does not match the pattern, 1 if it does.
        // this subject matches, e.g. # is not in the character class [a-zA-Z_\x80-\xff]
        self::assertEquals(1, preg_match($pattern, $subject));
    }

    public function countMessageVariables(string $messageTemplate): int
    {
        /**
         * starts with '${', character class includes any combo of characters except '}' (at least one), finishes
         * with a '}'.
         *
         * Thought about restricting the characters in the character class to those that can be in a legitimate
         * variable name, but PHP itself would kick those out with a
         * compile error when you created the exception with bad dummy variable names, so we can afford to slack here
         * a little bit.....
         *
         * preg_match_all returns the number of matches found.
         */
        $regex = '/\$\{[^}]+}/';
        return preg_match_all($regex, $messageTemplate);
    }

    protected function dataProvider() : array
    {
        return [
            'messageWithNoParameters' => ['This is a test message', 0],
            'messageWithOneParameter' => ['Your function parameter ${param} is invalid.', 1],
            'messageWithTwoParameters' => ['preg match failed.  regex = ${regex}, subject = ${subject}', 2],
            'messageWithThreeParameters' => ['preg replace failed.  regex = ${regex}, subject = ${subject}, replace = ${replace}', 3],
            'messageWithMalformedParameter' => ['Your function parameter ${param is invalid.', 0],
        ];
    }

    /**
     * @function testCountXMessageVariables
     * @param string $message
     * @param int $expectNumParameters
     * @dataProvider dataProvider
     */
    public function testCountXMessageVariables(string $message, int $expectNumParameters): void
    {
        self::assertEquals($expectNumParameters, $this->countMessageVariables($message));
    }

    protected function dataProviderGetVariables() : array
    {
        return [
            'messageWithNoParameters' => ['This is a test message', []],
            'messageWithOneParameter' => ['Your function parameter ${param} is invalid.', ['param']],
            'messageWithTwoParameters' => ['preg match failed.  regex = ${regex}, subject = ${subject}', ['regex', 'subject']],
            'messageWithThreeParameters' => ['preg replace failed.  regex = ${regex}, subject = ${subject}, replace = ${replace}', ['regex', 'subject', 'replace']],
            'messageWithMalformedParameter' => ['Your function parameter ${param is invalid.', []],
        ];
    }

    /**
     * testGetMessageVariables
     * @param string $messageTemplate
     * @param array $messageVariablesArray
     * @dataProvider dataProviderGetVariables
     */
    public function testGetMessageVariables(string $messageTemplate, array $messageVariablesArray): void
    {
        $regex = '/\$\{([^}]+)}/';
        $pregResult = preg_match_all($regex, $messageTemplate, $matches);
        self::assertEqualsCanonicalizing($messageVariablesArray, $matches[1]);
    }

}