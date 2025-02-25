<?php

/**
 * @author tlefebvre@norsys.fr
 */

declare(strict_types=1);

namespace Norsys\Bundle\BannerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\Config;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\ConfigField;
use Oro\Bundle\LocaleBundle\Entity\AbstractLocalizedFallbackValue;

/**
 * @ORM\Table(
 *     name="norsys_banner_link",
 *     indexes={
 *
 *         @ORM\Index(name="idx_banner_link_fallback", columns={"fallback"}),
 *         @ORM\Index(name="idx_banner_link_string", columns={"string"})
 *     }
 * )
 *
 * @ORM\Entity
 *
 * @Config
 */
class LocalizedBannerLink extends AbstractLocalizedFallbackValue
{
    /**
     * @var string|null
     *
     * @ORM\Column(name="string", type="string", length=255, nullable=true)
     *
     * @ConfigField(
     *     defaultValues={
     *         "importexport": {
     *             "excluded": false
     *         }
     *     }
     * )
     */
    protected $string;

    /**
     * @var Banner
     *
     * @ORM\ManyToOne(targetEntity="Norsys\Bundle\BannerBundle\Entity\Banner", inversedBy="localizedLinks")
     *
     * @ORM\JoinColumn(name="banner_id", referencedColumnName="id", onDelete="CASCADE")
     *
     * @ConfigField(
     *     defaultValues={
     *         "importexport": {
     *             "excluded": true
     *         }
     *     }
     * )
     */
    protected $banner;

    public function getBanner(): ?Banner
    {
        return $this->banner;
    }

    public function setBanner(?Banner $banner): self
    {
        /* @phpstan-ignore-next-line */
        $this->banner = $banner;

        return $this;
    }
}
