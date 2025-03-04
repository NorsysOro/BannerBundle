<?php

/**
 * @author Nicolas VERBEKE <nverbeke@norsys.fr>
 */

declare(strict_types=1);

namespace Norsys\Bundle\BannerBundle\Tests\Unit\Form;

use Norsys\Bundle\BannerBundle\Entity\Banner;
use Norsys\Bundle\BannerBundle\Entity\LocalizedBannerContent;
use Norsys\Bundle\BannerBundle\Entity\LocalizedBannerLink;
use Norsys\Bundle\BannerBundle\Form\BannerType;
use Oro\Bundle\FormBundle\Form\Type\CheckboxType;
use Oro\Bundle\FormBundle\Form\Type\OroDateTimeType;
use Oro\Bundle\FormBundle\Form\Type\OroRichTextType;
use Oro\Bundle\LocaleBundle\Form\Type\LocalizedFallbackValueCollectionType;
use Oro\Bundle\ScopeBundle\Form\Type\ScopeCollectionType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Count;

/**
 * Suppressing for formTypes.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 */
class BannerTypeTest extends TestCase
{
    public function testBuildForm(): void
    {
        $bannerType = new BannerType();

        $banner = new Banner();
        $banner->setStart(new \DateTime());

        $options = [
            'layout_template' => false,
            'data' => $banner,
        ];

        $builder = $this->getMockBuilder('Symfony\Component\Form\FormBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $builder->expects($this->exactly(9))
            ->method('add')
            ->withConsecutive(
                [
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
                    ],
                ],
                [
                    'localizedLinks',
                    LocalizedFallbackValueCollectionType::class,
                    [
                        'label' => 'norsys.banner.localized_links.label',
                        'required' => false,
                        'value_class' => LocalizedBannerLink::class,
                    ],
                ],
                [
                    'title',
                    TextType::class,
                    [
                        'label' => 'norsys.banner.title.label',
                        'required' => true,
                    ],
                ],
                [
                    'start',
                    OroDateTimeType::class,
                    [
                        'label' => 'norsys.banner.start.label',
                        'data' => $options['data']->getStart() ? $options['data']->getStart() : new \DateTime(),
                    ],
                ],
                [
                    'end',
                    OroDateTimeType::class,
                    [
                        'label' => 'norsys.banner.end.label',
                        'required' => false,
                    ],
                ],
                [
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
                    ],
                ],
                [
                    'homepage',
                    CheckboxType::class,
                    [
                        'required' => false,
                        'label' => 'norsys.banner.homepage.label',
                    ],
                ],
                [
                    'enabled',
                    CheckboxType::class,
                    [
                        'required' => false,
                    ],
                ],
                [
                    'priority',
                    IntegerType::class,
                    [
                        'label' => 'norsys.banner.priority.label',
                        'data' => $options['data']->getPriority() ? $options['data']->getPriority() : 0,
                        'tooltip' => 'norsys.banner.priority.tooltip',
                    ],
                ]
            )
            ->willReturnSelf();

        $bannerType->buildForm($builder, $options);
    }
}
