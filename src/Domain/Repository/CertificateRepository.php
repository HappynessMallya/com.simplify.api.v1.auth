<?php

namespace App\Domain\Repository;

use App\Domain\Company\CertificateId;
use App\Domain\Model\Certificate;

interface CertificateRepository
{
    /**
     * @param array $filesPack
     * @return void
     */
    public function save(array $filesPack): void;

    /**
     * @param CertificateId $certificateId
     * @return Certificate|null
     */
    public function findByCertificateId(CertificateId $certificateId): ?Certificate;

    /**
     * @param string $filePath
     * @return Certificate|null
     */
    public function findByFilePath(string $filePath): ?Certificate;

    /**
     * @param Certificate $file
     * @return void
     */
    public function update(Certificate $file): void;

    /**
     * @param CertificateId $fileId
     * @return void
     */
    public function remove(CertificateId $certificateId): void;
}
