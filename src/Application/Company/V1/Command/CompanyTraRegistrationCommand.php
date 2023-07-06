<?php

declare(strict_types=1);

namespace App\Application\Company\V1\Command;

/**
 * Class CompanyTraRegistrationCommand
 * @package App\Application\Company\V1\Command
 */
class CompanyTraRegistrationCommand
{
    /** @var string */
    protected string $tin;

    /** @var string */
    protected string $traRegistration;

    /**
     * @return string
     */
    public function getTin(): ?string
    {
        return $this->tin;
    }

    /**
     * @param string $tin
     */
    public function setTin(string $tin): void
    {
        $this->tin = $tin;
    }

    /**
     * @return string
     */
    public function getTraRegistration(): string
    {
        return $this->traRegistration;
    }

    /**
     * @param string $traRegistration
     */
    public function setTraRegistration(string $traRegistration): void
    {
        $this->traRegistration = $traRegistration;
    }
}
