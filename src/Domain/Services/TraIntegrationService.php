<?php

declare(strict_types=1);

namespace App\Domain\Services;

interface TraIntegrationService
{
    /**
     * @param CompanyStatusOnTraRequest $request
     * @return CompanyStatusOnTraResponse
     */
    public function requestCompanyStatusOnTra(
        CompanyStatusOnTraRequest $request
    ): CompanyStatusOnTraResponse;
}
