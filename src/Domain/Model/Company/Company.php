<?php

declare(strict_types=1);

namespace App\Domain\Model\Company;

use DateTime;

/**
 * Class Company
 * @package App\Domain\Model\Company
 */
final class Company
{
    /**
     * @var CompanyId
     */
    private $companyId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $address;

    /**
     * @var string
     */
    private $email;

    /**
     * @var DateTime
     */
    private $createdAt;

    /**
     * @param CompanyId $companyId
     * @param string $name
     * @param string|null $address
     * @param string|null $email
     * @param DateTime $createdAt
     * @return Company
     */
    public static function create(
        CompanyId $companyId,
        string $name,
        ?string $address,
        ?string $email,
        DateTime $createdAt
    ): Company {
        $self = new self();
        $self->companyId = $companyId;
        $self->name = $name;
        $self->address = $address;
        $self->email = $email;
        $self->createdAt = $createdAt;

        return $self;
    }

    /**
     * @return CompanyId
     */
    public function companyId(): CompanyId
    {
        return $this->companyId;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function address(): ?string
    {
        return $this->address;
    }

    /**
     * @return string
     */
    public function email(): ?string
    {
        return $this->email;
    }

    /**
     * @return DateTime
     */
    public function createdAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param array $toUpdate
     */
    public function update(array $toUpdate): void
    {
        foreach ($toUpdate as $attribute => $newValue) {
            if (property_exists(self::class, $attribute)) {
                if (empty($newValue)) {
                    $this->{$attribute} = null;
                } else {
                    $this->{$attribute} = $newValue;
                }
            }
        }
    }
}
