<?php

/**
 * @author Nicolas VERBEKE <nverbeke@norsys.fr>
 */

declare(strict_types=1);

namespace Norsys\Bundle\BannerBundle\Validator\Constraints;

use Norsys\Bundle\BannerBundle\Entity\Banner;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class BannerDatesValidator extends ConstraintValidator
{
    public const ALIAS = 'norsys_news_dates_validator';

    /* @phpstan-ignore-next-line */
    public function validate($value, Constraint $constraint): void
    {
        if (!$value instanceof Banner) {
            throw new UnexpectedTypeException($value, Banner::class);
        }

        if (null === $value->getStart()) {
            return;
        }

        if (null === $value->getEnd()) {
            return;
        }

        if ($value->getEnd() <= $value->getStart()) {
            $this->context
                /* @phpstan-ignore-next-line */
                ->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
