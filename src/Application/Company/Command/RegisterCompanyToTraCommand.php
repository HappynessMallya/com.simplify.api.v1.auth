<?php

namespace App\Application\Company\Command;

class RegisterCompanyToTraCommand
{
    private string $tin;

    private string $certificateKey;

    private string $certificateSerial;

    private string $certificatePassword;

    /**
     * @param string $tin
     * @param string $certificateKey
     * @param string $certificateSerial
     * @param string $certificatePassword
     */
    public function __construct(
        string $tin,
        string $certificateKey,
        string $certificateSerial,
        string $certificatePassword
    ) {
        $this->tin = $tin;
        $this->certificateKey = $certificateKey;
        $this->certificateSerial = $certificateSerial;
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
     * @return string
     */
    public function getCertificateKey(): string
    {
        return $this->certificateKey;
    }

    /**
     * @return string
     */
    public function getCertificateSerial(): string
    {
        return $this->certificateSerial;
    }

    /**
     * @return string
     */
    public function getCertificatePassword(): string
    {
        return $this->certificatePassword;
    }
}
