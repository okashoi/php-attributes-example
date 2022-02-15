<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Okashoi\PhpAttributesExample\{JSON, CanConvertToJson};

class UserWithNullableProperty implements  JsonSerializable
{
    use CanConvertToJson;

    public function __construct(
        #[JSON('id')]
        public int $id,
        #[JSON('name')]
        public string $name,
        #[JSON('rate')]
        public ?float $rate = null,
        #[JSON('isActive')]
        public bool $isActive,
    ) {
    }
}

class UserWithOmittableProperty implements  JsonSerializable
{
    use CanConvertToJson;

    public function __construct(
        #[JSON('id')]
        public int $id,
        #[JSON('name')]
        public string $name,
        #[JSON('rate', JSON::OPTION_OMIT_EMPTY)] // JSON attribute can take second argument
        public ?float $rate = null,
        #[JSON('isActive')]
        public bool $isActive,
    ) {
    }
}

$userWithNullableProperty = new UserWithNullableProperty(id: 1, name: 'Alice', isActive: true);

echo json_encode($userWithNullableProperty) . PHP_EOL; // {"id":1,"name":"Alice","rate":null,"isActive":true}
// property "rate" is present and its value is null.


$userWithOmittableProperty = new UserWithOmittableProperty(id: 1, name: 'Alice', isActive: true);

echo json_encode($userWithOmittableProperty) . PHP_EOL; // {"id":1,"name":"Alice","isActive":true}
// property "rate" is omitted (not present).
