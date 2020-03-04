<?php

namespace App\Models;

use FryskeOranjekoeke\Model\Table;

class Examples extends Table
{
    protected $table = 'example';

    public function getByExample(string $example): array
    {
        return $this->select([
            'where' => [
                'example' => $name,
                'active' => 1
            ]
        ]);
    }
}
