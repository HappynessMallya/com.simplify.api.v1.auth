<?php

namespace App\Domain\Services;

class UploadCertificateToTraRegistrationRequest
{
    /** @var string  */
    private string $tin;

    /** @var string  */
    private string $serial;

    /** @var array  */
    private array $certificateFiles;

    /**
     * @param string $tin
     * @param array $certificateFiles
     * @param string $serial
     */
    public function __construct(string $tin, array $certificateFiles, string $serial)
    {
        $this->tin = $tin;
        $this->certificateFiles = $certificateFiles;
        $this->serial = $serial;
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

    /**
     * @return string
     */
    public function getSerial(): string
    {
        return $this->serial;
    }
}
