<?php

declare(strict_types=1);

namespace App\Application\Company\V1\Query;

/**
 * Class GetCompanyByTinQuery
 * @package App\Application\Company\V1\Query
 */
class GetCompanyByTinQuery
{
    /** @var string */
    protected string $tin;

    /**
     * @param string $tin
     */
    public function __construct(string $tin)
    {
        $this->tin = $tin;
    }

    /**
     * @return string
     */
    public function getTin(): string
    {
        return $this->tin;
    }
}
