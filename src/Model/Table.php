<?php

namespace FryskeOranjekoeke\Model;

use pdohelper\PDOHelper;

class Table extends PDOHelper
{
    /**
     * The Entity connected with the Table.
     *
     * @var Entity
     */
    protected $entity = null;

    /**
     * The name of the Primary Key field in the Table.
     *
     * @var string
     */
    protected $pk = 'id';

    public function __construct()
    {
        parent::__construct(
            APP_CONFIG['database']['host'],
            APP_CONFIG['database']['name'],
            APP_CONFIG['database']['username'],
            APP_CONFIG['database']['password'],
            APP_CONFIG['database']['table_prefix']
        );

        $this->setDebug(APP_CONFIG['runtime']['debug']);

        // Set the Table Name based on the Class Name if no name is set
        if ($this->getTable() === null) {
            $className = get_class_name($this);
            $className = strtolower($className);
            $this->setTable($className);
        } else if (is_string($this->getTable())) {
            $this->setTable($this->getTable());
        }

        // Convert Entity name to the actual Entity Class
        if (is_string($this->entity))
            $this->setEntity($this->entity);
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

    public function getPK(): string
    {
        return $this->pk;
    }

    public function setPK(string $pk): self
    {
        $this->pk = $pk;
        return $this;
    }

    /**
     * Gets a single record by its @param id (Primary Key).
     *
     * @param int $id
     *
     * @return Entity|null
     */
    public function get(int $id, array $additional_conditions = []): ?Entity
    {
        $conditions = [
            $this->getPK() => $id
        ];
        $conditions = array_merge($conditions, $additional_conditions);

        $query = $this->getBuilder()
                      ->select()
                      ->columns('*')
                      ->where($conditions)
                      ->getQuery();

        $result = $this->execute($query);
        $result = $this->afterSelect($result);
        return $result[key($result)] ?? null;
    }

    /**
     * Convenience function for getting all the records from the table.
     */
    public function getAll(array $conditions = []): array
    {
        $query = $this->getBuilder()
                      ->select()
                      ->columns('*')
                      ->where($conditions)
                      ->getQuery();

        $result = $this->execute($query);
        $result = $this->afterSelect($result);
        return $result;
    }

    /**
     * @return bool false On failure
     *         int        ID of the created record
     */
    public function add(Entity $entity)
    {
        $saveData = $this->prepareSave($entity);
        if (empty($saveData))
            return false;

        $query = $this->getBuilder()
                      ->insert([$saveData])
                      ->getQuery();
        return $this->execute($query);
    }

    public function edit(Entity $entity): bool
    {
        $saveData = $this->prepareSave($entity);
        if (empty($saveData))
            return false;

        $query = $this->getBuilder()
                      ->update($saveData)
                      ->where([
                          $this->getPK() => $entity->getId()
                      ])
                      ->getQuery();
        return $this->execute($query);
    }

    public function remove(int $id): bool
    {
        $query = $this->getBuilder()
                      ->delete()
                      ->where([
                          $this->getPK() => $id
                      ])
                      ->getQuery();
        return $this->execute($query);
    }

    public function getErrors(Entity $target): array
    {
        $errors = [];

        // Check required properties
        $required = ($target->required ?? (new $target)->required);
        foreach ($required as $field) {
            if (isset($target->{$field}))
                continue;

            $errors[$field] = 'Must be set';
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

    protected function unsetRuntimeProperties(array $rows): array
    {
        foreach ($rows as &$row) {
            unset($row->required);
        }
        return $rows;
    }
}
