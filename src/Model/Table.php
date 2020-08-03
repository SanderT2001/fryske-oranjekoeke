<?php

namespace FryskeOranjekoeke\Model;

use pdohelper\PDOHelper;

// @TODO Update docs
class Table extends PDOHelper
{
    /**
     * The Entity connected with the Table.
     *
     * @var Entity
     */
    private $entity = null;

    /**
     * @var array
     */
    //protected $relationships = [];

    public function __construct()
    {
        parent::__construct(
            APP_CONFIG['database']['host'],
            APP_CONFIG['database']['name'],
            APP_CONFIG['database']['username'],
            APP_CONFIG['database']['password'],
            APP_CONFIG['database']['table_prefix']
        );

        // Set Entity if not given already.
        if ($this->getTable() !== null) {
            $this->setTable($this->getTable());
            // @TODO (Sander) Pluralize
            $this->setEntity('Test');
        }

        /**
        foreach ($this->getRelationships() as $tableName => $relSettings) {
            $this->{$tableName} = get_app_class('model', $tableName);
        }
         */
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

    /**
    public function getRelationships(): array
    {
        return $this->relationships;
    }
     */

    /**
     * Gets a single record by its @param id (Primary Key).
     *
     * @param int $id
     *
     * @return Entity|null
     */
    public function get(int $id): ?Entity
    {
        $query = $this->getBuilder()
                      ->select()
                      ->columns('*')
                      ->where([
                          'id' => $id
                      ])
                      ->getQuery();

        $result = $this->execute($query);
        $result = $this->afterSelect($result);
        return $result[key($result)] ?? null;
    }

    /**
     * Convenience function for getting all the records from the table.
     */
    public function getAll(): array
    {
        $query = $this->getBuilder()
                      ->select()
                      ->columns('*')
                      ->getQuery();

        $result = $this->execute($query);
        $result = $this->afterSelect($result);
        return $result;
    }

    public function add($entities): bool
    {
        if (is_object($entities))
            $entities = [$entities];

        $saveData = [];
        foreach ($entities as $key => $entity) {
            if ($entity instanceof Entity === false) {
                continue;
            }

            $saveData[] = $this->prepareSave($entity);
        }

        if ($saveData === []) {
            return true;
        }

        $query = $this->getBuilder()
                      ->insert($saveData)
                      ->getQuery();
        return $this->execute($query);
    }

    public function edit(Entity $entity): bool
    {
        $saveData = $this->prepareSave($entity);

        $query = $this->getBuilder()
                      ->update($saveData)
                      ->where([
                          'id' => $entity->getId()
                      ])
                      ->getQuery();
        return $this->execute($query);
    }

    public function remove(int $id): bool
    {
        $query = $this->getBuilder()
                      ->delete()
                      ->where([
                          'id' => $id
                      ])
                      ->getQuery();
        return $this->execute($query);
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

    protected function prepareSave(Entity $entity): array
    {
        if (!empty($this->getErrors($entity))) {
            return [];
        }
        $saveArray = (array) $entity;
        $saveArray = $this->stripSystemKeys($saveArray);
        //$saveArray = $this->stripRelationships($saveArray);
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
        unset($entity['types']);
        return $entity;
    }

    /**
    protected function stripRelationships(array $entity): array
    {
        foreach ($this->getRelationships() as $tableName => $relSettings) {
            unset($entity[$tableName]);
        }
        return $entity;
    }
     */

    protected function unsetRuntimeProperties(array $rows): array
    {
        foreach ($rows as &$row) {
            unset($row->required);
            unset($row->types);
        }
        return $rows;
    }
}
