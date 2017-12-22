<?php

namespace MapleSyrupGroup\Search\Services\Merchants\FindByUrl\Stubs;

use MapleSyrupGroup\Search\Services\Merchants\FindByUrl\Merchant;

class MerchantFactory
{
    const ID = 13;

    const NAME = 'Amazon';

    const URL_NAME = 'amazon';

    const DESCRIPTION = 'Foo bar baz.';

    const LINKS = ['amazon.com', 'amazon.co.uk'];

    /**
     * @return Merchant
     */
    public static function withDefaults()
    {
        return new Merchant(self::ID, self::NAME, self::URL_NAME, self::DESCRIPTION, self::LINKS);
    }

    /**
     * @param array $links
     *
     * @return Merchant
     */
    public static function withLinks(array $links)
    {
        return new Merchant(self::ID, self::NAME, self::URL_NAME, self::DESCRIPTION, $links);
    }
}