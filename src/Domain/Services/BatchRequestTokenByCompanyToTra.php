<?php

namespace App\Domain\Services;

class BatchRequestTokenByCompanyToTra
{
    /** @var array  */
    private array $companies;

    /**
     * @param array $companies
     */
    public function __construct(array $companies)
    {
        $this->companies = $companies;
    }

    public function getCompanies(): array
    {
        return $this->companies;
    }
}
