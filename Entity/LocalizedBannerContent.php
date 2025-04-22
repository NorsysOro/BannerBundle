<?php

/**
 * @author nverbeke@norsys.fr
 */

declare(strict_types=1);

namespace Norsys\Bundle\BannerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Oro\Bundle\EntityConfigBundle\Metadata\Attribute\Config;
use Oro\Bundle\EntityConfigBundle\Metadata\Attribute\ConfigField;
use Oro\Bundle\LocaleBundle\Entity\AbstractLocalizedFallbackValue;

#[ORM\Entity]
#[ORM\Table(name: 'norsys_banner_content')]
#[ORM\Index(columns: ['fallback'], name: 'idx_banner_content_fallback')]
#[Config]
class LocalizedBannerContent extends AbstractLocalizedFallbackValue
{
    #[ORM\Column(name: 'text', type: 'text', nullable: true)]
    #[ConfigField(defaultValues: [
        'dataaudit' => ['auditable' => true],
        'importexport' => ['excluded' => false],
    ])]
    protected ?string $text = null;

    #[ORM\ManyToOne(targetEntity: Banner::class, inversedBy: 'localizedContents')]
    #[ORM\JoinColumn(name: 'banner_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ConfigField(defaultValues: ['importexport' => ['excluded' => true]])]
    protected ?Banner $banner = null;

    public function getBanner(): ?Banner
    {
        return $this->banner;
    }

    public function setBanner(Banner $banner): self
    {
        $this->banner = $banner;

        return $this;
    }
}
