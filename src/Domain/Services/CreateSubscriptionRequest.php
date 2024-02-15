<?php

namespace App\Domain\Services;

class CreateSubscriptionRequest
{
    private string $companyId;

    private string $date;

    private string $type;

    /**
     * @param string $companyId
     * @param string $date
     * @param string $type
     */
    public function __construct(string $companyId, string $date, string $type)
    {
        $this->companyId = $companyId;
        $this->date = $date;
        $this->type = $type;
    }

    public function getCompanyId(): string
    {
        return $this->companyId;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
