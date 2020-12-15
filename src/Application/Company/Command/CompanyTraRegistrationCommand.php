<?php
declare(strict_types=1);

namespace App\Application\Company\Command;

/**
 * Class CompanyTraRegistrationCommand
 * @package App\Application\Company\Command
 */
final class CompanyTraRegistrationCommand
{
    /**
     * @var string
     */
    protected $tin;

    /**
     * @var string
     */
    protected $traRegistration;

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
