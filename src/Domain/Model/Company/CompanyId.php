<?php

declare(strict_types=1);

namespace App\Domain\Model\Company;

use App\Domain\Model\ValueObject;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * Class CompanyId
 * @package App\Domain\Model\Company
 */
class CompanyId implements ValueObject
{
    /** @var UuidInterface */
    private UuidInterface $uuid;

    /**
     * @return CompanyId
     */
    public static function generate(): CompanyId
    {
        return new self(Uuid::uuid4());
    }

    /**
     * @param string $userId
     * @return CompanyId
     */
    public static function fromString(string $userId): CompanyId
    {
        return new self(Uuid::fromString($userId));
    }

    /**
     * @param UuidInterface $uuid
     */
    private function __construct(UuidInterface $uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->uuid->toString();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * @param ValueObject $object
     * @return bool
     */
    public function sameValueAs(ValueObject $object): bool
    {
        return get_class($this) === get_class($object) && $this->uuid->equals($object->uuid);
    }
}
