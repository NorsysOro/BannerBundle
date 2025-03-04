<?php

/**
 * @author nverbeke@norsys.fr
 */

declare(strict_types=1);

namespace Norsys\Bundle\BannerBundle\Migrations\Schema\v1_1;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\SchemaException;
use Norsys\Bundle\BannerBundle\Entity\Banner;
use Oro\Bundle\EntityConfigBundle\Migration\RemoveFieldQuery;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class NorsysBannerBundle implements Migration, ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function getMigrationVersion(): string
    {
        return 'v1_1';
    }

    /**
     * @throws SchemaException
     */
    public function up(Schema $schema, QueryBag $queries): void
    {
        $this->removeBannerFields($schema, $queries);
    }

    /**
     * @throws SchemaException
     */
    private function removeBannerFields(Schema $schema, QueryBag $queries): void
    {
        $table = $schema->getTable('norsys_banner');

        $table->dropColumn('background_color');
        $queries->addPostQuery(new RemoveFieldQuery(Banner::class, 'background_color'));

        $table->dropColumn('sticky');
        $queries->addPostQuery(new RemoveFieldQuery(Banner::class, 'sticky'));

        $table->dropColumn('icon');
        $queries->addPostQuery(new RemoveFieldQuery(Banner::class, 'icon'));
    }
}
