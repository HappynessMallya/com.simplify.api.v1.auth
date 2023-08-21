<?php

declare(strict_types=1);

namespace App\Domain\Model\Company;

use App\Domain\Model\Organization\OrganizationId;
use DateTime;

/**
 * Class Company
 * @package App\Domain\Model\Company
 */
class Company
{
    /** @var CompanyId */
    private CompanyId $companyId;

    /** @var string */
    private string $name;

    /** @var int */
    private int $tin;

    /** @var string|null */
    private ?string $address;

    /** @var string|null */
    private ?string $email;

    /** @var string|null */
    private ?string $phone;

    /** @var array|null */
    private ?array $traRegistration;

    /** @var bool */
    private bool $enable;

    /** @var DateTime */
    private DateTime $createdAt;

    /** @var string */
    private string $companyStatus;

    /** @var string|null */
    private ?string $serial;

    /** @var OrganizationId|null  */
    private ?OrganizationId $organizationId;

    /** @var DateTime|null  */
    private ?DateTime $updatedAt;

    /**
     * @param CompanyId $companyId
     * @param string $name
     * @param int $tin
     * @param string|null $address
     * @param string|null $email
     * @param string|null $phone
     * @param DateTime $createdAt
     * @param CompanyStatus $companyStatus
     * @param string|null $serial
     * @param OrganizationId|null $organizationId
     * @return Company
     */
    public static function create(
        CompanyId $companyId,
        string $name,
        int $tin,
        ?string $address,
        ?string $email,
        ?string $phone,
        DateTime $createdAt,
        CompanyStatus $companyStatus,
        ?string $serial,
        ?OrganizationId $organizationId,
        ?DateTime $updatedAt
    ): Company {
        $self = new self();
        $self->companyId = $companyId;
        $self->name = $name;
        $self->tin = $tin;
        $self->address = $address;
        $self->email = $email;
        $self->phone = $phone;
        $self->enable = true;
        $self->createdAt = $createdAt;
        $self->companyStatus = $companyStatus->getValue();
        $self->serial = $serial;
        $self->organizationId = $organizationId;
        $self->updatedAt = $updatedAt;

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
     * @return int
     */
    public function tin(): int
    {
        return $this->tin;
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
     * @return string
     */
    public function phone(): ?string
    {
        return $this->phone;
    }

    /**
     * @return DateTime
     */
    public function createdAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param array $data
     */
    public function updateTraRegistration(array $data)
    {
        $this->traRegistration = $data;
    }

    /**
     * @return array|null
     */
    public function traRegistration(): ?array
    {
        return $this->traRegistration;
    }

    /**
     * @return bool
     */
    public function isEnable(): bool
    {
        return $this->enable ?? false;
    }

    public function disable(): void
    {
        $this->enable = false;
    }

    /**
     * @param array $toUpdate
     */
    public function update(array $toUpdate): void
    {
        $notNull = ['companyId', 'name', 'tin', 'traRegistration'];

        foreach ($toUpdate as $attribute => $newValue) {
            if (property_exists(self::class, $attribute)) {
                if (empty($newValue)) {
                    if (in_array($attribute, $notNull)) {
                        continue;
                    }

                    $this->{$attribute} = null;
                } else {
                    $this->{$attribute} = $newValue;
                }
            }
        }
    }

    /**
     * @return string
     */
    public function companyStatus(): string
    {
        return $this->companyStatus;
    }

    /**
     * @param CompanyStatus $companyStatus
     * @return void
     */
    public function updateCompanyStatus(CompanyStatus $companyStatus): void
    {
        $this->companyStatus = $companyStatus->getValue();
    }

    /**
     * @return string|null
     */
    public function serial(): ?string
    {
        return $this->serial;
    }

    /**
     * @return OrganizationId|null
     */
    public function organizationId(): ?OrganizationId
    {
        return $this->organizationId;
    }

    public function updatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function setOrganizationId(?OrganizationId $organizationId): void
    {
        $this->organizationId = $organizationId;
    }

    public function enable(): void
    {
        $this->enable = true;
    }
}
