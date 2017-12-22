<?php

namespace MapleSyrupGroup\Search\Models\Merchants;

interface WordFilter
{
    /**
     * @param mixed $data
     * @param array $options
     *
     * @return mixed
     */
    public function filter($data, array $options = null);

    /**
     * @param array $options
     *
     * @return bool
     */
    public function validateFilterOptions(array $options);
}
