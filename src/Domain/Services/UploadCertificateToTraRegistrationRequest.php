<?php

namespace App\Domain\Services;

class UploadCertificateToTraRegistrationRequest
{
    private string $tin;

    private array $certificateFiles;

    /**
     * @param string $tin
     * @param array $certificateFiles
     */
    public function __construct(string $tin, array $certificateFiles)
    {
        $this->tin = $tin;
        $this->certificateFiles = $certificateFiles;
    }

    /**
     * @return string
     */
    public function getTin(): string
    {
        return $this->tin;
    }

    /**
     * @return array
     */
    public function getCertificateFiles(): array
    {
        return $this->certificateFiles;
    }
}
