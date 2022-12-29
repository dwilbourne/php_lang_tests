<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

namespace tests\error;

use ParseError;
use Throwable;

function FriendlyErrorType($type): string
{
    switch ($type) {
        case E_ERROR: // 1 //
            return 'E_ERROR';
        case E_WARNING: // 2 //
            return 'E_WARNING';
        case E_PARSE: // 4 //
            return 'E_PARSE';
        case E_NOTICE: // 8 //
            return 'E_NOTICE';
        case E_CORE_ERROR: // 16 //
            return 'E_CORE_ERROR';
        case E_CORE_WARNING: // 32 //
            return 'E_CORE_WARNING';
        case E_COMPILE_ERROR: // 64 //
            return 'E_COMPILE_ERROR';
        case E_COMPILE_WARNING: // 128 //
            return 'E_COMPILE_WARNING';
        case E_USER_ERROR: // 256 //
            return 'E_USER_ERROR';
        case E_USER_WARNING: // 512 //
            return 'E_USER_WARNING';
        case E_USER_NOTICE: // 1024 //
            return 'E_USER_NOTICE';
        case E_STRICT: // 2048 //
            return 'E_STRICT';
        case E_RECOVERABLE_ERROR: // 4096 //
            return 'E_RECOVERABLE_ERROR';
        case E_DEPRECATED: // 8192 //
            return 'E_DEPRECATED';
        case E_USER_DEPRECATED: // 16384 //
            return 'E_USER_DEPRECATED';
    }
    return "";
}

function errMsg(string $errorType, int $errorLevel): string
{
    return $errorType . " with level " . FriendlyErrorType($errorLevel) . " was issued" . PHP_EOL;
}

/**
 * myErrorHandler
 * @param int $errno The "error level", e.g. E_NOTICE, E_WARNING, etc.
 * @param string $errstr Message about how the error occurred
 * @param string $errfile
 * @param int $errline
 *
 * The documentation for the severity levels / error numbers does not really help clarify the ways in which errors
 * can be "grouped together" in order to understand them in more abstract ways.  The case statement in the error
 * handler below demonstrates how errors can be grouped into one of four types: notices, warnings, parse error,
 * and fatal errors.  Interestingly (to me anyway), a parse error is not "fatal" per se.  If there is a parse error
 * in the script itself, the script never starts to begin with (it is never alive, so it cannot die).  You can find
 * some comments out on the web to the effect that the scripting engine will "keep going" after a parse error.  That
 * is only true in the narrow case of having a string of php code "eval'd" in your script.  Eval will fail with a
 * parse error and the script will continue to execute.
 *
 * All the errors that start with E_USER.... are ones that are used with the built-in php function trigger_error.
 * PHP will never issue these errors.
 *
 * All the errors that start with E_CORE.... are ones that could be issued "at startup", when the core part of php is
 * being initialized.
 *
 * All errors that start with E_COMPILE.... are ones that could be issued by the Zend scripting engine.  If you know a
 * little about how compilers work, then you know that the first step in compilation is to parse the source code.
 * Parsing strips out comments and breaks the code up into symbols according to strict grammatical rules.  If you get
 * a parse error, it is a problem in the source code:  you have a syntax problem.  But if you get an E_COMPILE error,
 * then the problem is "internal" to php.
 *
 * Notices and warnings are not fatal in the sense that the script will continue to run.  The default error handler
 * in php will echo some text to the standard output (typically the screen) and then keep going.  Prior to PHP 7,
 * fatal errors were, well, fatal.  PHP would issue a message about what had happened and then die.  The script
 * stopped running and no further statements would be executed.  This made it difficult to debug fatal errors because
 * you could not get any context about what had just happened in your script.
 *
 * In PHP 7, fatal errors are now catchable using the try-throw-catch mechanism that was introduced for exceptions in
 * PHP 5. Note that "non-fatal errors" (notices / warnings) are not catchable.  But the fact that
 * fatal errors now are catchable "gives you options".  When you define a custom error handler, you tell php what
 * error levels you want to handle.  If you choose NOT to handle fatal errors in your error handler, you can still
 * catch them in your code.
 */

$myErrorHandler = function (int $errno, string $errstr, string $errfile, int $errline) {
    switch ($errno) {
        case E_NOTICE:
        case E_USER_NOTICE:
        case E_DEPRECATED:
        case E_USER_DEPRECATED:
        case E_STRICT:
            echo errMsg("notice", $errno);
            break;
        case E_WARNING:
        case E_CORE_WARNING:
        case E_COMPILE_WARNING:
        case E_USER_WARNING:
            echo errMsg("warning", $errno);
            break;
        case E_PARSE:
            echo errMsg("parse error", $errno);
            break;
        case E_ERROR:
        case E_CORE_ERROR:
        case E_COMPILE_ERROR:
        case E_USER_ERROR:
        case E_RECOVERABLE_ERROR:
            // exceptionCode should not be confused with error severity.  exceptionCode is the same as it is for other
            // exceptions.  Error severity / error number / error level is the E_WARNING, etc.
            $exceptionCode = 0;
            throw new \ErrorException($errstr, $exceptionCode, $errno, $errfile, $errline);
    }
};

error_reporting(E_ALL);
$oldErrorHandler = set_error_handler($myErrorHandler);

// this eval will produce a parse error and keep going
try {
    eval("echo 'toto' echo 'tata'");
} catch (ParseError $p) {
    echo $p->getMessage() . PHP_EOL;
}

// commenting this will create a parse error where the script never begins to be executed
// include ".\\fixture\ParseErrorFixture.php";

/*
 * generate E_WARNING by using a variable before setting its value
 */

echo $t;

try {
    trigger_error("(notice text)", E_USER_NOTICE);
    trigger_error("(warning_text)", E_USER_WARNING);
    trigger_error("(deprecation text)", E_USER_DEPRECATED);
} catch (Throwable $e) {
	// this block never gets executed because notices and warning are not catchable unless you convert
	// them to errorexceptions in your customer error handler.
}

// catch a fatal error which is converted to an errorexception by the error handler
try {
    trigger_error("(error text)", E_USER_ERROR);
} catch (\ErrorException $e) {
    echo "caught the error exception which was a conversion from the fatal error." . PHP_EOL;
}

// now restore the original error handler
set_error_handler($oldErrorHandler);

// now catch a fatal error slightly differently. "fho" is not defined and that is a fatal runtime error
try {
    fho();
} catch (Throwable $e) {
    echo "caught the fatal error." . PHP_EOL;
}

