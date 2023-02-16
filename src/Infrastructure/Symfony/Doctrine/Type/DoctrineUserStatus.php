<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\StringType;
use App\Domain\Model\User\UserStatus;

/**
 * Class DoctrineUserStatus
 * @package App\Infrastructure\Symfony\Doctrine\Type
 */
class DoctrineUserStatus extends StringType
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return 'UserStatus';
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

        if ($value instanceof UserStatus) {
            return $value->getValue();
        }

        throw ConversionException::conversionFailed($value, $this->getName());
    }

    /**
     * @param mixed $value
     * @param AbstractPlatform $platform
     * @return UserStatus|null
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?UserStatus
    {
        if (empty($value)) {
            return null;
        }

        if ($value instanceof UserStatus) {
            return $value;
        }

        return UserStatus::byValue($value);
    }
}
