<?php

declare(strict_types=1);

namespace App\Domain\Model\User;

use App\Domain\Model\ValueObject;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * Class UserId
 * @package App\Domain\Model\ApiUser
 */
final class UserId implements ValueObject
{
    /**
     * @var UuidInterface
     */
    private $uuid;

    public static function generate(): UserId
    {
        return new self(Uuid::uuid4());
    }

    public static function fromString(string $userId): UserId
    {
        return new self(Uuid::fromString($userId));
    }

    private function __construct(UuidInterface $uuid)
    {
        $this->uuid = $uuid;
    }

    public function toString(): string
    {
        return $this->uuid->toString();
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function sameValueAs(ValueObject $other): bool
    {
        return get_class($this) === get_class($other) && $this->uuid->equals($other->uuid);
    }
}