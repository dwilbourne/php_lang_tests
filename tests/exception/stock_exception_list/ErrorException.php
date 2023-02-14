<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\err\stock;

/**
 * Used to convert errors to exceptions.  Would be nice not to have to use this object but there is a case where
 * the set_error_handler routine in PHP uses a callback where the argument list to the callback is $code, $msg,
 * $file, $line and is not an error object. Unfortunately, the error constructor does not allow one to set the file
 * and line from the constructor, so there is no way to handle uncaught errors other than convert them to
 * ErrorExceptions where the constructor does allow one to specify the file and line number.
 */

/**
 * Class ErrorException
 */
class ErrorException extends Exception
{
}
