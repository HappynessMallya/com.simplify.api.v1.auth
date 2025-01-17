<?php

declare(strict_types=1);

namespace App\Domain\Model;

interface ValueObject
{
    public function sameValueAs(ValueObject $object): bool;
}
