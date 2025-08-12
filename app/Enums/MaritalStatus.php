<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static MARRIED()
 * @method static static SINGLE()
 * @method static static DIVORCED()
 * @method static static WIDOWED()
 */
final class MaritalStatus extends Enum
{
    const MARRIED = 0;
    const SINGLE = 1;
    const DIVORCED = 2;
    const WIDOWED = 3;
}
