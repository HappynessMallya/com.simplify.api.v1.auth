<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Doctrine\Type;

use App\Domain\Model\Organization\OrganizationId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\GuidType;

/**
 * Class DoctrineUserId
 * @package App\Infrastructure\Persistence\Doctrine\Type
 */
class DoctrineOrganizationId extends GuidType
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

        if ($value instanceof OrganizationId) {
            return $value->toString();
        }

        throw ConversionException::conversionFailed($value, $this->getName());
    }

    /**
     * @param mixed $value
     * @param AbstractPlatform $platform
     * @return OrganizationId|null
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?OrganizationId
    {
        if (empty($value)) {
            return null;
        }

        if ($value instanceof OrganizationId) {
            return $value;
        }

        return OrganizationId::fromString($value);
    }
}
