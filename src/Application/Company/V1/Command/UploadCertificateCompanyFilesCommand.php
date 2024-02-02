<?php

declare(strict_types=1);

namespace App\Application\Company\V1\Command;

/**
 * Class UploadCertificateCompanyFilesCommand
 * @package App\Application\Company\V1\Command
 */
class UploadCertificateCompanyFilesCommand
{
    /** @var string */
    private string $tin;

    /** @var string  */
    private string $serial;

    /** @var array */
    private array $companyFiles;

    private string $certificatePassword;

    /**
     * @param string $tin
     * @param array $companyFiles
     * @param string $serial
     * @param string $certificatePassword
     */
    public function __construct(
        string $tin,
        array $companyFiles,
        string $serial,
        string $certificatePassword
    ) {
        $this->tin = $tin;
        $this->companyFiles = $companyFiles;
        $this->serial = $serial;
        $this->certificatePassword = $certificatePassword;
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
    public function getCompanyFiles(): array
    {
        return $this->companyFiles;
    }

    /**
     * @return string
     */
    public function getSerial(): string
    {
        return $this->serial;
    }

    public function getCertificatePassword(): string
    {
        return $this->certificatePassword;
    }
}
