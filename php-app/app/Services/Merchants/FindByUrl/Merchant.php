<?php

namespace MapleSyrupGroup\Search\Services\Merchants\FindByUrl;

final class Merchant
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $urlName;

    /**
     * @var string
     */
    private $description;

    /**
     * @var array
     */
    private $links;

    /**
     * @param int    $id
     * @param string $name
     * @param string $urlName
     * @param string $description
     * @param array  $links
     */
    public function __construct($id, $name, $urlName, $description, array $links = [])
    {
        $this->id = (int)$id;
        $this->name = (string)$name;
        $this->urlName = (string)$urlName;
        $this->description = (string)$description;
        $this->links = $links;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->name;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getUrlName()
    {
        return $this->urlName;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return array
     */
    public function getLinks()
    {
        return $this->links;
    }
}
