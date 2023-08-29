<?php

declare(strict_types=1);

namespace App\Application\Company\V1\Query;

/**
 * Class GetCompanyByIdQuery
 * @package App\Application\Company\V1\Query
 */
class GetCompanyByIdQuery
{
    /** @var string */
    private string $companyId;

    /**
     * @param string $companyId
     */
    public function __construct(
        string $companyId
    ) {
        $this->companyId = $companyId;
    }

    /**
     * @return string
     */
    public function getCompanyId(): string
    {
        return $this->companyId;
    }
}
