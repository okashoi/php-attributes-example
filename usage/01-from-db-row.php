<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Okashoi\PhpAttributesExample\{DB, CanConvertFromDbRow};

class User
{
    use CanConvertFromDbRow;

    #[DB('id')]
    public int $id;

    #[DB('name')]
    public string $name;

    #[DB('rate')]
    public float $rate;

    #[DB('active_flag')]
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

//-----------------
// Expected Usage
//-----------------
// $pdo = new PDO(/* ... */);
// $stmt = $pdo->query('SELECT id, name, rate, active_flag FROM users WHERE id = 1');
// $row = $stmt->fetch(PDO::FETCH_ASSOC);
$row = ['id' => 1, 'name' => 'Alice', 'rate' => 0.5, 'active_flag' => 1];
$user = User::fromDbRow($row);

$user->showProfile(); // id:1 name:Alice, rate:0.5, status:active
