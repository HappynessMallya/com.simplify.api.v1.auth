<?php

declare(strict_types=1);

namespace App\Domain\Services;

use App\Domain\Model\Company\TaxIdentificationNumber;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Interface FileUploaderService
 * @package App\Domain\Services
 */
interface FileUploaderService
{
    /**
     * @param UploadedFile $file
     * @param TaxIdentificationNumber $tin
     * @return string
     */
    public function uploadFile(UploadedFile $file, TaxIdentificationNumber $tin): string;
}
