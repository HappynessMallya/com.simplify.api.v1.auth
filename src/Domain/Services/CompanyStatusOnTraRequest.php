<?php

declare(strict_types=1);

namespace App\Domain\Services;

class CompanyStatusOnTraRequest
{
    /**
     * @var string
     */
    private string $companyId;

    /**
     * @var string
     */
    private string $tin;

    /**
     * @param string $companyId
     * @param string $tin
     */
    public function __construct(string $companyId, string $tin)
    {
        $this->companyId = $companyId;
        $this->tin = $tin;
    }

    /**
     * @return string
     */
    public function getCompanyId(): string
    {
        return $this->companyId;
    }

    /**
     * @return string
     */
    public function getTin(): string
    {
        return $this->tin;
    }
}
