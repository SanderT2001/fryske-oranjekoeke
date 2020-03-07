<?php

namespace FryskeOranjekoeke\Model;

/**
 * The PDO Connection Class containing functions to be able to connect to the Database and containing
 *   some convenience functions/wrappers that make it easier to work with PDO Queries.
 *
 * @author Sander Tuinstra <sandert2001@hotmail.com>
 */
class PDOConnection
{
    /**
     * The created PDO connection.
     *
     * @var PDO
     */
    protected $connection = null;

    /**
     * The prefix to add to every Table name for queries.
     *
     * @var string
     */
    protected $tablePrefix = null;

    /**
     * The name of the Table to query.
     *
     * @var string
     */
    protected $tableName = null;

    /**
     * The path of the Entity for this Table
     */
    protected $entityPath = null;

    public function getConnection()
    {
        return $this->connection;
    }

    public function setConnection($connection)
    {
        if ($connection instanceof \PDO === false) {
            throw new \InvalidArgumentException('Connection parameter is not an instance of PDO');
        }
        $this->connection = $connection;
    }

    public function getTablePrefix(): string
    {
        return (($this->tablePrefix === null) ? '' : $this->tablePrefix);
    }

    public function setTablePrefix(string $prefix): void
    {
        $this->tablePrefix = $prefix;
    }

    public function getTableName(): string
    {
        if ($this->tableName === null) {
            throw new \InvalidArgumentException('No table given to query.');
        }
        return ($this->getTablePrefix() . $this->tableName);
    }

    public function setTableName(string $name): void
    {
        $this->tableName = $name;
    }

    public function setEntityPath(string $path): void
    {
        $this->entityPath = $path;
    }

    public function getEntityPath(): string
    {
        return $this->entityPath;
    }

    public function __construct(
        string $dbname,
        string $table,
        string $host     = 'localhost',
        string $username = 'root',
        string $password = 'root',
        string $prefix   = null
    ) {
        $connectionString = 'mysql:host=$host;dbname=$dbname';
        $connectionString = strtr($connectionString, [
            '$host'   => $host,
            '$dbname' => $dbname
        ]);
        $this->setConnection(new \PDO($connectionString, $username, $password));
        $this->getConnection()
             ->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $this->setTableName($table);
        if ($prefix !== null) {
            $this->setTablePrefix($prefix);
        }
    }

    /**
     * Builds a SELECT query with the conditions from @param conditions.
     *
     * @param array conditions
     *
     * @return array Containing the found rows.
     */
    protected function select(array $conditions): array
    {
        $query         = 'SELECT $fields FROM $table $relations $conditions';
        $fields        = '*';
        $relations     = '';
        $conditionsStr = '';
        foreach ($conditions as $name => $subconditions) {
            if (empty($subconditions)) {
                continue;
            }

            switch ($name) {
                case 'fields':
                    $fields = '';
                    $fieldsCount = 0;
                    foreach ($subconditions as $field) {
                        if ($fieldsCount > 0) {
                            $fields .= ',';
                        }
                        $fields .= $field;
                        $fieldsCount++;
                    }
                    continue;
                    break;

                case 'JOIN':
                    $joinCount = 0;
                    foreach ($subconditions as $joinConditions) {
                        if ($joinCount > 0) {
                            $relations .= (' ' . 'AND' . ' ');
                        }
                        $relations .= ($joinConditions['type'] . ' ' . 'JOIN');
                        $relations .= (' ' . $joinConditions['table']);
                        $relations .= (' ' . 'ON');
                        $relations .= ' ';

                        foreach ($joinConditions['fieldRelations'] as $srcField => $destField) {
                            $relations .= ($srcField . '=' . $destField);
                        }
                        $joinCount++;
                    }
                    continue;
                    break;

                default:
                    $conditionsStr = (' ' . $name);
                    $subconditionsCount = 0;
                    foreach ($subconditions as $field => $value) {
                        if ($subconditionsCount > 0) {
                            $conditionsStr .= ' AND';
                        }
                        $conditionsStr .= (' ' . $field . '=' . '"' . $value . '"');
                        $subconditionsCount++;
                    }
            }
        }
        $query = strtr($query, [
            '$table'      => $this->getTableName(),
            '$fields'     => $fields,
            '$relations'  => $relations,
            '$conditions' => $conditionsStr
        ]);

        $rows = $this->getConnection()
                     ->query($query)
                     ->fetchAll(\PDO::FETCH_CLASS, $this->getEntityPath());
        return $this->afterSelect($rows);
    }

    protected function insert(array $data): bool
    {
        $query = 'INSERT INTO $table (';
        unset($data['id']);

        $count = 0;
        foreach ($data as $field => $value) {
            if ($count > 0) {
                $query .= ', ';
            }
            $query .= $field;
            $count++;
        }
        $query .= ') VALUES (';

        $count = 0;
        foreach ($data as $field => $value) {
            if ($count > 0) {
                $query .= ', ';
            }
            $query .= (':' . $field);
            $count++;
        }
        $query .= ')';

        $query = strtr($query, [
            '$table'  => $this->getTableName()
        ]);
        return $this->getConnection()->prepare($query)->execute($data);
    }

    protected function update(int $id, array $data): bool
    {
        $query = 'UPDATE $table SET';
        unset($data['id']);

        $sqldata = [];

        $count = 0;
        foreach ($data as $field => $value) {
            if ($count > 0) {
                $query .= ',';
            }
            $query .= (' ' . $field . '=:' . $field);
            $sqldata[$field] = $value;
            $count++;
        }
        $query .= (' WHERE id=:id');
        $sqldata['id'] = $id;
        $query = strtr($query, [
            '$table' => $this->getTableName()
        ]);
        return $this->getConnection()->prepare($query)->execute($sqldata);
    }

    protected function delete(array $conditions): bool
    {
        $query = 'DELETE FROM $table WHERE';
        foreach ($conditions as $name => $value) {
            $query .= (' '.$name . '=' . $value);
        }
        $query = strtr($query, [
            '$table' => $this->getTableName()
        ]);
        $prepared = $this->getConnection()->prepare($query);

        $success = true;
        try {
            $prepared->execute();
        } catch (\Exception $e) {
            $success = false;
        }
        return $success;
    }

    /**
     * Function that is invoked after an Fetch/Select Query.
     *
     * @param array $rows Containing the fetched rows.
     *
     * @return array
     */
    protected function afterSelect(array $rows): array
    {
        $rows = $this->setIdAsPK($rows);
        $rows = $this->unsetRuntimeProperties($rows);
        return $rows;
    }

    /**
     * Sets the Primary Key of an element in the @param $rows as the output/array key.
     *
     * @param array $rows
     *
     * @return array
     */
    protected function setIdAsPK(array $rows): array
    {
        $output = [];
        foreach ($rows as $row) {
            $output[$row->id] = $row;
        }
        return $output;
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
