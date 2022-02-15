<?php

declare(strict_types=1);

namespace Okashoi\PhpAttributesExample;

use PHPUnit\Framework\TestCase;
use TypeError;

class UserForDBTest
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
}

class UserWithNullablePropertyForDBTest
{
    use CanConvertFromDbRow;

    #[DB('id')]
    public int $id;

    #[DB('name')]
    public string $name;

    #[DB('rate')]
    public ?float $rate;

    #[DB('active_flag')]
    public bool $isActive;
}

class DBTest extends TestCase
{
    public function test_fromDbRow()
    {
        $row = ['id' => 1, 'name' => 'Alice', 'rate' => 0.5, 'active_flag' => 1];
        $user = UserForDBTest::fromDbRow($row);

        $this->assertSame(1, $user->id);
        $this->assertSame('Alice', $user->name);
        $this->assertEqualsWithDelta(0.5, $user->rate, 0.0);
        $this->assertSame(true, $user->isActive);
    }

    public function test_fromDbRow_throwsTypeErrorWhenRequiredColumnIsMissing()
    {
        $this->expectException(TypeError::class);
        $row = ['id' => 1, 'name' => 'Alice', 'active_flag' => 1];
        UserForDBTest::fromDbRow($row);
    }

    public function test_fromDbRow_setsNullWhenArbitraryColumnIsMissing()
    {
        $row = ['id' => 1, 'name' => 'Alice', 'active_flag' => 1];
        $user = UserWithNullablePropertyForDBTest::fromDbRow($row);

        $this->assertSame(1, $user->id);
        $this->assertSame('Alice', $user->name);
        $this->assertNull($user->rate);
        $this->assertSame(true, $user->isActive);
    }

    public function test_fromDbRow_doesntSetPropertyWhenExtraColumnExists()
    {
        $row = ['id' => 1, 'name' => 'Alice', 'rate' => 0.5, 'active_flag' => 1, 'password' => 'secret'];
        $user = UserForDBTest::fromDbRow($row);

        $this->assertSame(1, $user->id);
        $this->assertSame('Alice', $user->name);
        $this->assertEqualsWithDelta(0.5, $user->rate, 0.0);
        $this->assertSame(true, $user->isActive);
        $this->assertObjectNotHasAttribute('password', $user);
    }
}
