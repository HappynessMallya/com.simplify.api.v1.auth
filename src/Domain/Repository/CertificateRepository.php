<?php

namespace App\Domain\Repository;

use App\Domain\Model\Company\Certificate;
use App\Domain\Model\Company\CertificateId;

/**
 * Interface CertificateRepository
 * @package App\Domain\Repository
 */
interface CertificateRepository
{
    /**
     * @param array $filesPack
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
     */
    public function update(Certificate $file): void;

    /**
     * @param CertificateId $certificateId
     */
    public function remove(CertificateId $certificateId): void;
}
