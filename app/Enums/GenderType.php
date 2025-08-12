<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static MALE()
 * @method static static FEMALE()
 * @method static static OTHERS()
 */
final class GenderType extends Enum
{
    const MALE = 'MALE';
    const FEMALE = 'FEMALE';
    const OTHERS = 'OTHERS';
}
