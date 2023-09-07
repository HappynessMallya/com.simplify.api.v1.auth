<?php

declare(strict_types=1);

namespace App\Application\Company\V1\Command;

/**
 * Class RequestAuthenticationTraCommand
 * @package App\Application\Company\V1\Command
 */
class BatchRequestAuthenticationTraCommand
{
    /** @var array */
    private array $companies;

    /**
     * @param array $companies
     */
    public function __construct(array $companies)
    {
        $this->companies = $companies;
    }

    public function getCompanies(): array
    {
        return $this->companies;
    }
}
