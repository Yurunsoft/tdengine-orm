<?php

declare(strict_types=1);

namespace Yurun\TDEngine\Orm;

use Yurun\TDEngine\Action\Sql\SqlResult;
use Yurun\TDEngine\Orm\Annotation\Tag;
use Yurun\TDEngine\Orm\Enum\DataType;
use Yurun\TDEngine\Orm\Meta\Meta;
use Yurun\TDEngine\Orm\Meta\MetaManager;
use Yurun\TDEngine\TDEngineManager;

/**
 * InfluxDB Model 基类.
 */
abstract class BaseModel implements \JsonSerializable
{
    /**
     * Meta.
     *
     * @var \Yurun\TDEngine\Orm\Meta\Meta
     */
    private $__meta;

    /**
     * 表名.
     *
     * @var string|null
     */
    protected $__table;

    public function __construct(array $data = [], ?string $table = null)
    {
        $this->__meta = $meta = static::__getMeta();
        foreach ($meta->getProperties() as $propertyName => $property)
        {
            if (isset($data[$propertyName]))
            {
                $this->$propertyName = $data[$propertyName];
            }
            elseif (isset($data[$property->name]))
            {
                $this->$propertyName = $data[$property->name];
            }
        }
        $this->__table = $table;
    }

    public function insert(): SqlResult
    {
        return self::batchInsert([$this]);
    }

    public static function createSuperTable(bool $ifNotExists = true): SqlResult
    {
        $meta = self::__getMeta();
        $tableAnnotation = $meta->getTable();
        $sql = 'CREATE TABLE ';
        if ($ifNotExists)
        {
            $sql .= 'IF NOT EXISTS ';
        }
        $fields = [];
        foreach ($meta->getFields() as $propertyName => $annotation)
        {
            $fields[] = ($annotation->name ?? $propertyName) . ' ' . $annotation->type . ($annotation->length > 0 ? ('(' . $annotation->length . ')') : '');
        }
        $sql .= $tableAnnotation->database . '.' . $tableAnnotation->name . ' (' . implode(',', $fields) . ')';

        $fields = [];
        foreach ($meta->getTags() as $propertyName => $annotation)
        {
            $fields[] = ($annotation->name ?? $propertyName) . ' ' . $annotation->type . ($annotation->length > 0 ? ('(' . $annotation->length . ')') : '');
        }
        $sql .= ' TAGS (' . implode(',', $fields) . ')';

        return TDEngineManager::getClient($tableAnnotation->client ?? null)->sql($sql);
    }

    public static function createTable(string $tableName, array $tags = [], bool $ifNotExists = true): SqlResult
    {
        $meta = self::__getMeta();
        $tableAnnotation = $meta->getTable();
        if (!$tableAnnotation->super)
        {
            return self::createSuperTable($ifNotExists);
        }
        $sql = 'CREATE TABLE ';
        if ($ifNotExists)
        {
            $sql .= 'IF NOT EXISTS ';
        }
        $sql .= $tableAnnotation->database . '.' . $tableName . ' USING ' . $tableAnnotation->database . '.' . $tableAnnotation->name . ' ';
        if ($tags)
        {
            if (array_is_list($tags))
            {
                $i = 0;
                $values = [];
                foreach ($meta->getTags() as $annotation)
                {
                    $values[] = self::parseValue($annotation->type, $tags[$i] ?? null);
                    ++$i;
                }
                if ($values)
                {
                    $sql .= 'TAGS (' . implode(',', $values) . ') ';
                }
            }
            else
            {
                $tagAnnotations = $meta->getTags();
                $propertiesByFieldName = $meta->getPropertiesByFieldName();
                $propertyNames = [];
                $values = [];
                foreach ($tags as $key => $value)
                {
                    if (isset($tagAnnotations[$key]))
                    {
                        $tagAnnotation = $tagAnnotations[$key];
                    }
                    elseif (isset($propertiesByFieldName[$key]) && $propertiesByFieldName[$key] instanceof Tag)
                    {
                        $tagAnnotation = $propertiesByFieldName[$key];
                    }
                    else
                    {
                        continue;
                    }
                    $propertyNames[] = $tagAnnotation->name ?? $key;
                    $values[] = self::parseValue($tagAnnotation->type, $value);
                }
                if ($values)
                {
                    $sql .= '(' . implode(',', $propertyNames) . ') TAGS (' . implode(',', $values) . ') ';
                }
            }
        }

        return TDEngineManager::getClient($tableAnnotation->client ?? null)->sql($sql);
    }

