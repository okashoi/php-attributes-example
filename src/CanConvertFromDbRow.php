<?php

declare(strict_types=1);

namespace Okashoi\PhpAttributesExample;

use ReflectionClass;

trait CanConvertFromDbRow
{
    public static function fromDbRow(array $row): self
    {
        $instance = new self();

        $reflection = new ReflectionClass($instance);
        foreach ($reflection->getProperties() as $property) {
            $attributes = $property->getAttributes(DB::class);
            if (count($attributes) === 0) {
                continue;
            }

            $columnName = $attributes[0]->getArguments()[0];
            $type = $property->getType()->getName();
            $propertyName = $property->getName();

            if (!isset($row[$columnName])) {
                $instance->$propertyName = null;
            } elseif(str_contains($type, 'int')) {
                $instance->$propertyName = (int)$row[$columnName];
            } elseif (str_contains($type, 'float')) {
                $instance->$propertyName = (float)$row[$columnName];
            } elseif (str_contains($type, 'bool')) {
                $instance->$propertyName = (bool)$row[$columnName];
            } else {
                $instance->$propertyName = $row[$columnName];
            }
        }

        return $instance;
    }
}
