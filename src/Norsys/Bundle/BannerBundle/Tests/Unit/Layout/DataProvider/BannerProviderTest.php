<?php

/**
 * @author Corentin Svoboda <csvoboda@norsys.fr>
 */

declare(strict_types=1);

namespace Norsys\Bundle\BannerBundle\Tests\Unit\Layout\DataProvider;

use Norsys\Bundle\BannerBundle\Entity\Banner;
use Norsys\Bundle\BannerBundle\Layout\DataProvider\BannerProvider;
use Norsys\Bundle\BannerBundle\Repository\BannerRepository;
use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\ScopeBundle\Manager\ScopeManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class BannerProviderTest extends TestCase
{
    private ScopeManager $scopeManager;
    private DoctrineHelper $doctrineHelper;
    private string $scopeType;
    private RequestStack $requestStack;
    private BannerProvider $bannerProvider;
    private BannerRepository $bannerRepository;

    protected function setUp(): void
    {
        $this->scopeManager = $this->createMock(ScopeManager::class);
        $this->doctrineHelper = $this->createMock(DoctrineHelper::class);
        $this->scopeType = 'cms_content_block';
        $this->requestStack = $this->createMock(RequestStack::class);

        $this->bannerProvider = new BannerProvider(
            $this->scopeManager,
            $this->doctrineHelper,
            $this->scopeType,
            $this->requestStack
        );

        $this->bannerRepository = $this->createMock(BannerRepository::class);

        $this->doctrineHelper->expects($this->once())
            ->method('getEntityRepositoryForClass')
            ->with(Banner::class)
            ->willReturn($this->bannerRepository);
    }

    public function testNotFindBanner(): void
    {
        $this->bannerRepository->expects($this->once())
            ->method('getActiveBanner')
            ->willReturn(null);

        $result = $this->bannerProvider->getActiveBanner();

        $this->assertNull($result);
    }

    public function testFindBannerWithHomePageAndRouteHome(): void
    {
        $banner = new Banner();
        $banner->setHomepage(true);

        $this->bannerRepository->expects($this->once())
            ->method('getActiveBanner')
            ->willReturn($banner);

        $request = $this->createMock(Request::class);

        $this->requestStack->expects($this->once())
            ->method('getCurrentRequest')
            ->willReturn($request);

        $request->expects($this->once())
            ->method('get')
            ->with('_route')
            ->willReturn('oro_cms_frontend_page_view');

        $result = $this->bannerProvider->getActiveBanner();

        $this->assertEquals($banner, $result);
    }

    public function testFindBannerWithHomePageAndRouteNotHome(): void
    {
        $banner = new Banner();
        $banner->setHomepage(true);

        $this->bannerRepository->expects($this->once())
            ->method('getActiveBanner')
            ->willReturn($banner);

        $request = $this->createMock(Request::class);

        $this->requestStack->expects($this->once())
            ->method('getCurrentRequest')
            ->willReturn($request);

        $request->expects($this->once())
            ->method('get')
            ->with('_route')
            ->willReturn('other_route');

        $result = $this->bannerProvider->getActiveBanner();

        $this->assertNull($result);
    }

    public function testFindBannerWithNotHomePage(): void
    {
        $banner = new Banner();
        $banner->setHomepage(false);

        $this->bannerRepository->expects($this->once())
            ->method('getActiveBanner')
            ->willReturn($banner);

        $result = $this->bannerProvider->getActiveBanner();

        $this->assertEquals($banner, $result);
    }
}
