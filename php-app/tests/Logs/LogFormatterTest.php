<?php

namespace MapleSyrupGroup\Search\Logs;

use MapleSyrupGroup\Search\Logs\Stub\ObjectWithToString;

class LogFormatterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @group format
     */
    public function testFormatterCanNormalizeUnknowDataType()
    {
        $records = ['context' => new \Exception('', 100, new \Exception())];
        $logger  = new LogFormatter();
        $result  = $logger->normalizeUnknown($records);

        $this->assertNotEmpty(trim($result));
        $this->assertInternalType('string', $result);
        $this->assertSame('[unknown(array)]', $result);
    }

    /**
     * @group format
     */
    public function testFormatConformToRfc5424()
    {
        $date = new \DateTime();

        $records  = [
            'date'    => $date,
            'object'  => new \stdClass(),
            'float'   => 9.01,
            'integer' => 1,
            'array'   => [1, 2, 3],
        ];
        $expected = [
            'date'    => $date->format(Rfc5424Formatter::DATE_TIME_FORMAT),
            'object'  => '[object] (stdClass: {})',
            'float'   => 9.01,
            'integer' => 1,
            'array'   => [1, 2, 3],
        ];

        $logger = new LogFormatter();
        $result = $logger->normalize($records);
        $this->assertSame($expected, $result);
    }

    public function testFormatCanConvertObjectToJson()
    {
        $object    = new ObjectWithToString();
        $formatter = new LogFormatter();
        $result    = $formatter->objectToJson($object);
        $this->assertSame(sprintf('[object] (%s: %s)', get_class($object), (string)$object), $result);
    }

    public function testFormatCanConvertObjectWithNoToStringMethodToJson()
    {
        $object    = new \stdClass();
        $formatter = new LogFormatter();
        $result    = $formatter->objectToJson($object);
        $this->assertSame(sprintf('[object] (%s: %s)', get_class($object), json_encode($object)), $result);
    }
}
