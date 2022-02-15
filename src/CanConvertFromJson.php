<?php

declare(strict_types=1);

namespace Okashoi\PhpAttributesExample;

use JsonException;
use ReflectionClass;
use UnexpectedValueException;

trait CanConvertFromJson
{
    /**
     * @throws UnexpectedValueException
     */
    public static function fromJson(string $json): self
    {
        try {
            $data = json_decode($json, true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            throw new UnexpectedValueException();
        }

        $instance = new self();

        $reflection = new ReflectionClass($instance);
        foreach ($reflection->getProperties() as $property) {
            $attributes = $property->getAttributes(JSON::class);
            if (count($attributes) === 0) {
                continue;
            }

            $jsonPropertyName = $attributes[0]->getArguments()[0];
            $type = $property->getType()->getName();
            $instancePropertyName = $property->getName();

            if (!isset($data[$jsonPropertyName])) {
                $instance->$instancePropertyName = null;
            } elseif(str_contains($type, 'int')) {
                $instance->$instancePropertyName = (int)$data[$jsonPropertyName];
            } elseif (str_contains($type, 'float')) {
                $instance->$instancePropertyName = (float)$data[$jsonPropertyName];
            } elseif (str_contains($type, 'bool')) {
                $instance->$instancePropertyName = (bool)$data[$jsonPropertyName];
            } else {
                $instance->$instancePropertyName = $data[$jsonPropertyName];
            }
        }

        return $instance;
    }
}
