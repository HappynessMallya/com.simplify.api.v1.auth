<?php

declare(strict_types=1);

namespace App\Domain\Model;

/**
 * Class Enum
 * @package App\Domain\Model
 */
abstract class Enum extends EnumBookie implements ValueObject
{
    public function sameValueAs(ValueObject $object): bool
    {
        return $this->is($object);
    }

    public function toString(): string
    {
        return $this->getName();
    }
}
