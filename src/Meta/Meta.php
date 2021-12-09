<?php

declare(strict_types=1);

namespace Yurun\TDEngine\Orm\Meta;

use Doctrine\Common\Annotations\AnnotationReader;
use ReflectionClass;
use Yurun\TDEngine\Orm\Annotation\Field;
use Yurun\TDEngine\Orm\Annotation\Table;
use Yurun\TDEngine\Orm\Annotation\Tag;

class Meta
{
    /**
     * 注解读取器.
     *
     * @var \Doctrine\Common\Annotations\AnnotationReader
     */
    private static $reader;

    /**
     * @var Table
     */
    private $table;

    /**
     * 属性列表.
     *
     * @var Tag[]|Field[]
     */
    private $properties;

    /**
     * 字段名属性列表.
     *
     * @var Tag[]|Field[]
     */
    private $propertiesByFieldName;

    /**
     * 标签列表.
     *
     * @var Tag[]
     */
    private $tags;

    /**
     * 字段列表.
     *
     * @var Field[]
     */
    private $fields;

    public function __construct(string $className)
    {
        if (null === self::$reader)
        {
            self::$reader = new AnnotationReader();
        }
        $refClass = new ReflectionClass($className);
        /** @var Table|null $table */
        $table = self::$reader->getClassAnnotation($refClass, Table::class);
        if (!$table)
        {
            throw new \RuntimeException('%s must have @Table');
        }
        $this->table = $table;
        $fields = $tags = $properties = $propertiesByFieldName = [];
        foreach ($refClass->getProperties() as $refProperty)
        {
            $propertyName = $refProperty->getName();
            foreach (self::$reader->getPropertyAnnotations($refProperty) as $annotation)
            {
                if ($annotation instanceof Field)
                {
                    $annotation->type = strtoupper($annotation->type);
                    $fields[$propertyName] = $properties[$propertyName] = $propertiesByFieldName[$annotation->name ?? $propertyName] = $annotation;
                }
                if ($annotation instanceof Tag)
                {
                    $annotation->type = strtoupper($annotation->type);
                    $tags[$propertyName] = $properties[$propertyName] = $propertiesByFieldName[$annotation->name ?? $propertyName] = $annotation;
                }
            }
        }
        $this->fields = $fields;
        $this->tags = $tags;
        $this->properties = $properties;
        $this->propertiesByFieldName = $propertiesByFieldName;
    }

    public function getTable(): Table
    {
        return $this->table;
    }

    public function setTable(Table $table): self
    {
        $this->table = $table;

        return $this;
    }

    /**
     * @return Tag[]|Field[]
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * @param Tag[]|Field[] $properties
     */
    public function setProperties(array $properties): self
    {
        $this->properties = $properties;

        return $this;
    }

    /**
     * @return Tag[]|Field[]
     */
    public function getPropertiesByFieldName(): array
    {
        return $this->propertiesByFieldName;
    }

    /**
     * @param Tag[]|Field[] $propertiesByFieldName
     */
    public function setPropertiesByFieldName(array $propertiesByFieldName): self
    {
        $this->propertiesByFieldName = $propertiesByFieldName;

        return $this;
    }

    /**
     * @return Tag[]
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param Tag[] $tags 标签列表
     */
    public function setTags(array $tags): self
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * @return Field[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @param Field[] $fields 字段列表
     */
    public function setFields(array $fields): self
    {
        $this->fields = $fields;

        return $this;
    }
}
