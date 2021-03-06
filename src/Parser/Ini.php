<?php

namespace Noodlehaus\Parser;

use Noodlehaus\Exception\ParseException;
use Noodlehaus\Exception\UnsupportedFormatException;

/**
 * INI parser
 *
 * @package    Config
 * @author     Jesus A. Domingo <jesus.domingo@gmail.com>
 * @author     Hassan Khan <contact@hassankhan.me>
 * @author     Filip Š <projects@filips.si>
 * @link       https://github.com/noodlehaus/config
 * @license    MIT
 */
class Ini implements ParserInterface
{
    /**
     * {@inheritDoc}
     * Decodes an INI string as an array
     *
     * @throws ParseException If there is an error parsing the INI string
     */
    public function decode($config, $filename = null)
    {
        $data = @parse_ini_string($config, true);

        if (!$data) {
            $error = error_get_last();

            // parse_ini_string() may return NULL but set no error if the string contains no parsable data
            if (!is_array($error)) {
                $error["message"] = "No parsable content in string.";
            }

            $error["file"] = $filename;

            // if string contains no parsable data, no error is set, resulting in any previous error
            // persisting in error_get_last(). in php 7 this can be addressed with error_clear_last()
            if (function_exists("error_clear_last")) {
                error_clear_last();
            }

            throw new ParseException($error);
        }

        return $this->expandDottedKey($data);
    }

    /**
     * Expand array with dotted keys to multidimensional array
     *
     * @param array $data
     *
     * @return array
     */
    protected function expandDottedKey($data)
    {
        foreach ($data as $key => $value) {
            if (($found = strpos($key, '.')) !== false) {
                $newKey = substr($key, 0, $found);
                $remainder = substr($key, $found + 1);

                $expandedValue = $this->expandDottedKey([$remainder => $value]);
                if (isset($data[$newKey])) {
                    $data[$newKey] = array_merge_recursive($data[$newKey], $expandedValue);
                } else {
                    $data[$newKey] = $expandedValue;
                }
                unset($data[$key]);
            }
        }
        return $data;
    }

    /**
     * {@inheritDoc}
     * Encodes a configuration as an INI string
     */
    public function encode(array $config)
    {
        return $this->toINI($config);
    }

    /**
     * Converts array to INI string
     * @param array $arr    Array to be converted
     * @param array $parent Parent array
     *
     * @return string Converted array as INI
     *
     * @see https://stackoverflow.com/a/17317168/6523409/
     */
    protected function toINI(array $arr, array $parent = [])
    {
        $converted = '';
    
        foreach ($arr as $k => $v) {
            if (is_array($v)) {
                $sec = array_merge((array) $parent, (array) $k);

                $converted .= '[' . join('.', $sec) . ']' . PHP_EOL;
                $converted .= $this->toINI($v, $sec);

            } else {
                $converted .= $k . '=' . $v . PHP_EOL;
            }
        }

        return $converted;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSupportedExtensions()
    {
        return ['ini'];
    }
}
