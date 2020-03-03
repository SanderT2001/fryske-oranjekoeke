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
        'active'
    ];

    public $id;

    public $active;

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

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function __construct()
    {
        $this->setActive(true);
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
