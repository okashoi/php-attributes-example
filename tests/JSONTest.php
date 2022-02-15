<?php

declare(strict_types=1);

namespace Okashoi\PhpAttributesExample;

use JsonSerializable;
use PHPUnit\Framework\TestCase;
use TypeError;

class UserForJSONTest implements JsonSerializable
{
    use CanConvertFromJson, CanConvertToJson, CanListFromJson;

    #[JSON('id')]
    public int $id;

    #[JSON('name')]
    public string $name;

    #[JSON('rate')]
    public float $rate;

    #[JSON('isActive')]
    public bool $isActive;

    public static function create(int $id, string $name, float $rate, bool $isActive): self
    {
        $user = new self();

        $user->id = $id;
        $user->name = $name;
        $user->rate  = $rate;
        $user->isActive = $isActive;

        return $user;
    }
}

class UserWithNullablePropertyForJSONTest implements JsonSerializable
{
    use CanConvertFromJson, CanConvertToJson;

    #[JSON('id')]
    public int $id;

    #[JSON('name')]
    public string $name;

    #[JSON('rate')]
    public ?float $rate;

    #[JSON('isActive')]
    public bool $isActive;

    public static function create(int $id, string $name, ?float $rate = null, bool $isActive): self
    {
        $user = new self();

        $user->id = $id;
        $user->name = $name;
        $user->rate  = $rate;
        $user->isActive = $isActive;

        return $user;
    }
}

class UserWithOmittablePropertyForJSONTest implements JsonSerializable
{
    use CanConvertToJson;

    #[JSON('id')]
    public int $id;

    #[JSON('name')]
    public string $name;

    #[JSON('rate', JSON::OPTION_OMIT_EMPTY)]
    public ?float $rate;

    #[JSON('isActive')]
    public bool $isActive;

    public static function create(int $id, string $name, ?float $rate = null, bool $isActive): self
    {
        $user = new self();

        $user->id = $id;
        $user->name = $name;
        $user->rate  = $rate;
        $user->isActive = $isActive;

        return $user;
    }
}

class JSONTest extends TestCase
{
    public function test_fromJson()
    {
        $json = <<<'JSON'
        {
          "id": 1,
          "name": "Alice",
          "rate": 0.5,
          "isActive": true
        }
        JSON;

        $user = UserForJSONTest::fromJson($json);

        $this->assertSame(1, $user->id);
        $this->assertSame('Alice', $user->name);
        $this->assertEqualsWithDelta(0.5, $user->rate, 0.0);
        $this->assertSame(true, $user->isActive);
    }

    public function test_listFromJson()
    {
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

        $users = UserForJSONTest::listFromJson($json);

        $this->assertCount(3, $users);

        $this->assertSame(1, $users[0]->id);
        $this->assertSame('Alice', $users[0]->name);
        $this->assertEqualsWithDelta(0.5, $users[0]->rate, 0.0);
        $this->assertSame(true, $users[0]->isActive);

        $this->assertSame(2, $users[1]->id);
        $this->assertSame('Bob', $users[1]->name);
        $this->assertEqualsWithDelta(0.3, $users[1]->rate, 0.0);
        $this->assertSame(true, $users[1]->isActive);

        $this->assertSame(3, $users[2]->id);
        $this->assertSame('Carol', $users[2]->name);
        $this->assertEqualsWithDelta(0.7, $users[2]->rate, 0.0);
        $this->assertSame(false, $users[2]->isActive);
    }

    public function test_fromJson_throwsExceptionWhenRequiredPropertyIsMissingInJson()
    {
        $this->expectException(TypeError::class);

        $json = <<<'JSON'
        {
          "id": 1,
          "name": "Alice",
          "isActive": true
        }
        JSON;

        UserForJSONTest::fromJson($json);
    }

    public function test_fromJson_setsNullWhenArbitraryPropertyIsMissingInJson()
    {
        $json = <<<'JSON'
        {
          "id": 1,
          "name": "Alice",
          "isActive": true
        }
        JSON;

        $user = UserWithNullablePropertyForJSONTest::fromJson($json);

        $this->assertSame(1, $user->id);
        $this->assertSame('Alice', $user->name);
        $this->assertNull($user->rate);
        $this->assertSame(true, $user->isActive);
    }

    public function test_fromJson_doesntSetPropertyWhenExtraPropertyExistsInJson()
    {
        $json = <<<'JSON'
        {
          "id": 1,
          "name": "Alice",
          "rate": 0.5,
          "isActive": true,
          "password": "secret"
        }
        JSON;

        $user = UserForJSONTest::fromJson($json);

        $this->assertSame(1, $user->id);
        $this->assertSame('Alice', $user->name);
        $this->assertEqualsWithDelta(0.5, $user->rate, 0.0);
        $this->assertSame(true, $user->isActive);
        $this->assertObjectNotHasAttribute('password', $user);
    }

    public function test_jsonSerialize()
    {
        $user = UserForJSONTest::create(id: 1, name: 'Alice', rate: 0.5, isActive: true);
        $userInJson = json_encode($user);

        $this->assertJsonStringEqualsJsonString('{"id":1,"name":"Alice","rate":0.5,"isActive":true}', $userInJson);
    }

    public function test_jsonSerialize_setsNullWhenPropertyIsNullInObject()
    {
        $user = UserWithNullablePropertyForJSONTest::create(id: 1, name: 'Alice', isActive: true);
        $userInJson = json_encode($user);

        $this->assertJsonStringEqualsJsonString('{"id":1,"name":"Alice","rate":null,"isActive":true}', $userInJson);
    }

    public function test_jsonSerialize_omitsPropertyWhenPropertyWithOmitEmptyOptionIsNullInObject()
    {
        $user = UserWithOmittablePropertyForJSONTest::create(id: 1, name: 'Alice', isActive: true);
        $userInJson = json_encode($user);

        $this->assertJsonStringEqualsJsonString('{"id":1,"name":"Alice","isActive":true}', $userInJson);
    }
}
