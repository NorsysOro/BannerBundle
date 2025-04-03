<?php

/**
 * @author nverbeke@norsys.fr
 */

declare(strict_types=1);

namespace Norsys\Bundle\BannerBundle\Layout\DataProvider;

use Norsys\Bundle\BannerBundle\Entity\Banner;
use Norsys\Bundle\BannerBundle\Repository\BannerRepository;
use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\ScopeBundle\Manager\ScopeManager;
use Symfony\Component\HttpFoundation\RequestStack;

class BannerProvider
{
    private ScopeManager $scopeManager;
    private DoctrineHelper $doctrineHelper;
    private string $scopeType;
    private RequestStack $requestStack;

    public function __construct(
        ScopeManager $scopeManager,
        DoctrineHelper $doctrineHelper,
        string $scopeType,
        RequestStack $requestStack
    ) {
        $this->scopeManager = $scopeManager;
        $this->doctrineHelper = $doctrineHelper;
        $this->scopeType = $scopeType;
        $this->requestStack = $requestStack;
    }

    public function getActiveBanner(): ?Banner
    {
        /** @var BannerRepository $bannerRepository */
        $bannerRepository = $this->doctrineHelper->getEntityRepositoryForClass(Banner::class);
        $banner = $bannerRepository->getActiveBanner($this->scopeManager->getCriteria($this->scopeType));
        if (null === $banner) {
            return null;
        }

        if (true === $banner->isHomepage()
            && 'oro_cms_frontend_page_view' !== $this->requestStack->getCurrentRequest()?->get('_route')
        ) {
            return null;
        }

        return $banner;
    }
}
