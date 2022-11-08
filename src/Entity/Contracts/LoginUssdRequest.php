<?php

declare(strict_types=1);

namespace App\Entity\Contracts;

/**
 * Class LoginUssdRequest
 * @package App\Entity\Contracts
 */
class LoginUssdRequest
{
    /** @var string */
    private string $tin;

    /** @var string */
    private string $pin;

    /**
     * @return string
     */
    public function getTin(): string
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
    public function getPin(): string
    {
        return $this->pin;
    }

    /**
     * @param string $pin
     */
    public function setPin(string $pin): void
    {
        $this->pin = $pin;
    }
}
