<?php

/**
 * @author tlefebvre@norsys.fr
 */

declare(strict_types=1);

namespace Norsys\Bundle\BannerBundle\Form;

use Norsys\Bundle\BannerBundle\Entity\LocalizedBannerContent;
use Norsys\Bundle\BannerBundle\Entity\LocalizedBannerLink;
use Oro\Bundle\FormBundle\Form\Type\CheckboxType;
use Oro\Bundle\FormBundle\Form\Type\OroDateTimeType;
use Oro\Bundle\FormBundle\Form\Type\OroRichTextType;
use Oro\Bundle\LocaleBundle\Form\Type\LocalizedFallbackValueCollectionType;
use Oro\Bundle\ScopeBundle\Form\Type\ScopeCollectionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Count;

/**
 * Suppressing for formTypes.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 */
class BannerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'localizedContents',
                LocalizedFallbackValueCollectionType::class,
                [
                    'label' => 'norsys.banner.localized_contents.label',
                    'required' => false,
                    'value_class' => LocalizedBannerContent::class,
                    'field' => 'text',
                    'entry_type' => OroRichTextType::class,
                    'entry_options' => [
                        'wysiwyg_options' => [
                            'autoRender' => false,
                            'elementpath' => true,
                            'resize' => true,
                            'height' => 200,
                        ],
                    ],
                    'use_tabs' => true,
                ]
            )
            ->add(
                'localizedLinks',
                LocalizedFallbackValueCollectionType::class,
                [
                    'label' => 'norsys.banner.localized_links.label',
                    'required' => false,
                    'value_class' => LocalizedBannerLink::class,
                ]
            )
            ->add(
                'title',
                TextType::class,
                [
                    'label' => 'norsys.banner.title.label',
                    'required' => true,
                ]
            )
            ->add(
                'start',
                OroDateTimeType::class,
                [
                    'label' => 'norsys.banner.start.label',
                    /* @phpstan-ignore-next-line */
                    'data' => $options['data']->getStart() ? $options['data']->getStart() : new \DateTime(),
                ]
            )
            ->add(
                'end',
                OroDateTimeType::class,
                [
                    'label' => 'norsys.banner.end.label',
                    'required' => false,
                ]
            )
            ->add(
                'scopes',
                ScopeCollectionType::class,
                [
                    'label' => 'norsys.banner.scopes.label',
                    'entry_options' => [
                        'scope_type' => 'cms_content_block',
                    ],
                    'required' => true,
                    'constraints' => [new Count(['min' => 1])],
                    'tooltip' => 'norsys.banner.scopes.tooltip',
                ]
            )
            ->add('homepage', CheckboxType::class, [
                    'required' => false,
                    'label' => 'norsys.banner.homepage.label',
                ])
            ->add('enabled', CheckboxType::class, [
                'required' => false,
                ])
            ->add('priority', IntegerType::class, [
                'label' => 'norsys.banner.priority.label',
                /* @phpstan-ignore-next-line */
                'data' => $options['data']->getPriority() ? $options['data']->getPriority() : 0,
                'tooltip' => 'norsys.banner.priority.tooltip',
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'norsys_banner';
    }
}
