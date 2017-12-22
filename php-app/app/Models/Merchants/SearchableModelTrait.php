<?php

namespace MapleSyrupGroup\Search\Models\Merchants;

trait SearchableModelTrait
{

    /**
     * @param string $dirtyString
     *
     * @return string
     */
    public function cleanString($dirtyString)
    {
        return trim(
            preg_replace(
                '/\s+/',
                ' ',
                strip_tags(
                    html_entity_decode(
                        htmlspecialchars_decode(
                            str_replace(['&nbsp;', '†', '®'], ' ', (string) $dirtyString)
                        )
                    )
                )
            )

        );
    }

    /**
     * @param string $text
     *
     * @return string
     */
    public function encodeString($text)
    {
        return utf8_encode($this->cleanString($text));
    }
}
