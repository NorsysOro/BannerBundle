<?php

/**
 * @author Nicolas VERBEKE <nverbeke@norsys.fr>
 */

declare(strict_types=1);

namespace Norsys\Bundle\BannerBundle\Tests\Functional\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Oro\Bundle\CustomerBundle\Entity\Customer;
use Oro\Bundle\CustomerBundle\Entity\CustomerGroup;
use Oro\Bundle\CustomerBundle\Tests\Functional\DataFixtures\LoadGroups;

class LoadCustomerData extends AbstractFixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $customer = new Customer();
        $customer->setName('customer.1');
        $manager->persist($customer);
        $this->addReference('customer.1', $customer);

        $customer2 = new Customer();
        $customer2->setName('customer.2');

        $group = $this->getReference(LoadGroups::GROUP1);

        if (!$group instanceof CustomerGroup) {
            return;
        }

        $customer2->setGroup($group);
        $manager->persist($customer2);
        $this->addReference('customer.2', $customer2);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            LoadGroups::class,
        ];
    }
}
