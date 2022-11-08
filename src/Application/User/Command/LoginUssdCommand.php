<?php

declare(strict_types=1);

namespace App\Application\User\Command;

/**
 * Class LoginUssdCommand
 * @package App\Application\User\Command
 */
class LoginUssdCommand
{
    /** @var string */
    private string $tin;

    /** @var string */
    private string $pin;

    /**
     * LoginUssdCommand constructor
     * @param string $tin
     * @param string $pin
     */
    public function __construct(string $tin, string $pin)
    {
        $this->tin = $tin;
        $this->pin = $pin;
    }

    /**
     * @return string
     */
    public function getTin(): string
    {
        return $this->tin;
    }

    /**
     * @return string
     */
    public function getPin(): string
    {
        return $this->pin;
    }
}
