<?php

declare(strict_types=1);

namespace Okashoi\PhpAttributesExample;

use ReflectionClass;

trait CanConvertToJson
{
    public function jsonSerialize(): array
    {
        $data = [];

        $reflection = new ReflectionClass($this);
        foreach ($reflection->getProperties() as $property) {
            $attributes = $property->getAttributes(JSON::class);
            if (count($attributes) === 0) {
                continue;
            }

            $jsonPropertyName = $attributes[0]->getArguments()[0];
            $options = $attributes[0]->getArguments()[1] ?? 0;
            $instancePropertyName = $property->getName();

            if ($options & JSON::OPTION_OMIT_EMPTY && empty($this->$instancePropertyName)) {
                continue;
            }

            $data[$jsonPropertyName] = $this->$instancePropertyName;
        }

        return $data;
    }
}
