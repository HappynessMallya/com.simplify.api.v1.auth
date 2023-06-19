<?php

namespace App\Domain\Model\User;

use App\Domain\Model\Enum;

/**
 * Class UserType
 * @package App\Domain\Model\User
 * @method static UserType TYPE_ADMIN()
 * @method static UserType TYPE_OWNER()
 * @method static UserType TYPE_OPERATOR()
 */
class UserType extends Enum
{
    public const TYPE_ADMIN = 'ADMIN';
    public const TYPE_OWNER = 'OWNER';
    public const TYPE_OPERATOR = 'OPERATOR';
}
