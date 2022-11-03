<?php

declare(strict_types=1);

namespace App\Domain\Services;

class RegistrationCompanyToTraRequest
{
    /**
     * @var string
     */
    private string $tin;

    /**
     * @var string
     */
    private string $certificateKey;

    /**
     * @var string
     */
    private string $certificateSerial;

    /**
     * @var string
     */
    private string $password;

    /**
     * @param string $tin
     * @param string $certificateKey
     * @param string $certificateSerial
     * @param string $password
     */
    public function __construct(string $tin, string $certificateKey, string $certificateSerial, string $password)
    {
        $this->tin = $tin;
        $this->certificateKey = $certificateKey;
        $this->certificateSerial = $certificateSerial;
        $this->password = $password;
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
    public function getPassword(): string
    {
        return $this->password;
    }
}
