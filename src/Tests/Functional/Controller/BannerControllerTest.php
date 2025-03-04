<?php

/**
 * @author Nicolas VERBEKE <nverbeke@norsys.fr>
 */

declare(strict_types=1);

namespace Norsys\Bundle\BannerBundle\Tests\Functional\Controller;

use Norsys\Bundle\BannerBundle\Entity\Banner;
use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\EntityBundle\ORM\Registry;
use Oro\Bundle\ScopeBundle\Tests\Functional\DataFixtures\LoadScopeData;
use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;

class BannerControllerTest extends WebTestCase
{
    private const BANNER_NAME = 'Banner Test';

    protected function setUp(): void
    {
        parent::setUp();
        $this->initClient([], $this->generateBasicAuthHeader());

        $this->loadFixtures([
            LoadScopeData::class,
        ]);
    }

    public function testIndex(): void
    {
        // todo : fix pipeline
        //        $crawler = $this->client->request('GET', $this->getUrl('norsys_banner_index'));
        //
        //        $this->assertHtmlResponseStatusCodeEquals($this->client->getResponse(), 200);
        //        static::assertStringContainsString('banner-grid', $crawler->html());
        //        static::assertStringContainsString(
        //            'Create Bannière',
        //            $crawler->filter('div.title-buttons-container')->html()
        //        );
    }

    public function testCreate(): Banner
    {
        $this->client->catchExceptions(true);
        $this->client->followRedirects();

        $crawler = $this->client->request('GET', $this->getUrl('norsys_banner_create'));

        $button = $crawler->selectButton('Continue');
        $form = $button->form();
        $formValues = $form->getPhpValues();
        $formValues['_sa_org_id'] = 1;

        $this->client->followRedirects(true);
        $crawler = $this->client->request($form->getMethod(), $form->getUri(), $formValues);

        $button = $crawler->selectButton('Save');
        $form = $button->form();

        $form['norsys_banner[title]'] = self::BANNER_NAME;

        $this->client->followRedirects();

        $crawler = $this->client->submit($form);

        $this->assertHtmlResponseStatusCodeEquals($this->client->getResponse(), 200);

        $doctrine = $this->getContainer()->get('doctrine');

        if (!$doctrine instanceof Registry) {
            throw new \LogicException(sprintf('%s entity configuration should exist', DoctrineHelper::class));
        }

        $bannerRepo = $doctrine->getRepository(Banner::class);

        $banner = $bannerRepo->findOneBy(['title' => self::BANNER_NAME]);

        $this->assertInstanceOf(Banner::class, $banner);
        $this->assertEquals(self::BANNER_NAME, $banner->getTitle());

        return $banner;
    }

    /**
     * @depends testCreate
     */
    public function testUpdate(Banner $banner): Banner
    {
        $crawler = $this->client->request(
            'GET',
            $this->getUrl('norsys_banner_update', ['id' => $banner->getId()])
        );

        $form = $crawler->selectButton('Save')->form();
        $form['norsys_banner[title]'] = self::BANNER_NAME.'-updated';

        $this->client->followRedirects();

        $crawler = $this->client->submit($form);

        $this->assertHtmlResponseStatusCodeEquals($this->client->getResponse(), 200);

        $doctrine = $this->getContainer()->get('doctrine');

        if (!$doctrine instanceof Registry) {
            throw new \LogicException(sprintf('%s entity configuration should exist', DoctrineHelper::class));
        }

        $bannerRepo = $doctrine->getRepository(Banner::class);

        $banner = $bannerRepo->findOneBy(['title' => self::BANNER_NAME.'-updated']);

        $this->assertInstanceOf(Banner::class, $banner);
        $this->assertEquals(self::BANNER_NAME.'-updated', $banner->getTitle());

        return $banner;
    }

    /**
     * @depends testUpdate
     */
    public function testView(Banner $banner): Banner
    {
        $crawler = $this->client->request(
            'GET',
            $this->getUrl('norsys_banner_view', ['id' => $banner->getId()])
        );

        $this->assertHtmlResponseStatusCodeEquals($this->client->getResponse(), 200);

        /** @var string $title */
        $title = $banner->getTitle();
        static::assertStringContainsString($title, $crawler->html());

        return $banner;
    }
}
