<?php

declare(strict_types=1);

namespace App\Domain\Model\User;

use App\Domain\Model\Enum;

/**
 * Class UserRole
 * @package App\Domain\Model\User
 *
 * @method static UserRole USER()
 * @method static UserRole COMPANY()
 * @method static UserRole ADMIN()
 * @method static UserRole SUPER_ADMIN()
 * @method static UserRole USSD()
 *
 */
class UserRole extends Enum
{
    public const USER = 'ROLE_USER';
    public const COMPANY = 'ROLE_COMPANY';
    public const ADMIN = 'ROLE_ADMIN';
    public const SUPER_ADMIN = 'ROLE_SUPER_ADMIN';
    public const USSD = 'ROLE_USSD';
}
