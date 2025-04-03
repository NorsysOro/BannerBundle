<?php

declare(strict_types=1);

namespace Norsys\Bundle\BannerBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Oro\Bundle\EntityBundle\EntityProperty\DatesAwareInterface;
use Oro\Bundle\EntityBundle\EntityProperty\DatesAwareTrait;
use Oro\Bundle\EntityConfigBundle\Metadata\Attribute\Config;
use Oro\Bundle\EntityConfigBundle\Metadata\Attribute\ConfigField;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityInterface;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityTrait;
use Oro\Bundle\LocaleBundle\Entity\AbstractLocalizedFallbackValue;
use Oro\Bundle\LocaleBundle\Entity\FallbackTrait;
use Oro\Bundle\LocaleBundle\Entity\Localization;
use Oro\Bundle\OrganizationBundle\Entity\OrganizationAwareInterface;
use Oro\Bundle\OrganizationBundle\Entity\Ownership\OrganizationAwareTrait;
use Oro\Bundle\ScopeBundle\Entity\Scope;

#[ORM\Entity(repositoryClass: "Norsys\Bundle\BannerBundle\Repository\BannerRepository")]
#[ORM\Table(name: 'norsys_banner')]
#[ORM\HasLifecycleCallbacks]
#[Config(
    routeName: 'norsys_banner_index',
    routeView: 'norsys_banner_view',
    routeUpdate: 'norsys_banner_update',
    defaultValues: [
        'ownership' => [
            'owner_type' => 'ORGANIZATION',
            'owner_field_name' => 'organization',
            'owner_column_name' => 'organization_id',
        ],
        'security' => [
            'type' => 'ACL',
            'group_name' => '',
        ],
        'dataaudit' => [
            'auditable' => true,
        ],
    ]
)]
class Banner implements DatesAwareInterface, ExtendEntityInterface, OrganizationAwareInterface
{
    use DatesAwareTrait;
    use ExtendEntityTrait;
    use FallbackTrait;
    use OrganizationAwareTrait;

    /**
     * @var int
     */
    #[ORM\Column(type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected $id;

