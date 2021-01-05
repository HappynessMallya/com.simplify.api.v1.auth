<?php

declare(strict_types=1);

namespace App\Domain\Model\Company;

use DateTime;

/**
 * Class Company
 * @package App\Domain\Model\Company
 */
class Company
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
     * @var int
     */
    private $tin;

    /**
     * @var string
     */
    private $address;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $phone;

    /**
     * @var array
     */
    private $traRegistration;

    /**
     * @var bool
     */
    private $enable;

    /**
     * @var DateTime
     */
    private $createdAt;

    /**
     * @param CompanyId $companyId
     * @param string $name
     * @param int $tin
     * @param string|null $address
     * @param string|null $email
     * @param string|null $phone
     * @param DateTime $createdAt
     * @return Company
     */
    public static function create(
        CompanyId $companyId,
        string $name,
        int $tin,
        ?string $address,
        ?string $email,
        ?string $phone,
        DateTime $createdAt
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
        return (int) $this->tin;
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
}
