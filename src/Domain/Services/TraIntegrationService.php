<?php

declare(strict_types=1);

namespace App\Domain\Services;

/**
 * Interface TraIntegrationService
 * @package App\Domain\Services
 */
interface TraIntegrationService
{
    /**
     * @param CompanyStatusOnTraRequest $request
     * @return CompanyStatusOnTraResponse
     */
    public function requestCompanyStatusOnTra(
        CompanyStatusOnTraRequest $request
    ): CompanyStatusOnTraResponse;

    /**
     * @param UploadCertificateToTraRegistrationRequest $request
     * @return UploadCertificateToTraRegistrationResponse
     */
    public function uploadCertificateToTraRegistration(
        UploadCertificateToTraRegistrationRequest $request
    ): UploadCertificateToTraRegistrationResponse;

    /**
     * @param RegistrationCompanyToTraRequest $request
     * @return RegistrationCompanyToTraResponse
     */
    public function registrationCompanyToTra(
        RegistrationCompanyToTraRequest $request
    ): RegistrationCompanyToTraResponse;

    /**
     * @param BatchRequestTokenByCompanyToTra $request
     * @return BatchRequestTokenByCompanyToTraResponse
     */
    public function batchRequestTokenByCompanyToTra(
        BatchRequestTokenByCompanyToTra $request
    ): BatchRequestTokenByCompanyToTraResponse;
}
