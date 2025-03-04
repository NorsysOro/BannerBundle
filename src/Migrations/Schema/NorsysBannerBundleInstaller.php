<?php

/**
 * @author nverbeke@norsys.fr
 */

declare(strict_types=1);

namespace Norsys\Bundle\BannerBundle\Migrations\Schema;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\SchemaException;
use Oro\Bundle\EntityExtendBundle\Migration\Extension\ExtendExtension;
use Oro\Bundle\EntityExtendBundle\Migration\Extension\ExtendExtensionAwareInterface;
use Oro\Bundle\MigrationBundle\Migration\Installation;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class NorsysBannerBundleInstaller implements Installation, ContainerAwareInterface, ExtendExtensionAwareInterface
{
    use ContainerAwareTrait;

    private ExtendExtension $extendExtension;

    public function setExtendExtension(ExtendExtension $extendExtension): void
    {
        $this->extendExtension = $extendExtension;
    }

    public function getMigrationVersion(): string
    {
        return 'v1_1';
    }

    /**
     * @throws SchemaException
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function up(Schema $schema, QueryBag $queries): void
    {
        $this->createBannerTable($schema);
        $this->createLocalizedBannerContentTable($schema);
        $this->createLocalizedBannerLink($schema);
        $this->createBannerScopeTable($schema);
    }

    /**
     * @throws SchemaException
     */
    private function createBannerTable(Schema $schema): void
    {
        $table = $schema->createTable('norsys_banner');

        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('title', 'string', ['notnull' => true]);
        $table->addColumn('created_at', 'datetime', ['notnull' => true]);
        $table->addColumn('updated_at', 'datetime', ['notnull' => true]);
        $table->addColumn('organization_id', 'integer', ['notnull' => false]);
        $table->addColumn('start_at', 'datetime', ['notnull' => true]);
        $table->addColumn('end_at', 'datetime', ['notnull' => false]);
        $table->addColumn('homepage', 'boolean', ['notnull' => true, 'default' => false]);
        $table->addColumn('enabled', 'boolean', ['notnull' => true, 'default' => false]);
        $table->addColumn('priority', 'integer', ['notnull' => true, 'default' => 0]);

        $table->setPrimaryKey(['id']);

        $table->addForeignKeyConstraint(
            $schema->getTable('oro_organization'),
            ['organization_id'],
            ['id'],
            ['onDelete' => 'SET NULL']
        );
    }

    private function createLocalizedBannerContentTable(Schema $schema): void
    {
        $table = $schema->createTable('norsys_banner_content');

        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('banner_id', 'integer', ['notnull' => false]);
        $table->addColumn('localization_id', 'integer', ['notnull' => false]);
        $table->addColumn('fallback', 'string', ['notnull' => false, 'length' => 64]);
        $table->addColumn('text', 'text', ['notnull' => false]);
        $table->setPrimaryKey(['id']);
        $table->addIndex(['fallback'], 'idx_banner_content_fallback', []);

        $table->addForeignKeyConstraint(
            $schema->getTable('norsys_banner'),
            ['banner_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => 'CASCADE']
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_localization'),
            ['localization_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => 'CASCADE']
        );
    }

    /**
     * @throws SchemaException
     */
    private function createLocalizedBannerLink(Schema $schema): void
    {
        $table = $schema->createTable('norsys_banner_link');

        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('banner_id', 'integer', ['notnull' => false]);
        $table->addColumn('localization_id', 'integer', ['notnull' => false]);
        $table->addColumn('fallback', 'string', ['notnull' => false, 'length' => 64]);
        $table->addColumn('string', 'string', ['notnull' => false]);
        $table->setPrimaryKey(['id']);
        $table->addIndex(['fallback'], 'idx_banner_link_fallback', []);
        $table->addIndex(['string'], 'idx_banner_link_string', []);

        $table->addForeignKeyConstraint(
            $schema->getTable('norsys_banner'),
            ['banner_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => 'CASCADE']
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_localization'),
            ['localization_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => 'CASCADE']
        );
    }

    private function createBannerScopeTable(Schema $schema): void
    {
        $table = $schema->createTable('norsys_banner_scope');
        $table->addColumn('banner_id', 'integer', []);
        $table->addColumn('scope_id', 'integer', []);
        $table->setPrimaryKey(['banner_id', 'scope_id']);

        $table->addForeignKeyConstraint(
            $schema->getTable('norsys_banner'),
            ['banner_id'],
            ['id'],
            ['onDelete' => 'CASCADE', 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_scope'),
            ['scope_id'],
            ['id'],
            ['onDelete' => 'CASCADE', 'onUpdate' => null]
        );
    }
}
