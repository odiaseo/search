<?php

namespace MapleSyrupGroup\Search\Models\Merchants\Filters;

use MapleSyrupGroup\Search\Models\Merchants\WordFilter;

class StopWordFilter implements WordFilter
{
    /**
     * @var array
     */
    private static $stopWords = [];

    /**
     * StopWordFilter constructor.
     *
     * @param array $stopWords
     */
    public function __construct(array $stopWords)
    {
        $this->setStopWords($stopWords);
    }

    /**
     * {@inheritdoc}
     *
     * @return mixed
     */
    public function filter($data, array $options = null)
    {
        $cleanUp = function (&$text) use ($options) {
            $text = $this->removeWord(self::getStopWords($options['language'], $options['field']), $text);
            //remove price information
            $text = preg_replace('/\£\s?[\d,]+(\.\d+)?/', '', $text);

            //replace invalid characters with space
            $text = preg_replace('/[^a-z0-9\-\%\.]+/i', ' ', strtolower($text));

            //Remove double spaces, trim invalid chars
            $text = trim(preg_replace('/\s+/', ' ', $text), ' .-"');
        };

        $this->validateFilterOptions($options);

        array_walk($data, $cleanUp);

        return $data;
    }

    /**
     * @param array $stopWords
     * @param string $text
     *
     * @return mixed
     */
    private function removeWord(array $stopWords, $text)
    {
        foreach ($stopWords as $word) {
            //remove individual stop words
            $text = preg_replace(sprintf('/\b(%s|\')\b/i', $word), '', $text);
        }

        return $text;
    }

    /**
     * Check that filter options are set.
     *
     * @param array $options | null
     *
     * @return bool
     */
    public function validateFilterOptions(array $options = null)
    {
        if (!$options || empty($options['language'])) {
            throw new MissingFilterOptionException('Language option not providers');
        }

        if (empty($options['field'])) {
            throw new MissingFilterOptionException('field option not providers');
        }

        return true;
    }

    /**
     * Get stop words for the specified language  and field if set.
     *
     * @param string $language
     * @param string $field
     *
     * @return array
     */
    private static function getStopWords($language, $field)
    {
        if (isset(self::$stopWords[$language][$field])) {
            return self::$stopWords[$language][$field];
        }

        return [];
    }

    /**
     * @param $stopWords
     */
    private static function setStopWords($stopWords)
    {
        self::$stopWords = $stopWords;
    }
}
