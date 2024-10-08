<?php

namespace App\Application\User\V1\Query;

class GetUsersByCompanyIdQuery
{
    private string $companyId;

    /**
     * @param string $companyId
     */
    public function __construct(string $companyId)
    {
        $this->companyId = $companyId;
    }

    public function getCompanyId(): string
    {
        return $this->companyId;
    }
}
