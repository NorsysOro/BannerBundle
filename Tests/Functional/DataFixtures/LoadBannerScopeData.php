<?php

/**
 * @author Corentin Svoboda <csvoboda@norsys.fr>
 */

declare(strict_types=1);

namespace Norsys\Bundle\BannerBundle\Tests\Functional\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Oro\Bundle\CustomerBundle\Tests\Functional\DataFixtures\LoadGroups;
use Oro\Bundle\ScopeBundle\Entity\Scope;

class LoadBannerScopeData extends AbstractFixture implements DependentFixtureInterface
{
    public const SCOPE_CUSTOMER_1 = 'scope_customer_1';
    public const SCOPE_CUSTOMER_2 = 'scope_customer_2';
    public const SCOPE_GROUP_1 = 'scope_group_1';

    public function getDependencies(): array
    {
        return [
            LoadCustomerData::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $scope = new Scope();
        /* @phpstan-ignore-next-line */
        $scope->setCustomer($this->getReference('customer.1'));
        $manager->persist($scope);
        $manager->flush();
        $this->setReference(self::SCOPE_CUSTOMER_1, $scope);

        $scope = new Scope();
        /* @phpstan-ignore-next-line */
        $scope->setCustomer($this->getReference('customer.2'));
        $manager->persist($scope);
        $manager->flush();
        $this->setReference(self::SCOPE_CUSTOMER_2, $scope);

        $scope = new Scope();
        /* @phpstan-ignore-next-line */
        $scope->setCustomerGroup($this->getReference(LoadGroups::GROUP1));
        $manager->persist($scope);
        $manager->flush();
        $this->setReference(self::SCOPE_GROUP_1, $scope);
    }
}
