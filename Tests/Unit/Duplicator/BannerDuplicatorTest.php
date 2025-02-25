<?php
/**
 * @author Corentin Svoboda <csvoboda@norsys.fr>
 */

declare(strict_types=1);

namespace Norsys\Bundle\BannerBundle\Tests\Unit\Duplicator;

use Doctrine\ORM\EntityManager;
use Norsys\Bundle\BannerBundle\Duplicator\BannerDuplicator;
use Norsys\Bundle\BannerBundle\Entity\Banner;
use Norsys\Bundle\BannerBundle\Entity\LocalizedBannerContent;
use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\LocaleBundle\Entity\Localization;
use PHPUnit\Framework\TestCase;

class BannerDuplicatorTest extends TestCase
{
    public const TITLE = 'Banner Title';
    public const LOCALIZED_CONTENT = 'Localized Content';

    private DoctrineHelper $doctrineHelper;
    private BannerDuplicator $bannerDuplicator;

    protected function setUp(): void
    {
        $this->doctrineHelper = $this->createMock(DoctrineHelper::class);

        $this->bannerDuplicator = new BannerDuplicator(
            $this->doctrineHelper
        );
    }

    public function testDuplicate(): void
    {
        $localization = new Localization();

        $localizedContent = new LocalizedBannerContent();
        $localizedContent->setString(self::LOCALIZED_CONTENT)
            ->setLocalization($localization);

        $banner = new Banner();
        $banner->setTitle(self::TITLE)
            ->setEnabled(true)
            ->addLocalizedContent($localizedContent);

        $entityManager = $this->createMock(EntityManager::class);

        $this->doctrineHelper->expects($this->once())
            ->method('getEntityManager')
            ->with($banner)
            ->willReturn($entityManager);

        $bannerCopy = $this->bannerDuplicator->duplicate($banner);

        $this->assertEquals(self::TITLE.' copy', $bannerCopy->getTitle());
        $this->assertFalse($bannerCopy->isEnabled());
        $this->assertEquals(
            self::LOCALIZED_CONTENT,
            /* @phpstan-ignore-next-line */
            $bannerCopy->getLocalizedContentByLocalization($localization)->getString()
        );
    }

    public function testDuplicateException(): void
    {
        $banner = new Banner();

        $this->doctrineHelper->expects($this->once())
            ->method('getEntityManager')
            ->with($banner)
            ->willReturn(null);

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Doctrine\ORM\EntityManager entity configuration should exist');
        $this->bannerDuplicator->duplicate($banner);
    }
}
