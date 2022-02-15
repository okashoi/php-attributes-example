<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Okashoi\PhpAttributesExample\{JSON, CanListFromJson};

class User
{
    use CanListFromJson;

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
[
  {
    "id": 1,
    "name": "Alice",
    "rate": 0.5,
    "isActive": true
  },
  {
    "id": 2,
    "name": "Bob",
    "rate": 0.3,
    "isActive": true
  },
  {
    "id": 3,
    "name": "Carol",
    "rate": 0.7,
    "isActive": false
  }
]
JSON;

$users = User::listFromJson($json);

foreach ($users as $user) {
    $user->showProfile();
}
// id:1 name:Alice, rate:0.5, status:active
// id:2 name:Bob, rate:0.3, status:active
// id:3 name:Carol, rate:0.7, status:inactive
