<?php

declare(strict_types=1);

namespace App\Domain\Model\User;

use App\Domain\Model\ValueObject;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * Class UserId
 * @package App\Domain\Model\User
 */
class UserId implements ValueObject
{
    /**
     * @var UuidInterface
     */
    private UuidInterface $uuid;

    /**
     * @return UserId
     */
    public static function generate(): UserId
    {
        return new self(Uuid::uuid4());
    }

    /**
     * @param string $userId
     * @return UserId
     */
    public static function fromString(string $userId): UserId
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
