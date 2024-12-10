<?php

declare(strict_types=1);

namespace App\Domain\Model\Subscription;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="subscriptions")
 */
class Subscription
{
    /** @ORM\Id */
    #[ORM\Id]
    #[ORM\Column(type: "string")]
    private string $id;

    /** @ORM\Column(type: "string") */
    private string $companyId;

    /** @ORM\Column(type: "string") */
    private string $date;

    /** @ORM\Column(type: "string") */
    private string $type;

    /** @ORM\Column(type: "decimal", precision: 10, scale: 2, nullable: true) */
    private ?float $subscriptionAmount;

    public function __construct(string $id, string $companyId, string $date, string $type, ?float $subscriptionAmount)
    {
        $this->id = $id;
        $this->companyId = $companyId;
        $this->date = $date;
        $this->type = $type;
        $this->subscriptionAmount = $subscriptionAmount;
    }

    public function getId(): string
    {
        return $this->id;
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

    public function getSubscriptionAmount(): ?float
    {
        return $this->subscriptionAmount;
    }

    public function setSubscriptionAmount(?float $subscriptionAmount): void
    {
        $this->subscriptionAmount = $subscriptionAmount;
    }
}
