<?php

declare(strict_types=1);

namespace App\Application\Company\Command;

class UploadCertificateCompanyFilesCommand
{
    /**
     * @var string
     */
    private string $tin;

    /**
     * @var array
     */
    private array $companyFiles;

    /**
     * @param string $companyId
     * @param array $companyFiles
     */
    public function __construct(string $companyId, array $companyFiles)
    {
        $this->tin = $companyId;
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
