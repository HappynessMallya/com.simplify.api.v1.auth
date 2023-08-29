<?php

declare(strict_types=1);

namespace App\Domain\Services;

/**
 * Interface CertificateDataService
 * @package App\Domain\Services
 */
interface CertificateDataService
{
    public function createCertificateData(string $filepath): string;
}
