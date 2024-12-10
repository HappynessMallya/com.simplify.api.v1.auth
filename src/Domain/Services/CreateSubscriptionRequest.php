<?php

namespace App\Domain\Services;

class CreateSubscriptionRequest
{
    private string $companyId;

    private string $date;

    private string $type;

    private ?float $subscriptionAmount;

    /**
     * @param string $companyId
     * @param string $date
     * @param string $type
     * @param float $subscriptionAmount
     */
    public function __construct(string $companyId, string $date, string $type, ?float $subscriptionAmount)
    {
        $this->companyId = $companyId;
        $this->date = $date;
        $this->type = $type;
        $this->subscriptionAmount = $subscriptionAmount;
    }

    public function getSubscriptionAmount(): ?float
    {
        return $this->subscriptionAmount;
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
