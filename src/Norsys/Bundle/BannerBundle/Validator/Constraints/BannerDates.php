<?php

/**
 * @author Nicolas VERBEKE <nverbeke@norsys.fr>
 */

declare(strict_types=1);

namespace Norsys\Bundle\BannerBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class BannerDates extends Constraint
{
    public string $message = 'norsys.banner.dates.error';

    public function validatedBy(): string
    {
        return BannerDatesValidator::class;
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
