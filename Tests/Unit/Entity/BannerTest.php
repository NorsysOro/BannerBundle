<?php

/**
 * @author Corentin Svoboda <csvoboda@norsys.fr>
 */

declare(strict_types=1);

namespace Norsys\Bundle\BannerBundle\Tests\Unit\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Norsys\Bundle\BannerBundle\Entity\Banner;
use Norsys\Bundle\BannerBundle\Entity\LocalizedBannerContent;
use Norsys\Bundle\BannerBundle\Entity\LocalizedBannerLink;
use Oro\Bundle\LocaleBundle\Entity\Localization;
use Oro\Bundle\LocaleBundle\Entity\LocalizedFallbackValue;
use Oro\Bundle\ScopeBundle\Entity\Scope;
use Oro\Component\Testing\Unit\EntityTestCaseTrait;
use PHPUnit\Framework\TestCase;

class BannerTest extends TestCase
{
    use EntityTestCaseTrait;

    public const LOCALIZED_VALUE = 'some string';

    private Banner $banner;

    protected function setUp(): void
    {
        $this->banner = new Banner();
    }

    public function testAccessors(): void
    {
        $this->assertPropertyAccessors(new Banner(), [
            ['title', 'title'],
            ['homepage', true],
            ['start', new \DateTime('now')],
            ['end', new \DateTime('now')],
            ['enabled', true],
            ['priority', 1],
        ]);

        $this->assertPropertyCollections(new Banner(), [
            ['localizedContents', new LocalizedBannerContent()],
            ['localizedLinks', new LocalizedBannerLink()],
            ['scopes', new Scope()],
        ]);
    }

    public function testConstruct(): void
    {
        $banner = $this->banner;

        $this->assertInstanceOf('Doctrine\Common\Collections\Collection', $banner->getScopes());
        $this->assertEmpty($banner->getScopes()->toArray());

        $this->assertInstanceOf('Doctrine\Common\Collections\Collection', $banner->getLocalizedContents());
        $this->assertEmpty($banner->getLocalizedContents()->toArray());

        $this->assertInstanceOf('Doctrine\Common\Collections\Collection', $banner->getLocalizedLinks());
        $this->assertEmpty($banner->getLocalizedLinks()->toArray());
    }

    public function testGetDefaultLocalizedContent(): void
    {
        $defaultLocalizedContent = $this->createLocalizedValue(true, LocalizedBannerContent::class);

        if (!$defaultLocalizedContent instanceof LocalizedBannerContent) {
            return;
        }
        $localizedContent = $this->createLocalizedValue(false, LocalizedBannerContent::class);

        if (!$localizedContent instanceof LocalizedBannerContent) {
            return;
        }
        $banner = $this->banner;
        $banner->addLocalizedContent($defaultLocalizedContent)
            ->addLocalizedContent($localizedContent);

        $this->assertEquals($defaultLocalizedContent, $banner->getDefaultLocalizedContent());
    }

    public function testSetDefaultLocalizedContent(): void
    {
        $defaultLocalizedContent = 'default localized content';

        $banner = $this->banner;

        $banner->setDefaultLocalizedContent($defaultLocalizedContent);

        $this->assertEquals($defaultLocalizedContent, $banner->getDefaultLocalizedContent());
    }

    public function testSetDefaultLocalizedContents(): void
    {
        $defaultLocalizedContents = new ArrayCollection();
        $defaultLocalizedContents->add($this->createLocalizedValue(true, LocalizedBannerContent::class));
        $defaultLocalizedContents->add($this->createLocalizedValue(false, LocalizedBannerContent::class));
        $defaultLocalizedContents->add([]);

        $banner = $this->banner;
        $banner->setLocalizedContents($defaultLocalizedContents);

        $this->assertInstanceOf(ArrayCollection::class, $defaultLocalizedContents);
        $this->assertEquals(2, count($banner->getLocalizedContents()));
    }

    public function testSetDefaultLink(): void
    {
        $defaultLink = 'default link';

        $banner = $this->banner;
        $banner->setDefaultLink($defaultLink);

        $this->assertEquals($defaultLink, $banner->getDefaultLocalizedLink());
    }

    public function testSetDefaultLinks(): void
    {
        $defaultLink = new LocalizedBannerLink();
        $defaultLink->setString('default link');

        $link = new LocalizedBannerLink();
        $link->setString('link');

        $banner = $this->banner;
        $banner->setLocalizedLinks([$defaultLink, $link]);

        $this->assertEquals(2, count($banner->getLocalizedLinks()));
    }

    public function testGetDefaultLocalizedLink(): void
    {
        $defaultLocalizedLink = $this->createLocalizedValue(true, LocalizedBannerLink::class);
        if (!$defaultLocalizedLink instanceof LocalizedBannerLink) {
            return;
        }

        $localizedLink = $this->createLocalizedValue(false, LocalizedBannerLink::class);
        if (!$localizedLink instanceof LocalizedBannerLink) {
            return;
        }

        $banner = $this->banner;
        $banner->addLocalizedLink($defaultLocalizedLink)
            ->addLocalizedLink($localizedLink);

        $this->assertEquals($defaultLocalizedLink, $banner->getDefaultLocalizedLink());
    }

    public function testGetByLocalization(): void
    {
        $localization = new Localization();

        $localizedBannerContent = new LocalizedBannerContent();
        $localizedBannerContent->setLocalization($localization);

        $localizedBannerLink = new LocalizedBannerLink();
        $localizedBannerLink->setLocalization($localization);

        $this->banner->addLocalizedContent($localizedBannerContent);
        $this->banner->addLocalizedLink($localizedBannerLink);

        $this->assertEquals($localizedBannerContent, $this->banner->getLocalizedContentByLocalization($localization));
        $this->assertEquals($localizedBannerLink, $this->banner->getLinkByLocalization($localization));
    }

    protected function createLocalizedValue(bool $default = false, string $className = LocalizedFallbackValue::class): mixed
    {
        /* @phpstan-ignore-next-line */
        $localized = (new $className())->setString(self::LOCALIZED_VALUE);

        if (!$default) {
            $localized->setLocalization(new Localization());
        }

        return $localized;
    }

    public function testResetScope(): void
    {
        $scope = new Scope();
        $this->banner->addScope($scope);

        $this->assertCount(1, $this->banner->getScopes());
        $this->banner->resetScopes();
        $this->assertCount(0, $this->banner->getScopes());
    }
}
