<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\GuidType;
use App\Domain\Model\User\UserId;

/**
 * Class DoctrineUserId
 * @package App\Infrastructure\Persistence\Doctrine\Type
 */
final class DoctrineUserId extends GuidType
{
    /**
     * @return string
     */
    public function getName()
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
        if (null === $value) {
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
     * @return UserId
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (empty($value)) {
            return null;
        }

        if ($value instanceof UserId) {
            return $value;
        }

        $value = UserId::fromString($value);

        return $value;
    }
}
