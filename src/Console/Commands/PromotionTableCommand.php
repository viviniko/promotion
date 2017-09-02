<?php

namespace Viviniko\Promotion\Console\Commands;

use Viviniko\Support\Console\CreateMigrationCommand;

class PromotionTableCommand extends CreateMigrationCommand
{
    protected $name = 'promotion:table';

    protected $description = 'Create a migration for the promotion service table';

    protected $stub = __DIR__ . '/stubs/promotion.stub';

    protected $migration = 'create_promotion_table';
}