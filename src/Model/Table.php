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
            'WHERE' => [
                'id' => $id
            ]
        ];
        foreach ($this->getMagicSelectConditions() as $rule => $value) {
            if (isset($conditions[$rule])) {
                $conditions[$rule] = array_merge($conditions[$rule], $value);
            }
        }
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
                if (isset($selectConditions['WHERE'])) {
                    $selectConditions['WHERE'] = array_merge($selectConditions['WHERE'], [
                        'id' => $data->{$relSettings['relationship']['foreignKey']}
                    ]);
                }

                $relationResults = $this->{$tableName}->select($selectConditions);
                $results[$id]->{$tableName} = $relationResults;
            }
        }
        return $this->afterSelect($results);
    }

    public function add(Entity $entity): bool
    {
        return $this->insert($this->beforeSave($entity));
    }

    public function addMultiple(array $entities): bool
    {
        $saveData = [];
        foreach ($entities as $entity) {
            if ($entity instanceof Entity === false) {
                continue;
            }

            $saveData[] = $this->beforeSave($entity);
        }

        if ($saveData === []) {
            return true;
        }
        return $this->insertMultiple($saveData);
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
        // Require fresh requirements if not present..
        $required = ($target->required ?? (new $target)->required);

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
            return [];
        }
        $saveArray = (array) $entity;
        $saveArray = $this->stripSystemKeys($saveArray);
        return $saveArray;
    }

    /**
     * Function that is invoked after a FryskeOranjekoeke\Model\Table::select().
     *
     * @param array $rows Containing the fetched rows.
     *
     * @return array
     */
    protected function afterSelect(array $rows): array
    {
        $rows = $this->unsetRuntimeProperties($rows);
        return $rows;
    }

    protected function stripSystemKeys(array $entity): array
    {
        unset($entity['required']);
        return $entity;
    }

    // @TODO (Sander) Docs
    protected function unsetRuntimeProperties(array $rows): array
    {
        foreach ($rows as &$row) {
            unset($row->required);
        }
        return $rows;
    }
}
