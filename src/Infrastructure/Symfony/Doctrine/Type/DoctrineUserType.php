<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Doctrine\Type;

use App\Domain\Model\User\UserType;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\StringType;

/**
 * Class DoctrineUserType
 * @package App\Infrastructure\Symfony\Doctrine\Type
 */
class DoctrineUserType extends StringType
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return 'UserType';
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

        if ($value instanceof UserType) {
            return $value->getValue();
        }

        throw ConversionException::conversionFailed($value, $this->getName());
    }

    /**
     * @param mixed $value
     * @param AbstractPlatform $platform
     * @return UserType|null
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?UserType
    {
        if (empty($value)) {
            return null;
        }

        if ($value instanceof UserType) {
            return $value;
        }

        return UserType::byValue($value);
    }
}
