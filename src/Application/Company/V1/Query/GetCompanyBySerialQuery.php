<?php

declare(strict_types=1);

namespace App\Application\Company\V1\Query;

/**
 * Class GetCompanyBySerialQuery
 * @package App\Application\Company\V1\Query
 */
class GetCompanyBySerialQuery
{
    /** @var string */
    private string $serial;

    /**
     * @param string $tin
     */
    public function __construct(
        string $tin
    ) {
        $this->serial = $tin;
    }

    /**
     * @return string
     */
    public function getSerial(): string
    {
        return $this->serial;
    }
}
