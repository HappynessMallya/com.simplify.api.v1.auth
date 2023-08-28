<?php

declare(strict_types=1);

namespace App\Application\User\V1\Query;

/**
 * Class GetUsersQuery
 * @package App\Application\User\V1\Query
 */
class GetUsersQuery
{
    /** @var string */
    private string $userType;

    /**
     * @param string $userType
     */
    public function __construct(
        string $userType
    ) {
        $this->userType = $userType;
    }

    /**
     * @return string
     */
    public function getUserType(): string
    {
        return $this->userType;
    }
}
