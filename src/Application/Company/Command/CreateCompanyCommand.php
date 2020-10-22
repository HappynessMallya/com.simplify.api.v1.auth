<?php
declare(strict_types=1);

namespace App\Application\Company\Command;

/**
 * Class CreateCompanyCommand
 * @package App\Application\Company\Command
 */
final class CreateCompanyCommand
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string|null
     */
    protected $address;

    /**
     * @var string|null
     */
    protected $email;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }
}
