<?php

namespace App\Domain\Services;

interface CertificateDataService
{
    public function createCertificateData(string $filepath): string;

}