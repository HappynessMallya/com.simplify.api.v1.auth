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

    /** @var Serial  */
    private Serial $serial;

    /** @var string */
    private string $filepath;

    /**
     * @param CertificateId $certificateId
     * @param TaxIdentificationNumber $tin
     * @param string $filepath
     * @param Serial $serial
     */
    public function __construct(
        CertificateId $certificateId,
        TaxIdentificationNumber $tin,
        string $filepath,
        Serial $serial
    ) {
        $this->certificateId = $certificateId;
        $this->tin = $tin;
        $this->filepath = $filepath;
        $this->serial = $serial;
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

    /**
     * @return Serial
     */
    public function getSerial(): Serial
    {
        return $this->serial;
    }
}
