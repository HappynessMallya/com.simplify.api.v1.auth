<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Api\Subscription\Controller;

use App\Domain\Model\Subscription\Subscription;
use App\Domain\Repository\SubscriptionRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SubscriptionController
{
    private SubscriptionRepository $subscriptionRepository;

    public function __construct(SubscriptionRepository $subscriptionRepository)
    {
        $this->subscriptionRepository = $subscriptionRepository;
    }

    #[Route('/api/v1/subscription', methods: ['POST'])]
    public function createSubscription(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['company_id'], $data['date'], $data['type'], $data['subscription_amount'])) {
            return new JsonResponse(['error' => 'Invalid request data'], 400);
        }

        $subscription = new Subscription(
            uniqid(),
            $data['company_id'],
            $data['date'],
            $data['type'],
            (float) $data['subscription_amount']
        );

        $this->subscriptionRepository->save($subscription);

        return new JsonResponse(['message' => 'Subscription created successfully'], 201);
    }
}
