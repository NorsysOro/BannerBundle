<?php

/**
 * @author nverbeke@norsys.fr
 */

declare(strict_types=1);

namespace Norsys\Bundle\BannerBundle\Layout\DataProvider;

use Norsys\Bundle\BannerBundle\Entity\Banner;
use Norsys\Bundle\BannerBundle\Repository\BannerRepository;
use Norsys\Bundle\BannerBundle\Service\Cookies\CookieService;
use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\ScopeBundle\Manager\ScopeManager;

/**
 * Returns active banners by scope according to the 'closed_banner_date' cookie value.
 */
class BannerProvider
{
    private const BANNER_COOKIE_NAME = 'closed_banner_date';

    public function __construct(
        private readonly ScopeManager $scopeManager,
        private readonly DoctrineHelper $doctrineHelper,
        private readonly string $scopeType,
        private readonly CookieService $cookieService
    ) {
    }

    /**
     * For the frontend, a banner is a message, and we can render several at once.
     */
    public function getActiveMessages(): array
    {
        /** @var BannerRepository $bannerRepository */
        $bannerRepository = $this->doctrineHelper->getEntityRepositoryForClass(Banner::class);

        $activeBanners = $bannerRepository->getActiveBanners($this->scopeManager->getCriteria($this->scopeType));

        if (
            true === empty($activeBanners)
            || false === is_array($activeBanners)
            || false === $this->authorizeBannerRendering($activeBanners)
        ) {
            return [];
        }

        return $activeBanners;
    }

    /**
     * From the cookie 'closed_banner_date' date value, if there are any new messages since then, render the banner.
     */
    private function authorizeBannerRendering(array $activeBanners): bool
    {
        $closedDate = $this->cookieService->getCookieFromRequest(self::BANNER_COOKIE_NAME);

        if (null === $closedDate) {
            return true;
        }

        return $this->getLatestBannerTimestamp($activeBanners) > (int) $closedDate;
    }

    /**
     * Get the latest updated banner date and convert it in ms like javascript.
     */
    private function getLatestBannerTimestamp(array $activeBanners): int
    {
        usort($activeBanners, function ($bannerA, $bannerB) {
            $bannerATimestamp = $bannerA->getUpdatedAt()->getTimestamp();
            $bannerBTimestamp = $bannerB->getUpdatedAt()->getTimestamp();

            return $bannerBTimestamp - $bannerATimestamp;
        });

        /** @var \DateTime $latestDate */
        $latestDate = $activeBanners[0]->getUpdatedAt();

        return (int) $latestDate->format('Uv');
    }
}
