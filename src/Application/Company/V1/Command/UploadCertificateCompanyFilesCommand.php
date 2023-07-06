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

    /** @var array */
    private array $companyFiles;

    /**
     * @param string $tin
     * @param array $companyFiles
     */
    public function __construct(
        string $tin,
        array $companyFiles
    ) {
        $this->tin = $tin;
        $this->companyFiles = $companyFiles;
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
}
