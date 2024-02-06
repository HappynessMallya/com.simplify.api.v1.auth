<?php

declare(strict_types=1);

namespace App\Domain\Services;

use App\Domain\Model\Company\CertificatePassword;

/**
 * Interface CertificateDataService
 * @package App\Domain\Services
 */
interface CertificateDataService
{
    public function createCertificateData(string $filepath, CertificatePassword $certificatePassword): string;
}
