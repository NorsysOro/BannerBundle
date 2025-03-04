<?php

/**
 * @author Nicolas VERBEKE <nverbeke@norsys.fr>
 */

declare(strict_types=1);

namespace Norsys\Bundle\BannerBundle\Tests\Functional\Repository;

use Doctrine\Persistence\ManagerRegistry;
use Norsys\Bundle\BannerBundle\Entity\Banner;
use Norsys\Bundle\BannerBundle\Repository\BannerRepository;
use Norsys\Bundle\BannerBundle\Tests\Functional\DataFixtures\LoadBannerData;
use Oro\Bundle\CustomerBundle\Tests\Functional\DataFixtures\LoadGroups;
use Oro\Bundle\ScopeBundle\Manager\ScopeManager;
use Oro\Bundle\ScopeBundle\Model\ScopeCriteria;
use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;

/**
 * @dbIsolationPerTest
 */
class BannerRepositoryTest extends WebTestCase
{
    protected object|null $registry;
    protected object|null $scopeManager;

    protected BannerRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->initClient();

        $this->scopeManager = $this->getContainer()->get('oro_scope.scope_manager');
        $this->registry = $this->getContainer()->get('doctrine');

        $this->loadFixtures([
            LoadBannerData::class,
        ]);
    }

    public function testGetActiveBannersByPriority(): void
    {
        $scopeManager = $this->scopeManager;

        $this->isScopeManager($scopeManager);

        /** @var ScopeManager $scopeManager */
        $criteria = $scopeManager->getCriteria(
            'cms_content_block',
            [
                'customer' => $this->getReference('customer.2'),
                'customerGroup' => $this->getReference(LoadGroups::GROUP1),
            ]
        );

        $result = $this->getBannersFromCriteria($criteria);

        $this->assertIsArray($result);
        $this->assertEquals(2, count($result));
        $this->assertEquals('banner group 1 test', $result[0]->getTitle());
    }

    public function testGetActiveBannersNotStart(): void
    {
        $scopeManager = $this->scopeManager;

        $this->isScopeManager($scopeManager);

        /** @var ScopeManager $scopeManager */
        $criteria = $scopeManager->getCriteria(
            'cms_content_block',
            [
                'customer' => $this->getReference('customer.1'),
            ]
        );

        $result = $this->getBannersFromCriteria($criteria);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    private function isScopeManager(object|null $scopeManager): void
    {
        if (false === $scopeManager instanceof ScopeManager) {
            throw new \LogicException(sprintf('%s entity configuration should exist', ScopeManager::class));
        }
    }

    private function isRegistry(object|null $registry): void
    {
        if (false === $registry instanceof ManagerRegistry) {
            throw new \LogicException(sprintf('%s entity configuration should exist', ManagerRegistry::class));
        }
    }

    private function getBannersFromCriteria(ScopeCriteria $criteria): mixed
    {
        $registry = $this->registry;

        $this->isRegistry($registry);

        /** @var ManagerRegistry $registry */
        $repository = $registry->getRepository(Banner::class);

        /* @var BannerRepository $repository */
        return $repository->getActiveBanners($criteria);
    }
}
