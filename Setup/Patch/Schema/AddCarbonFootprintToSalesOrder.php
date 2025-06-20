<?php

namespace Convert\Carbonfootprint\Setup\Patch\Schema;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class AddCarbonFootprintToSalesOrder implements SchemaPatchInterface
{
    private SchemaSetupInterface $schemaSetup;

    public function __construct(SchemaSetupInterface $schemaSetup)
    {
        $this->schemaSetup = $schemaSetup;
    }

    public function apply(): void
    {
        $this->schemaSetup->startSetup();

        $connection = $this->schemaSetup->getConnection();

        if (!$connection->tableColumnExists('sales_order', 'carbon_footprint')) {
            $connection->addColumn(
                'sales_order',
                'carbon_footprint',
                [
                    'type' => Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => true,
                    'comment' => 'Carbon Footprint (kg CO2)'
                ]
            );
        }

        $this->schemaSetup->endSetup();
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }
}
