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
 *     name="norsys_banner_content",
 *     indexes={
 *
 *         @ORM\Index(name="idx_banner_content_fallback", columns={"fallback"})
 *     }
 * )
 *
 * @ORM\Entity
 *
 * @Config
 */
class LocalizedBannerContent extends AbstractLocalizedFallbackValue
{
    /**
     * @var string|null
     *
     * @ORM\Column(name="text", type="text", nullable=true)
     *
     * @ConfigField(
     *     defaultValues={
     *         "dataaudit": {
     *             "auditable": true
     *         },
     *         "importexport": {
     *             "excluded": false
     *         }
     *     }
     * )
     */
    protected $text;

    /**
     * @var Banner
     *
     * @ORM\ManyToOne(targetEntity="Norsys\Bundle\BannerBundle\Entity\Banner", inversedBy="localizedContents")
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

    /**
     * @return $this
     */
    public function setBanner(Banner $banner): self
    {
        $this->banner = $banner;

        return $this;
    }
}
