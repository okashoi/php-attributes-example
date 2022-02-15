<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Okashoi\PhpAttributesExample\{DB, JSON, CanConvertFromDbRow, CanConvertToJson};

class User implements JsonSerializable
{
    use CanConvertFromDbRow, CanConvertToJson;

    #[DB('id'), JSON('id')]
    public int $id;

    #[DB('name'), JSON('name')]
    public string $name;

    #[DB('rate'), JSON('rate')]
    public float $rate;

    #[DB('active_flag'), JSON('isActive')]
    public bool $isActive;
}

//-----------------
// Expected Usage
//-----------------
// $pdo = new PDO(/* ... */);
// $stmt = $pdo->query('SELECT id, name, rate, active_flag FROM users WHERE id = 1');
// $row = $stmt->fetch(PDO::FETCH_ASSOC);
$row = ['id' => 1, 'name' => 'Alice', 'rate' => 0.5, 'active_flag' => 1];
$user = User::fromDbRow($row);

echo json_encode($user) . PHP_EOL; // {"id":1,"name":"Alice","rate":0.5,"isActive":true}
