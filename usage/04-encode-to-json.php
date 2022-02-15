<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Okashoi\PhpAttributesExample\{JSON, CanConvertToJson};

class User implements  JsonSerializable
{
    use CanConvertToJson;

    public function __construct(
        #[JSON('id')]
        public int $id,
        #[JSON('name')]
        public string $name,
        #[JSON('rate')]
        public float $rate,
        #[JSON('isActive')]
        public bool $isActive,
    ) {
    }
}

$user = new User(id: 1, name: 'Alice', rate: 0.5, isActive: true);

echo json_encode($user) . PHP_EOL; // {"id":1,"name":"Alice","rate":0.5,"isActive":true}
