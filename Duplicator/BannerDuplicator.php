<?php

/**
 * @author Corentin Svoboda <csvoboda@norsys.fr>
 */

declare(strict_types=1);

namespace Norsys\Bundle\BannerBundle\Duplicator;

use Doctrine\ORM\EntityManager;
use Norsys\Bundle\BannerBundle\Entity\Banner;
use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;

class BannerDuplicator
{
    private DoctrineHelper $doctrineHelper;

    public function __construct(DoctrineHelper $doctrineHelper)
    {
        $this->doctrineHelper = $doctrineHelper;
    }

    public function duplicate(Banner $banner): Banner
    {
        $objectManager = $this->doctrineHelper->getEntityManager($banner);

        if (!$objectManager instanceof EntityManager) {
            throw new \LogicException(sprintf('%s entity configuration should exist', EntityManager::class));
        }
        $bannerCopy = $this->createBannerCopy($banner);

        $objectManager->persist($bannerCopy);
        $objectManager->flush();

        return $bannerCopy;
    }

    private function createBannerCopy(Banner $banner): Banner
    {
        $bannerCopy = clone $banner;

        $bannerCopy->setTitle($bannerCopy->getTitle().' copy');
        $bannerCopy->setEnabled(false);

        return $bannerCopy;
    }
}
