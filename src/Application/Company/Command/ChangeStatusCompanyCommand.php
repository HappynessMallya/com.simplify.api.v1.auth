<?php

declare(strict_types=1);

namespace App\Application\Company\Command;

/**
 * Class ChangeStatusCompanyCommand
 * @package App\Application\Company\Command
 */
class ChangeStatusCompanyCommand
{
    /** @var string */
    private string $tin;

    /** @var string */
    private string $status;

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
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }
}
