<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Okashoi\PhpAttributesExample\{JSON, CanConvertFromJson};

class User
{
    use CanConvertFromJson;

    #[JSON('id')]
    public int $id;

    #[JSON('name')]
    public string $name;

    #[JSON('rate')]
    public float $rate;

    #[JSON('isActive')]
    public bool $isActive;

    public function showProfile(): void
    {
        echo sprintf(
            'id:%d name:%s, rate:%.1f, status:%s' . PHP_EOL,
            $this->id,
            $this->name,
            $this->rate,
            $this->isActive ? 'active' : 'inactive'
        );
    }
}

$json = <<<'JSON'
{
  "id": 1,
  "name": "Alice",
  "rate": 0.5,
  "isActive": true
}
JSON;

$user = User::fromJson($json);

$user->showProfile(); // id:1 name:Alice, rate:0.5, status:active
