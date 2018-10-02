<?php

namespace Noodlehaus\Parser;

use Exception;
use Symfony\Component\Yaml\Yaml as YamlParser;
use Noodlehaus\Exception\ParseException;

/**
 * YAML parser
 *
 * @package    Config
 * @author     Jesus A. Domingo <jesus.domingo@gmail.com>
 * @author     Hassan Khan <contact@hassankhan.me>
 * @author     Filip Š <projects@filips.si>
 * @link       https://github.com/noodlehaus/config
 * @license    MIT
 */
class Yaml implements ParserInterface
{
    /**
     * {@inheritDoc}
     * Decodes a YAML/YML string as an array
     *
     * @throws ParseException If If there is an error parsing the YAML string
     */
    public function decode($config, $filename = null)
    {
        try {
            $data = YamlParser::parse($config, YamlParser::PARSE_CONSTANT);
        } catch (Exception $exception) {
            throw new ParseException(
                [
                    'message'   => 'Error parsing YAML string',
                    'exception' => $exception,
                ]
            );
        }

        return $data;
    }

    /**
     * {@inheritDoc}
     * Encodes a configuration as an YAML/YML string
     */
    public function encode(array $config)
    {
        return YamlParser::dump($config);
    }

    /**
     * {@inheritDoc}
     */
    public static function getSupportedExtensions()
    {
        return ['yaml', 'yml'];
    }
}
