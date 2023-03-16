<?php

declare(strict_types=1);

namespace App\Application\User\Query;

/**
 * Class GetProfileByUsernameQuery
 * @package App\Application\User\Query
 */
class GetProfileByUsernameQuery
{
    /** @var string */
    protected string $username;

    /**
     * @param string $username
     */
    public function __construct(string $username)
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }
}
