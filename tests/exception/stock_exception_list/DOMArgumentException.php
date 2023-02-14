<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\err\stock;

use Exception;

/**
 * DOM exceptions are thrown from attempting illegal DOM functions / methods or calling them with bad arguments
 *
 * Example (using a regex class that is built into pvc):
 *
 *    function createElement($element_name) {
 *        $regex = new RegexXMLElementName();
 *        if (!$regex->Test($element_name)) {
 *            throw new pvc\DOMArgumentException($element_name, "DOMElement::__construct()");
 *        }
 *        return new DOMElement($element_name);
 *    }
 *
 */
class DOMArgumentException extends Exception
{
}
