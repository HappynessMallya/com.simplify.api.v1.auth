<?php

namespace App\Domain\Model\Company;

use App\Domain\Model\ValueObject;

class CertificatePassword implements ValueObject
{
    /** @var string */
    private string $value;

    /**
     * @param string $certificatePassword
     */
    public function __construct(string $certificatePassword)
    {
        $this->value = $certificatePassword;
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
