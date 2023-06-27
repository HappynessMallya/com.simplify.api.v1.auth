<?php

declare(strict_types=1);

namespace App\Application\Company\Command;

class ChangeEnableCompanyCommand
{
    /** @var string  */
    private string $companyId;

    /** @var bool  */
    private bool $enable;

    /**
     * @return string
     */
    public function getCompanyId(): string
    {
        return $this->companyId;
    }

    /**
     * @param string $companyId
     */
    public function setCompanyId(string $companyId): void
    {
        $this->companyId = $companyId;
    }

    /**
     * @return bool
     */
    public function isEnable(): bool
    {
        return $this->enable;
    }

    /**
     * @param bool $enable
     */
    public function setEnable(bool $enable): void
    {
        $this->enable = $enable;
    }
}
