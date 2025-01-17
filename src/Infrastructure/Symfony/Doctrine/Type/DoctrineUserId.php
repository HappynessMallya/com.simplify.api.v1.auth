<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Doctrine\Type;

use App\Domain\Model\User\UserId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\GuidType;

/**
 * Class DoctrineUserId
 * @package App\Infrastructure\Symfony\Doctrine\Type
 */
class DoctrineUserId extends GuidType
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return 'UserId';
    }

    /**
     * @param mixed $value
     * @param AbstractPlatform $platform
     * @return null
     * @throws ConversionException
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof UserId) {
            return $value->toString();
        }

        throw ConversionException::conversionFailed($value, $this->getName());
    }

    /**
     * @param mixed $value
     * @param AbstractPlatform $platform
     * @return UserId|null
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?UserId
    {
        if (empty($value)) {
            return null;
        }

        if ($value instanceof UserId) {
            return $value;
        }

        return UserId::fromString($value);
    }
}
