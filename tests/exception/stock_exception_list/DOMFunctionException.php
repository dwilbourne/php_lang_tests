<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\err\stock;

/**
 * DOM exceptions are thrown from attempting illegal DOM functions / methods or calling them with bad arguments
 *
 *    function DomMethodCall($method_name) {
 *        try {
 *            $doc = new DOMDocument('1.0', 'utf-8');
 *            try {
 *                return $doc->$method_name();
 *            }
 *            catch (\DOMException) {
 *                throw new pvc\DOMFunctionException($method_name);
 *            }
 *
 *        }
 *        return new DOMElement($elementName);
 *    }
 */
class DOMFunctionException extends Exception
{
}
