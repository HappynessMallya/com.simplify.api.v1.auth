<?php

declare(strict_types=1);

namespace App\Domain\Model\Company;

use App\Domain\Model\ValueObject;

/**
 * Class TaxIdentificationNumber
 * @package App\Domain\Model\Company
 */
class Serial implements ValueObject
{
    /** @var string */
    private string $value;

    /**
     * @param string $serial
     */
    public function __construct(string $serial)
    {
        $this->value = $serial;
    }

    /**
     * @param ValueObject $object
     * @return bool
     */
    public function sameValueAs(ValueObject $object): bool
    {
        return get_class($this) === get_class($object) && $this->value === $object->value;
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function value(): string
    {
        return $this->value;
    }
}
