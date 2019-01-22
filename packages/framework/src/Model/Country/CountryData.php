<?php

namespace Shopsys\FrameworkBundle\Model\Country;

class CountryData
{
    /**
     * @var string[]
     */
    public $name;

    /**
     * @var string|null
     */
    public $code;

    /**
     * @var bool[]
     */
    public $enabled;

    /**
     * @var int[]
     */
    public $priority;

    public function __construct()
    {
        $this->name = [];
        $this->enabled = [];
        $this->priority = [];
    }
}
