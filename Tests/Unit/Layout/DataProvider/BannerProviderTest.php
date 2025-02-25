<?php

/**
 * @author Corentin Svoboda <csvoboda@norsys.fr>
 */

declare(strict_types=1);

namespace Norsys\Bundle\BannerBundle\Tests\Unit\Layout\DataProvider;

use Norsys\Bundle\BannerBundle\Entity\Banner;
use Norsys\Bundle\BannerBundle\Layout\DataProvider\BannerProvider;
use Norsys\Bundle\BannerBundle\Repository\BannerRepository;
use Norsys\Bundle\UtilsBundle\Service\Cookies\CookieService;
use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\ScopeBundle\Manager\ScopeManager;
use PHPUnit\Framework\TestCase;

class BannerProviderTest extends TestCase
{
    private DoctrineHelper $doctrineHelper;

    private BannerProvider $bannerProvider;

    private BannerRepository $bannerRepository;

    private CookieService $cookieService;

    protected function setUp(): void
    {
        $scopeManager = $this->createMock(ScopeManager::class);
        $scopeType = 'cms_content_block';

        $this->doctrineHelper = $this->createMock(DoctrineHelper::class);
        $this->bannerRepository = $this->createMock(BannerRepository::class);
        $this->cookieService = $this->createMock(CookieService::class);

        $this->bannerProvider = new BannerProvider(
            $scopeManager,
            $this->doctrineHelper,
            $scopeType,
            $this->cookieService
        );
    }

    public function testNoBanner(): void
    {
        $this->doctrineHelper->expects($this->once())
            ->method('getEntityRepositoryForClass')
            ->with(Banner::class)
            ->willReturn($this->bannerRepository);

        $this->bannerRepository->expects($this->once())
            ->method('getActiveBanners')
            ->willReturn([]);

        $this->assertEmpty($this->bannerProvider->getActiveMessages());
    }

    /**
     * @dataProvider getBanners
     */
    public function testBanners(array $activeBanners, ?string $cookieTimestamp, bool $showBanner): void
    {
        $this->doctrineHelper->expects($this->once())
            ->method('getEntityRepositoryForClass')
            ->with(Banner::class)
            ->willReturn($this->bannerRepository);

        $this->bannerRepository->expects($this->once())
            ->method('getActiveBanners')
            ->willReturn($activeBanners);

        $this->cookieService->expects(self::once())
            ->method('getCookieFromRequest')
            ->with('closed_banner_date')
            ->willReturn($cookieTimestamp);

        $result = $this->bannerProvider->getActiveMessages();

        $this->assertIsArray($result);

        if (true === $showBanner) {
            $this->assertNotEmpty($result);
        } else {
            $this->assertEmpty($result);
        }
    }

    /**
     * Timestamps in js format (ms).
     */
    public function getBanners(): array
    {
        $banner1 = $this->createBanner('banner1', new \DateTime('2023-10-27'));
        $banner2 = $this->createBanner('banner2', new \DateTime('2023-10-28'));
        $banner3 = $this->createBanner('banner3', new \DateTime('2023-11-01'));

        $banners = [0 => $banner1, 1 => $banner2, 2 => $banner3];

        return [
            'closed before last banner was updated' => [
                'banners' => $banners,
                'dateClosed' => '1696111200000', // 2023-09-01
                'showBanner' => true,
            ],
            'closed after last banner was updated' => [
                'banners' => $banners,
                'dateClosed' => '1702767600000', // 2023-11-17
                'showBanner' => false,
            ],
            'never closed' => [
                'banners' => $banners,
                'dateClosed' => null,
                'showBanner' => true,
            ],
        ];
    }

    private function createBanner(string $title, \DateTime $date): Banner
    {
        $banner = new Banner();
        $banner
            ->setTitle($title)
            ->setUpdatedAt($date);

        return $banner;
    }
}
