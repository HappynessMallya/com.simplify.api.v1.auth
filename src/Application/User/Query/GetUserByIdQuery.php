<?php

declare(strict_types=1);

namespace App\Application\User\Query;

/**
 * Class GetUserByIdQuery
 * @package App\Application\User\Query
 */
class GetUserByIdQuery
{
    /** @var string */
    protected string $userId;

    /**
     * @param string $userId
     */
    public function __construct(string $userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return string
     */
    public function getUserId(): string
    {
        return $this->userId;
    }
}
