<?php

namespace App\Domain\Services;

interface SubscriptionService
{
    /**
     * @param CreateSubscriptionRequest $request
     * @return CreateSubscriptionResponse
     */
    public function createSubscription(CreateSubscriptionRequest $request): CreateSubscriptionResponse;
}