    /**
     * @var Collection|LocalizedBannerContent[]
     */
    #[ORM\OneToMany(
        targetEntity: "Norsys\Bundle\BannerBundle\Entity\LocalizedBannerContent",
        mappedBy: 'banner',
        cascade: ['ALL'],
        orphanRemoval: true,
        fetch: 'EXTRA_LAZY'
    )]
    #[ConfigField(defaultValues: ['attribute' => ['is_attribute' => true]])]
    protected $localizedContents;

    /**
     * @var Collection|LocalizedBannerLink[]
     */
    #[ORM\OneToMany(
        targetEntity: "Norsys\Bundle\BannerBundle\Entity\LocalizedBannerLink",
        mappedBy: 'banner',
        cascade: ['ALL'],
        orphanRemoval: true,
        fetch: 'EXTRA_LAZY'
    )]
    #[ConfigField(
        defaultValues: [
            'dataaudit' => ['auditable' => true],
            'attribute' => ['is_attribute' => true],
        ]
    )]
    protected $localizedLinks;

    /**
     * @var string
     */
    #[ORM\Column(name: 'title', type: 'string', nullable: false)]
    #[ConfigField(defaultValues: ['importexport' => ['excluded' => false]])]
    protected $title;

    /**
     * @var \DateTime
     */
    #[ORM\Column(name: 'start_at', type: 'datetime', nullable: false)]
    protected $start;

    /**
     * @var \DateTime|null
     */
    #[ORM\Column(name: 'end_at', type: 'datetime', nullable: true)]
    protected $end;

    /**
     * @var bool
     */
    #[ORM\Column(name: 'homepage', type: 'boolean', options: ['default' => false], nullable: false)]
    protected $homepage;

    /**
     * @var string|null
     */
    #[ORM\Column(name: 'background_color', type: 'string', options: ['length' => 7], nullable: false)]
    protected $backgroundColor;

    /**
     * @var string|null
     */
    #[ORM\Column(name: 'icon', type: 'string', options: ['length' => 150], nullable: false)]
    protected $icon;

    /**
     * @var bool
     */
    #[ORM\Column(name: 'sticky', type: 'boolean', options: ['default' => false], nullable: false)]
    protected $sticky;

    /**
     * @var ArrayCollection|Scope[]
     */
    #[ORM\ManyToMany(targetEntity: "Oro\Bundle\ScopeBundle\Entity\Scope", fetch: 'EXTRA_LAZY')]
    #[ORM\JoinTable(name: 'norsys_banner_scope')]
    #[ORM\JoinColumn(name: 'banner_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'scope_id', referencedColumnName: 'id', unique: true, onDelete: 'CASCADE')]
    protected $scopes;

    /**
     * @var bool
     */
    #[ORM\Column(name: 'enabled', type: 'boolean', nullable: false, options: ['default' => false])]
    protected $enabled;

    /**
     * @var int
     */
    #[ORM\Column(name: 'priority', type: 'integer', nullable: false, options: ['default' => 0])]
    protected $priority;

    public function __construct()
    {
        $this->localizedContents = new ArrayCollection();
        $this->localizedLinks = new ArrayCollection();
        $this->scopes = new ArrayCollection();
    }

    public function __toString()
    {
        return (string) $this->getId();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setDefaultLocalizedContent(string $value): self
    {
        $this->setDefaultFallbackValue($this->localizedContents, $value, LocalizedBannerContent::class);
        /* @phpstan-ignore-next-line */
        $this->getDefaultLocalizedContent()->setBanner($this);

        return $this;
    }

    /**
     * @return Collection|LocalizedBannerContent[]
     */
    public function getLocalizedContents()
    {
        return $this->localizedContents;
    }

    public function getLocalizedContentByLocalization(
        Localization $localization = null
    ): ?AbstractLocalizedFallbackValue {
        return $this->getFallbackValue($this->localizedContents, $localization);
    }

    public function getDefaultLocalizedContent(): ?AbstractLocalizedFallbackValue
    {
        return $this->getDefaultFallbackValue($this->localizedContents);
    }

    public function addLocalizedContent(LocalizedBannerContent $localizedContent): self
    {
        if (false === $this->localizedContents->contains($localizedContent)) {
            $localizedContent->setBanner($this);
            $this->localizedContents->add($localizedContent);
        }

        return $this;
    }

    public function setLocalizedContents(Collection $localizedContents): self
    {
        $this->localizedContents->clear();
        foreach ($localizedContents as $localizedContent) {
            if (!$localizedContent instanceof LocalizedBannerContent) {
                continue;
            }
            $this->addLocalizedContent($localizedContent);
        }

        return $this;
    }

    public function removeLocalizedContent(LocalizedBannerContent $localizedContent): self
    {
        if ($this->localizedContents->contains($localizedContent)) {
            $this->localizedContents->removeElement($localizedContent);
        }

        return $this;
    }

    public function setDefaultLink(string $value): self
    {
        $this->setDefaultFallbackValue($this->localizedLinks, $value, LocalizedBannerLink::class);
        /* @phpstan-ignore-next-line */
        $this->getDefaultLocalizedLink()->setBanner($this);

        return $this;
    }

    public function setLocalizedLinks(array $links = []): self
    {
        $this->localizedLinks->clear();

        foreach ($links as $link) {
            $this->addLocalizedLink($link);
        }

        return $this;
    }

    public function getLocalizedLinks(): Collection
    {
        return $this->localizedLinks;
    }

    public function addLocalizedLink(LocalizedBannerLink $link): self
    {
        if (false === $this->localizedLinks->contains($link)) {
            $link->setBanner($this);
            $this->localizedLinks->add($link);
        }

        return $this;
    }

    public function removeLocalizedLink(LocalizedBannerLink $link): self
    {
        if ($this->localizedLinks->contains($link)) {
            $this->localizedLinks->removeElement($link);
        }

        return $this;
    }

    public function getLinkByLocalization(Localization $localization = null): ?AbstractLocalizedFallbackValue
    {
        return $this->getFallbackValue($this->localizedLinks, $localization);
    }

    public function getDefaultLocalizedLink(): ?AbstractLocalizedFallbackValue
    {
        return $this->getDefaultFallbackValue($this->localizedLinks);
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getStart(): ?\DateTime
    {
        return $this->start;
    }

    public function setStart(\DateTime $start): self
    {
        $this->start = $start;

        return $this;
    }

    public function getEnd(): ?\DateTime
    {
        return $this->end;
    }

    public function setEnd(?\DateTime $end): self
    {
        $this->end = $end;

        return $this;
    }

    public function getBackgroundColor(): ?string
    {
        return $this->backgroundColor;
    }

    public function setBackgroundColor(?string $backgroundColor): self
    {
        $this->backgroundColor = $backgroundColor;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    /**
     * @return $this
     */
    public function setIcon(?string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    public function isSticky(): ?bool
    {
        return $this->sticky;
    }

    /**
     * @return $this
     */
    public function setSticky(bool $sticky): self
    {
        $this->sticky = $sticky;

        return $this;
    }

    public function isHomepage(): ?bool
    {
        return $this->homepage;
    }

    public function setHomepage(bool $homepage): self
    {
        $this->homepage = $homepage;

        return $this;
    }

    public function getScopes(): Collection
    {
        return $this->scopes;
    }

    public function addScope(Scope $scope): self
    {
        if (false === $this->scopes->contains($scope)) {
            $this->scopes->add($scope);
        }

        return $this;
    }

    public function removeScope(Scope $scope): self
    {
        if ($this->scopes->contains($scope)) {
            $this->scopes->removeElement($scope);
        }

        return $this;
    }

    public function resetScopes(): self
    {
        $this->scopes->clear();

        return $this;
    }

    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getPriority(): ?int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Pre persist event handler.
     */
    #[ORM\PrePersist]
    public function prePersist(): void
    {
        $this->createdAt = new \DateTime('now', new \DateTimeZone('UTC'));
        $this->updatedAt = new \DateTime('now', new \DateTimeZone('UTC'));
    }
}
