<?php

declare(strict_types=1);

namespace Norsys\Bundle\BannerBundle\Controller\Admin;

use Norsys\Bundle\BannerBundle\Entity\Banner;
use Norsys\Bundle\BannerBundle\Form\BannerType;
use Oro\Bundle\FormBundle\Model\UpdateHandlerFacade;
use Oro\Bundle\ScopeBundle\Manager\ScopeManager;
use Oro\Bundle\SecurityBundle\Attribute\Acl;
use Oro\Bundle\SecurityBundle\Attribute\AclAncestor;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\Translator;

class BannerController extends AbstractController
{
    private ScopeManager $scopeManager;
    private UpdateHandlerFacade $handlerFacade;
    private Translator $translator;

    public function __construct(
        ScopeManager $scopeManager,
        UpdateHandlerFacade $handlerFacade,
        Translator $translator
    ) {
        $this->scopeManager = $scopeManager;
        $this->handlerFacade = $handlerFacade;
        $this->translator = $translator;
    }

    /**
     * @Route("/", name="norsys_banner_index")
     *
     * @Template("@NorsysBanner/banner/index.html.twig")
     */
    #[AclAncestor('norsys_banner_view')]
    public function indexAction(): array
    {
        return [
            'entity_class' => Banner::class,
        ];
    }

    /**
     * @Route("/create", name="norsys_banner_create")
     *
     * @Template("@NorsysBanner/banner/update.html.twig")
     */
    #[Acl(id: 'norsys_banner_create', type: 'entity', class: Banner::class, permission: 'CREATE')]
    public function createAction(): RedirectResponse|array
    {
        return $this->update(new Banner());
    }

    /**
     * @Route("/update/{id}", name="norsys_banner_update", requirements={"id": "\d+"})
     *
     * @Template("@NorsysBanner/banner/update.html.twig")
     */
    #[Acl(id: 'norsys_banner_update', type: 'entity', class: Banner::class, permission: 'EDIT')]
    public function updateAction(Banner $banner): RedirectResponse|array
    {
        return $this->update($banner);
    }

    /**
     * @Route("/view/{id}", name="norsys_banner_view", requirements={"id": "\d+"})
     *
     * @Template("@NorsysBanner/banner/view.html.twig")
     */
    #[Acl(id: 'norsys_banner_view', type: 'entity', class: Banner::class, permission: 'VIEW')]
    public function viewAction(Banner $banner): array
    {
        $scopeEntities = $this->scopeManager->getScopeEntities('cms_content_block');

        return [
            'entity' => $banner,
            'scopeEntities' => array_reverse($scopeEntities),
        ];
    }

    protected function update(Banner $banner): RedirectResponse|array
    {
        return $this->handlerFacade->update(
            $banner,
            $this->createForm(BannerType::class, $banner),
            $this->translator->trans('norsys.banner.form.update.messages.saved')
        );
    }
}
