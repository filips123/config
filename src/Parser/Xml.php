<?php

namespace Noodlehaus\Parser;

use Noodlehaus\Exception\ParseException;
use SimpleXMLElement;

/**
 * XML parser
 *
 * @package    Config
 * @author     Jesus A. Domingo <jesus.domingo@gmail.com>
 * @author     Hassan Khan <contact@hassankhan.me>
 * @author     Filip Š <projects@filips.si>
 * @link       https://github.com/noodlehaus/config
 * @license    MIT
 */
class Xml implements ParserInterface
{
    /**
     * {@inheritDoc}
     * Decodes an XML string as an array
     *
     * @throws ParseException If there is an error parsing the XML string
     */
    public function decode($config, $filename = null)
    {
        libxml_use_internal_errors(true);

        $data = simplexml_load_string($config, null, LIBXML_NOERROR);

        if ($data === false) {
            $errors      = libxml_get_errors();
            $latestError = array_pop($errors);
            $error       = [
                'message' => $latestError->message,
                'type'    => $latestError->level,
                'code'    => $latestError->code,
                'file'    => $filename,
                'line'    => $latestError->line,
            ];
            throw new ParseException($error);
        }

        $data = json_decode(json_encode($data), true);

        return $data;
    }

    /**
     * {@inheritDoc}
     * Encodes a configuration as an XML string
     */
    public function encode(array $config)
    {
        return $this->toXML($config);
    }

    /**
     * Converts array to XML string
     * @param array             $arr       Array to be converted
     * @param string            $rootElement I specified will be taken as root element
     * @param SimpleXMLElement  $xml         If specified content will be appended
     *
     * @return string Converted array as XML
     *
     * @see https://www.kerstner.at/2011/12/php-array-to-xml-conversion/
     */
    protected function toXML(array $arr, $rootElement = '<config/>', $xml = null)
    {
        if ($xml === null) {
            $xml = new SimpleXMLElement($rootElement);
        }

        foreach ($arr as $k => $v) {
            if (is_array($v)) {
                $this->toXML($v, $k, $xml->addChild($k));
            } else {
                $xml->addChild($k, $v);
            }
        }

        return $xml->asXML();
    }

    /**
     * {@inheritDoc}
     */
    public static function getSupportedExtensions()
    {
        return ['xml'];
    }
}
