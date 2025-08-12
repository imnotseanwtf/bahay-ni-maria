<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static Admin()
 * @method static static Sister()
 * @method static static Caregiver()
 */
final class UserType extends Enum
{
    const Admin = 0;
    const Sister = 1;
    const Caregiver = 2;
}
