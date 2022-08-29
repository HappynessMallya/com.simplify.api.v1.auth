<?php

declare(strict_types=1);

namespace App\Domain\Company;

use App\Domain\Model\ValueObject;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class CertificateId implements ValueObject
{
    /**
     * @var Uuid
     */
    private $uuid;

    /**
     * FileId constructor
     * @param UuidInterface $uuid
     */
    public function __construct(UuidInterface $uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     * @return CertificateId
     */
    public static function generate(): CertificateId
    {
        return new CertificateId(Uuid::uuid4());
    }

    /**
     * @param ValueObject $object
     * @return bool
     */
    public function sameValueAs(ValueObject $object): bool
    {
        return get_class($this) === get_class($object) && $this->uuid->equals($object->uuid);
    }

    /**
     * @param string $id
     * @return CertificateId
     */
    public static function fromString(string $id): CertificateId
    {
        return new self(Uuid::fromString($id));
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->uuid->toString();
    }

    /**
     * @return Uuid
     */
    public function value(): Uuid
    {
        return $this->uuid;
    }
}
