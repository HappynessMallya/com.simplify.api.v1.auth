<?php

declare(strict_types=1);

namespace App\Application\Company\Query;

/**
 * Class GetCompanyByIdQuery
 * @package App\Application\Company\Query
 */
class GetCompanyByIdQuery
{
    /** @var string */
    protected $companyId;

    public function __construct(string $companyId)
    {
        $this->companyId = $companyId;
    }

    /**
     * @return string
     */
    public function companyId(): string
    {
        return $this->companyId;
    }
}
