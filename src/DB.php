<?php

declare(strict_types=1);

namespace Okashoi\PhpAttributesExample;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class DB
{
    public function __construct(private string $name)
    {
    }
}
