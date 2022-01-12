<?php

declare(strict_types=1);

namespace Okashoi\PhpAttributesExample;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class JSON
{
    public const OPTION_OMIT_EMPTY = 1;

    public function __construct(private string $name, private int $options = 0)
    {
    }
}
