<?php

namespace App\Models\Entities;

use FryskeOranjekoeke\Model\Entity;

class Example extends Entity
{
    public $required = [
    ];

    public $example;

    public function getExample(): ?string
    {
        return $this->example;
    }

    public function setExample(string $example): void
    {
        $this->example = $example;
    }
}