    /**
     * @param static[] $models
     */
    public static function batchInsert(array $models): SqlResult
    {
        $sql = 'INSERT INTO ';
        foreach ($models as $model)
        {
            $meta = $model::__getMeta();
            $tableAnnotation = $meta->getTable();
            if ($tableAnnotation->super)
            {
                if (null === $model->__table)
                {
                    throw new \RuntimeException('Table name cannot be null');
                }
                $sql .= $tableAnnotation->database . '.' . $model->__table . ' using ' . $tableAnnotation->database . '.' . $tableAnnotation->name;
                $tags = $tagValues = [];
                foreach ($meta->getTags() as $propertyName => $tagAnnotation)
                {
                    $tags[] = $tagAnnotation->name ?? $propertyName;
                    $tagValues[] = self::parseValue($tagAnnotation->type, $model->$propertyName);
                }
                if ($tags)
                {
                    $sql .= '(' . implode(',', $tags) . ') TAGS (' . implode(',', $tagValues) . ') ';
                }
            }
            $fields = $values = [];
            foreach ($meta->getFields() as $propertyName => $fieldAnnotation)
            {
                $fields[] = $fieldAnnotation->name ?? $propertyName;
                $values[] = self::parseValue($fieldAnnotation->type, $model->$propertyName);
            }
            if ($fields)
            {
                $sql .= '(' . implode(',', $fields) . ') VALUES (' . implode(',', $values) . ') ';
            }
        }

        return TDEngineManager::getClient(self::__getMeta()->getTable()->client ?? null)->sql($sql);
    }

    /**
     * 获取模型元数据.
     */
    public static function __getMeta(): Meta
    {
        return MetaManager::get(static::class);
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function &__get($name)
    {
        $methodName = 'get' . ucfirst($name);
        if (method_exists($this, $methodName))
        {
            $result = $this->$methodName();
        }
        else
        {
            $result = null;
        }

        return $result;
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return void
     */
    public function __set($name, $value)
    {
        $methodName = 'set' . ucfirst($name);

        $this->$methodName($value);
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function __isset($name)
    {
        return null !== $this->__get($name);
    }

    /**
     * @param string $name
     *
     * @return void
     */
    public function __unset($name)
    {
    }

    /**
     * 将当前对象作为数组返回.
     */
    public function toArray(): array
    {
        $result = [];
        foreach ($this->__meta->getProperties() as $propertyName => $_)
        {
            $result[$propertyName] = $this->__get($propertyName);
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function __getTable(): ?string
    {
        return $this->__table;
    }

    public function __settable(?string $table): self
    {
        $this->__table = $table;

        return $this;
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public static function parseValue(string $type, $value)
    {
        if (null === $value)
        {
            return 'NULL';
        }
        switch ($type)
        {
            case DataType::BINARY:
            case DataType::NCHAR:
                return '\'' . strtr($value, [
                    "\0"     => '\0',
                    "\n"     => '\n',
                    "\r"     => '\r',
                    "\t"     => '\t',
                    \chr(26) => '\Z',
                    \chr(8)  => '\b',
                    '"'      => '\"',
                    '\''     => '\\\'',
                    '_'      => '\_',
                    '%'      => '\%',
                    '\\'     => '\\\\',
                ]) . '\'';
            case DataType::BOOL:
                return $value ? 'true' : 'false';
        }

        return $value;
    }
}
