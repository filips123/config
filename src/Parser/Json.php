<?php

namespace Noodlehaus\Parser;

use Noodlehaus\Exception\ParseException;

/**
 * JSON parser
 *
 * @package    Config
 * @author     Jesus A. Domingo <jesus.domingo@gmail.com>
 * @author     Hassan Khan <contact@hassankhan.me>
 * @author     Filip Š <projects@filips.si>
 * @link       https://github.com/noodlehaus/config
 * @license    MIT
 */
class Json implements ParserInterface
{
    /**
     * {@inheritDoc}
     * Decodeds a JSON string as an array
     *
     * @throws ParseException If there is an error parsing the JSON string
     */
    public function decode($config, $filename = null)
    {
        $data = json_decode($config, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $error_message  = 'Syntax error';
            if (function_exists('json_last_error_msg')) {
                $error_message = json_last_error_msg();
            }

            $error = [
                'message' => $error_message,
                'type'    => json_last_error(),
                'file'    => $filename,
            ];
            throw new ParseException($error);
        }

        return $data;
    }

    /**
     * {@inheritDoc}
     * Encodes a configuration as an JSON string
     */
    public function encode(array $config)
    {
        return json_encode($config);
    }

    /**
     * {@inheritDoc}
     */
    public static function getSupportedExtensions()
    {
        return ['json'];
    }
}
