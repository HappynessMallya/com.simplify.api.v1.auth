<?php

declare(strict_types=1);

namespace App\Domain\Services;

interface CompanyAuthenticationTraService
{
    /**
     * @param CompanyAuthenticationTraRequest $request
     * @return CompanyAuthenticationTraResponse
     */
    public function requestTokenAuthenticationTra(
        CompanyAuthenticationTraRequest $request
    ): CompanyAuthenticationTraResponse;
}
