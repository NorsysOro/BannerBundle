<?php

/**
 * @author Corentin Svoboda <csvoboda@norsys.fr>
 */

declare(strict_types=1);

namespace Norsys\Bundle\BannerBundle\Tests\Unit\Validator;

use Norsys\Bundle\BannerBundle\Entity\Banner;
use Norsys\Bundle\BannerBundle\Validator\Constraints\BannerDates;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Validation;

class BannerDatesValidatorTest extends TestCase
{
    public function testExceptionUnexpectedType(): void
    {
        $constraint = new BannerDates();

        $banner = new \stdClass();

        $validator = Validation::createValidator();

        $this->expectException(UnexpectedTypeException::class);

        $validator->validate($banner, [
            $constraint,
        ]);
    }

    public function testBannerWithNoStart(): void
    {
        $constraint = new BannerDates();

        $banner = new Banner();

        $banner->setEnd(new \DateTime());

        $validator = Validation::createValidator();

        $violations = $validator->validate($banner, [
            $constraint,
        ]);

        $this->assertCount(0, $violations);
    }

    public function testBannerWithNoEnd(): void
    {
        $constraint = new BannerDates();

        $banner = new Banner();

        $banner->setStart(new \DateTime());

        $validator = Validation::createValidator();

        $violations = $validator->validate($banner, [
            $constraint,
        ]);

        $this->assertCount(0, $violations);
    }

    public function testBannerWithStartBiggerEnd(): void
    {
        $constraint = new BannerDates();

        $banner = new Banner();

        $dateStart = date_add(new \DateTime(), new \DateInterval('P1D'));
        $dateEnd = new \DateTime();

        $banner->setStart(date_add($dateStart, new \DateInterval('P1D')));
        $banner->setEnd($dateEnd);

        $validator = Validation::createValidator();

        $violations = $validator->validate($banner, [
            $constraint,
        ]);

        $this->assertNotCount(0, $violations);
    }

    public function testBannerWithStartLowerEnd(): void
    {
        $constraint = new BannerDates();

        $banner = new Banner();

        $dateStart = new \DateTime();
        $dateEnd = date_add(new \DateTime(), new \DateInterval('P1D'));

        $banner->setStart($dateStart);
        $banner->setEnd($dateEnd);

        $validator = Validation::createValidator();

        $violations = $validator->validate($banner, [
            $constraint,
        ]);

        $this->assertCount(0, $violations);
    }
}
