<?php

declare(strict_types=1);

namespace App\Application\User\Query;

/**
 * Class GetOperatorByIdQuery
 * @package App\Application\User\V2\QueryHandler
 */
class GetOperatorByIdQuery
{
    /** @var string */
    protected string $operatorId;

    /** @var string */
    protected string $userType;

    /**
     * @param string $operatorId
     * @param string $userType
     */
    public function __construct(string $operatorId, string $userType)
    {
        $this->operatorId = $operatorId;
        $this->userType = $userType;
    }

    /**
     * @return string
     */
    public function getOperatorId(): string
    {
        return $this->operatorId;
    }

    /**
     * @return string
     */
    public function getUserType(): string
    {
        return $this->userType;
    }
}
