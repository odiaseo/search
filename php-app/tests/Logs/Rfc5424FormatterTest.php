<?php

namespace MapleSyrupGroup\Search\Logs;

use Monolog\Logger;

class Rfc5424FormatterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @group format
     * @dataProvider logParameterProvider
     *
     * @param $define
     * @param $facility
     * @param $unicode
     * @param $data
     * @param $records
     * @param $expected
     */
    public function testFormatConformToRfc5424($define, $facility, $unicode, $data, $records, $expected)
    {
        if ($define) {
            define('PHP_WINDOWS_VERSION_BUILD', 1);
        }
        $logger = new Rfc5424Formatter(new LogFormatter(), $facility, $unicode, $data);
        $result = $logger->formatBatch($records);
        $this->assertSame($expected, trim($result));
    }

    /**
     * @group format
     * @expectedException \UnexpectedValueException
     */
    public function testExceptionIsThrownWithInvalidFacility()
    {
        new Rfc5424Formatter(new LogFormatter(), 999);
    }

    /**
     * @group format
     */
    public function testLogException()
    {
        $records = ['context' => new \Exception('', 100, new \Exception())];
        $logger  = new Rfc5424Formatter(new LogFormatter(), 'auth', true, false);
        $result  = $logger->format($records);
        $this->assertNotEmpty(trim($result));
        $this->assertContains('[object]', $result);
    }

    public function logParameterProvider()
    {
        $time    = new \DateTime();
        $result  = '%datetime% ' . gethostname() . ' %channel% ' . getmypid() . ' - - BOM %message%';
        $options = ['context' => ['term' => 'argos', 'amount' => 9.02]];

        $result2  = '<12>1 %datetime% ' . gethostname() . ' %channel% ' . getmypid() . ' - - %% %message%';
        $options2 = ['context' => ['%shop%' => 'merchant'], 'level' => Logger::WARNING, 'shop' => 'argos'];

        $result3 = '%datetime% ' . gethostname() . ' %channel% ' . getmypid() . ' - - %% %message%';

        $option4   = ['context' => ['date' => $time]];
        $formatted = $time->format(Rfc5424Formatter::DATE_TIME_FORMAT);
        $result4   = '%datetime% ' . gethostname() . ' %channel% ' . getmypid() . ' - - BOM %message% {"date":"' . $formatted . '"}';

        $option5 = ['context' => ['obj' => new \stdClass()]];

        $resource = fopen(sys_get_temp_dir(), 'r');
        $toLarge  = array_fill(0, 1001, 1);

        return [
            [0, LOG_USER, true, false, [$option4], $result4],
            [0, 'user', true, true, [[]], $result],
            [0, 'cron', true, false, [$options], $result . ' ' . json_encode($options['context'])],
            [0, LOG_USER, false, false, [$options2], $result2 . ' ' . json_encode(['argos' => 'merchant'])],
            [1, 'auth', false, true, [[]], $result3],
            [0, LOG_USER, false, false, [['context' => true, 'extra' => [1]]], $result3 . ' true [1]'],
            [0, LOG_USER, false, false, [['extra' => null]], $result3],
            [0, LOG_USER, true, false, [['context' => $resource]], $result . ' [resource] (stream)'],
            [
                0,
                LOG_USER,
                true,
                false,
                [['context' => $option5]],
                $result . ' {"context":{"obj":"[object] (stdClass: {})"}}',
            ],
            [
                0,
                LOG_USER,
                true,
                false,
                [['context' => $toLarge]],
                $result . ' {"...":"Over 1000 items, aborting normalization"}',
            ],
        ];
    }
}
