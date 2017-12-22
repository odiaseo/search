<?php

namespace MapleSyrupGroup\Search\Services\IndexStatusTracker;

use InvalidArgumentException;
use MapleSyrupGroup\Search\Exceptions\ExceptionCodes;

trait StatusIdentifierTrait
{
    /**
     * @var int
     */
    protected $idLength = 8;

    /**
     * {@inheritdoc}
     */
    public function getUniqueIdentifier()
    {
        return hash('crc32', microtime() . uniqid(__CLASS__));
    }

    /**
     * @param string $identifier
     *
     * @return string mixed
     */
    protected function validateIdentifier($identifier)
    {
        $identifier = (string)$identifier;

        if (empty($identifier)) {
            return '';
        }

        if (strlen($identifier) === $this->getIdLength() || is_numeric($identifier)) {
            return strip_tags($identifier);
        }

        throw new InvalidArgumentException(
            sprintf('Invalid status id found: %s', $identifier),
            ExceptionCodes::CODE_INVALID_ARGUMENT
        );
    }

    /**
     * @return int
     */
    public function getIdLength()
    {
        return $this->idLength;
    }
}
