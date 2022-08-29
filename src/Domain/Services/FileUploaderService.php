<?php

namespace App\Domain\Services;

use App\Domain\Model\Company\TaxIdentificationNumber;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface FileUploaderService
{
    /**
     * @param UploadedFile $file
     * @param TaxIdentificationNumber $tin
     * @return string
     */
    public function uploadFile(UploadedFile $file, TaxIdentificationNumber $tin): string;
}