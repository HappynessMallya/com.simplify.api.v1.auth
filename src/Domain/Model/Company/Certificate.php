<?php

declare(strict_types=1);

namespace App\Domain\Model\Company;

/**
 * Class Certificate
 * @package App\Domain\Model\Company
 */
class Certificate
{
    /** @var CertificateId */
    private CertificateId $certificateId;

    /** @var TaxIdentificationNumber */
    private TaxIdentificationNumber $tin;

    /** @var string */
    private string $filepath;

    /**
     * @param CertificateId $certificateId
     * @param TaxIdentificationNumber $tin
     * @param string $filepath
     */
    public function __construct(CertificateId $certificateId, TaxIdentificationNumber $tin, string $filepath)
    {
        $this->certificateId = $certificateId;
        $this->tin = $tin;
        $this->filepath = $filepath;
    }

    /**
     * @return CertificateId
     */
    public function getCertificateId(): CertificateId
    {
        return $this->certificateId;
    }

    /**
     * @return TaxIdentificationNumber
     */
    public function getTin(): TaxIdentificationNumber
    {
        return $this->tin;
    }

    /**
     * @return string
     */
    public function getFilepath(): string
    {
        return $this->filepath;
    }
}
