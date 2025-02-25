<?php

/**
 * @author Corentin Svoboda <csvoboda@norsys.fr>
 */

declare(strict_types=1);

namespace Norsys\Bundle\BannerBundle\Tests\Functional\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Norsys\Bundle\BannerBundle\Entity\Banner;
use Norsys\Bundle\BannerBundle\Entity\LocalizedBannerContent;
use Norsys\Bundle\BannerBundle\Entity\LocalizedBannerLink;
use Oro\Bundle\ScopeBundle\Entity\Scope;
use Oro\Bundle\ScopeBundle\Tests\Functional\DataFixtures\LoadScopeData;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class LoadBannerData extends AbstractFixture implements ContainerAwareInterface, DependentFixtureInterface
{
    use ContainerAwareTrait;

    public function load(ObjectManager $manager): void
    {
        $bannersField = [
            'banner1' => [
                'title' => 'banner general test',
                'enabled' => true,
                'priority' => 1,
                'scope' => $this->getReference(LoadScopeData::DEFAULT_SCOPE),
                'start' => new \DateTime('2030-10-10 12:00:00'),
                'end' => new \DateTime('2030-10-15 12:00:00'),
                'content' => '<p> banner general test </p>>',
            ],
            'banner2' => [
                'title' => 'banner customer 1 test',
                'enabled' => true,
                'priority' => 1,
                'scope' => $this->getReference(LoadBannerScopeData::SCOPE_CUSTOMER_1),
                'start' => new \DateTime('2030-10-10 12:00:00'),
                'end' => null,
                'content' => '<p> banner customer 1 test </p>>',
            ],
            'banner3' => [
                'title' => 'banner customer 2 test',
                'enabled' => true,
                'priority' => 1,
                'scope' => $this->getReference(LoadBannerScopeData::SCOPE_CUSTOMER_2),
                'start' => date_sub(new \DateTime(), new \DateInterval('P1D')),
                'end' => date_add(new \DateTime(), new \DateInterval('P1D')),
                'content' => '<p> banner customer 2 test </p>>',
            ],
            'banner4' => [
                'title' => 'banner group 1 test',
                'enabled' => true,
                'priority' => 2,
                'scope' => $this->getReference(LoadBannerScopeData::SCOPE_GROUP_1),
                'start' => date_sub(new \DateTime(), new \DateInterval('P1D')),
                'end' => date_add(new \DateTime(), new \DateInterval('P1D')),
                'content' => '<p> banner group 1 test </p>>',
            ],
            'banner5' => [
                'title' => 'banner customer 1 test finish',
                'enabled' => true,
                'priority' => 2,
                'scope' => $this->getReference(LoadBannerScopeData::SCOPE_CUSTOMER_1),
                'start' => date_sub(new \DateTime(), new \DateInterval('P1Y2D')),
                'end' => date_sub(new \DateTime(), new \DateInterval('P1Y1D')),
                'content' => '<p> banner group 1 test finish </p>>',
            ],
        ];

        foreach ($bannersField as $key => $bannerField) {
            $localizedBannerContent = new LocalizedBannerContent();
            $localizedBannerContent->setText($bannerField['content']);

            $localizedBannerLink = new LocalizedBannerLink();
            $localizedBannerLink->setString('https://www.google.com/');

            $banner = new Banner();
            $banner->setTitle($bannerField['title']);
            $banner->setHomepage(false);
            $banner->setEnabled($bannerField['enabled']);
            $banner->setPriority($bannerField['priority']);

            $scope = $bannerField['scope'];
            if ($scope instanceof Scope) {
                $banner->addScope($scope);
            }
            $banner->setStart($bannerField['start']);

            $end = $bannerField['end'];
            if ($end instanceof \DateTime) {
                $banner->setEnd($end);
            }
            $banner->addLocalizedContent($localizedBannerContent);
            $banner->addLocalizedLink($localizedBannerLink);

            $manager->persist($banner);

            $this->addReference($key, $banner);
        }

        $manager->flush();

        $banner = $this->getReference('banner4');

        if (!$banner instanceof Banner) {
            return;
        }

        $banner->setUpdatedAt(date_add(new \DateTime(), new \DateInterval('P1D')));
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            LoadScopeData::class,
            LoadBannerScopeData::class,
        ];
    }
}
