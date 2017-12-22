<?php

namespace MapleSyrupGroup\Search\Models\Merchants\Filters;

use MapleSyrupGroup\Search\Models\Merchants\WordFilter;

abstract class NameFilter implements WordFilter
{
    /**
     * @var array
     */
    protected $replacements = [];

    /**
     * MerchantNameFilter constructor.
     *
     * @param array $replacements
     */
    public function __construct(array $replacements = null)
    {
        $this->setReplacements($replacements);
    }

    /**
     * {@inheritdoc}
     */
    public function filter($name, array $options = null)
    {
        $this->validateFilterOptions($options);
        $replacements = $this->getReplacements($options['language']);

        $name = mb_strtolower(trim(str_replace(array_keys($replacements), array_values($replacements), $name)));

        return preg_replace('/[^a-z0-9-_. ]/', '', $name);
    }

    /**
     * @param string $language
     *
     * @return mixed
     */
    protected function getReplacements($language)
    {
        if (!array_key_exists($language, $this->replacements)) {
            throw new MissingFilterReplacementException("No replacement found for language: $language");
        }

        return $this->replacements[$language];
    }

    /**
     * @param array|null $replacements
     */
    protected function setReplacements($replacements)
    {
        if (is_array($replacements)) {
            $this->replacements = $replacements;
        }
    }

    /**
     * Check that filter options are set.
     *
     * @param array|null $options
     *
     * @return bool
     */
    public function validateFilterOptions(array $options = null)
    {
        if (!is_array($options) || empty($options['language'])) {
            throw new MissingFilterOptionException('Invalid option array: Language option not providers');
        }

        return true;
    }
}
