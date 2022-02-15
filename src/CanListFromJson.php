<?php

declare(strict_types=1);

namespace Okashoi\PhpAttributesExample;

use JsonException;
use ReflectionClass;
use ReflectionProperty;
use UnexpectedValueException;

trait CanListFromJson
{
    /**
     * @return array<self>
     * @throws UnexpectedValueException
     */
    public static function listFromJson(string $json): array
    {
        try {
            $dataset = json_decode($json, true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            throw new UnexpectedValueException();
        }

        $reflection = new ReflectionClass(self::class);
        $properties = array_filter($reflection->getProperties(), function (ReflectionProperty $property) {
            return count($property->getAttributes(JSON::class)) > 0;
        });

        /** @var array<self> $instances */
        $instances = [];
        foreach ($dataset as $data) {
            $instance = new self();

            foreach ($properties as $property) {
                $attribute = $property->getAttributes(JSON::class)[0];

                $jsonPropertyName = $attribute->getArguments()[0];
                $type = $property->getType()->getName();
                $instancePropertyName = $property->getName();

                if (!isset($data[$jsonPropertyName])) {
                    $instance->$instancePropertyName = null;
                } elseif (str_contains($type, 'int')) {
                    $instance->$instancePropertyName = (int)$data[$jsonPropertyName];
                } elseif (str_contains($type, 'float')) {
                    $instance->$instancePropertyName = (float)$data[$jsonPropertyName];
                } elseif (str_contains($type, 'bool')) {
                    $instance->$instancePropertyName = (bool)$data[$jsonPropertyName];
                } else {
                    $instance->$instancePropertyName = $data[$jsonPropertyName];
                }
            }

            $instances[] = $instance;
        }

        return $instances;
    }
}
