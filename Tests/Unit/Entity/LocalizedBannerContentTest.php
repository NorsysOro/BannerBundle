<?php

/**
 * @author Corentin Svoboda <csvoboda@norsys.fr>
 */

declare(strict_types=1);

namespace Norsys\Bundle\BannerBundle\Tests\Unit\Entity;

use Norsys\Bundle\BannerBundle\Entity\Banner;
use Norsys\Bundle\BannerBundle\Entity\LocalizedBannerContent;
use Oro\Component\Testing\Unit\EntityTestCaseTrait;
use PHPUnit\Framework\TestCase;

class LocalizedBannerContentTest extends TestCase
{
    use EntityTestCaseTrait;

    public function testAccessors(): void
    {
        $this->assertPropertyAccessors(new LocalizedBannerContent(), [
            ['banner', new Banner()],
        ]);
    }
}
