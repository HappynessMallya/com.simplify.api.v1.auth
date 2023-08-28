<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Doctrine\Type;

use App\Domain\Model\Company\CompanyId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\GuidType;

/**
 * Class DoctrineCompanyId
 * @package App\Infrastructure\Symfony\Doctrine\Type
 */
class DoctrineCompanyId extends GuidType
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return 'CompanyId';
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

        if ($value instanceof CompanyId) {
            return $value->toString();
        }

        throw ConversionException::conversionFailed($value, $this->getName());
    }

    /**
     * @param mixed $value
     * @param AbstractPlatform $platform
     * @return CompanyId|null
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?CompanyId
    {
        if (empty($value)) {
            return null;
        }

        if ($value instanceof CompanyId) {
            return $value;
        }

        return CompanyId::fromString($value);
    }
}
