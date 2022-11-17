<?php

declare(strict_types=1);

namespace App\Application\Company\Query;

/**
 * Class GetCompanyByTinQuery
 * @package App\Application\Company\Query
 */
class GetCompanyByTinQuery
{
    /** @var string */
    protected string $companyTin;

    /**
     * GetCompanyByTinQuery constructor
     * @param string $companyTin
     */
    public function __construct(string $companyTin)
    {
        $this->companyTin = $companyTin;
    }

    /**
     * @return string
     */
    public function getCompanyTin(): string
    {
        return $this->companyTin;
    }
}
