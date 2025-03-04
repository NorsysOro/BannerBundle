<?php

declare(strict_types=1);

namespace Norsys\Bundle\BannerBundle\Tests\Behat\Context;

use Norsys\Bundle\BannerBundle\Entity\Banner;
use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\TestFrameworkBundle\Behat\Context\OroFeatureContext;
use Oro\Bundle\TestFrameworkBundle\Behat\Element\OroPageObjectAware;
use Oro\Bundle\TestFrameworkBundle\Tests\Behat\Context\PageObjectDictionary;

class BannerContext extends OroFeatureContext implements OroPageObjectAware
{
    use PageObjectDictionary;

    /**
     * @beforeScenario
     */
    public function clearData(DoctrineHelper $doctrineHelper)
    {
        $entityManager = $doctrineHelper->getEntityManager(Banner::class);
        $entityManager->createQuery('DELETE FROM NorsysBannerBundle:Banner AS banner')->execute();
    }

    /**
     * @Then /^"([^"]*)" class should be present$/
     */
    public function classShouldBePresent($arg1)
    {
        $sticky = $this->getSession()->getPage()->find('css', '.'.$arg1);
        self::assertNotNull($sticky);
    }
}
