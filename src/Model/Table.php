<?php

namespace FryskeOranjekoeke\Model;

use FryskeOranjekoeke\Model\PDOConnection;

/**
 * The Base Table with some convenience functions.
 *
 * @author Sander Tuinstra <sandert2001@hotmail.com>
 */
class Table extends PDOConnection
{
    /**
     * The name of the Table.
     *
     * @var string
     */
    protected $table = null;

    /**
     * @var array
     */
    protected $magicSelectConditions = [
    ];

    /**
     * The Entity connected with the Table.
     *
     * @var Entity
     */
    private $entity = null;

    /**
     * @var array
     */
    protected $relationships = [
    ];

    public function getTable(): ?string
    {
        return $this->table;
    }

    public function setTable(string $name): void
    {
        $this->table = $name;

        // Plural to Singular.
        $entity = rtrim($name, 's');
        $this->setEntity(ucfirst($entity));
    }

    public function getMagicSelectConditions(): array
    {
        return $this->magicSelectConditions;
    }

    public function getEntity(): ?Entity
    {
        return $this->entity;
    }

    public function setEntity(string $entity): void
    {
        $this->entity = get_app_class('entity', $entity);
        $this->setEntityPath(get_app_class('entity', $entity, true));
    }

    public function getRelationships(): array
    {
        return $this->relationships;
    }

    public function __construct()
    {
        parent::__construct(
            APP_CONFIG['database']['name'],
            $this->getTable(),
            APP_CONFIG['database']['host'],
            APP_CONFIG['database']['username'],
            APP_CONFIG['database']['password'],
            APP_CONFIG['database']['table_prefix']
        );

        // Set Entity if not given already.
        if ($this->getTable() !== null) {
            $this->setTable($this->getTable());
        }

        foreach ($this->getRelationships() as $tableName => $relSettings) {
            $this->{$tableName} = get_app_class('model', $tableName);
        }
    }

    /**
     * Gets a single record by its @param id (Primary Key).
     *
     * @param int $id
     *
     * @return Entity|null
     */
    public function get(int $id): ?Entity
    {
        $conditions = [
            'where' => [
                'id' => $id
            ]
        ];
        $conditions = array_merge($conditions, $this->getMagicSelectConditions());
        $row = $this->select($conditions);
        return ($row[key($row)] ?? null);
    }

    /**
     * Gets all the active records.
     *
     * @return array
     */
    public function getAll(): array
    {
        return $this->select($this->getMagicSelectConditions());
    }

    public function select(array $conditions): array
    {
        $results = parent::select($conditions);

        // No relations present to add? Then return early.
        if ($this->getRelationships() === []) {
            return $results;
        }

        foreach ($results as $id => $data) {
            foreach ($this->getRelationships() as $tableName => $relSettings) {
                $selectConditions = $this->{$tableName}->getMagicSelectConditions();
                if (isset($selectConditions)) {
                    $selectConditions['WHERE'] = array_merge($selectConditions['WHERE'], [
                        'id' => $data->{$relSettings['relationship']['foreignKey']}
                    ]);
                }

                $relationResults = $this->{$tableName}->select($selectConditions);
                $results[$id]->{$tableName} = $relationResults;
            }
        }
        return $results;
    }

    public function add(Entity $entity): bool
    {
        return $this->insert($this->beforeSave($entity));
    }

    public function edit(Entity $entity): bool
    {
        return $this->update($entity->getId(), $this->beforeSave($entity));
    }

    public function remove(int $id): bool
    {
        return $this->delete(['id' => $id]);
    }

    public function getErrors(Entity $target): array
    {
        $errors = [];
        $required = [];
        if (!isset($target->required)) {
            $tmp = new $target;
            $required = $tmp->required;
        }
        foreach ($required as $field) {
            if (!empty($target->{'get' . ucfirst($field)}())) {
                continue;
            }

            // Error
            $errors[$field] = 'Cannot be empty';
        }
        return $errors;
    }

    protected function beforeSave(Entity $entity): array
    {
        if (!empty($this->getErrors($entity))) {
            return false;
        }
        $saveArray = (array) $entity;
        $saveArray = $this->stripSystemKeys($saveArray);
        return $saveArray;
    }

    protected function stripSystemKeys(array $entity): array
    {
        unset($entity['required']);
        return $entity;
    }
}
