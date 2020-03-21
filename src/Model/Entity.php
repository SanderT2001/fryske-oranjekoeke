<?php

namespace FryskeOranjekoeke\Model;

/**
 * The Base Entity.
 *
 * @author Sander Tuinstra <sandert2001@hotmail.com>
 */
class Entity
{
    public $required = [
    ];

    public $id = 0;

    public function getRequired(): array
    {
        return $this->required ?? [];
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function patch(\stdClass $data): bool
    {
        foreach ($data as $field => $value) {
            if ($field === 'id') {
                continue;
            }

            $this->{'set' . ucfirst($field)}($value);
        }
        return true;
    }
}
