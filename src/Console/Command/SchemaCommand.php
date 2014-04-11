<?php

namespace Layer\Console\Command;

use Illuminate\Database\Schema\Grammars\MySqlGrammar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SchemaCommand extends Command {

    protected function configure() {

        parent::configure();
        $this->setName('schema');
        $this->setDescription('Write the schema to the database');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {

        $app = $this->getSilexApplication();

        // just $connection = $app['db']?
        $connection = $app['db']->getConnection();
        $grammar = new MySqlGrammar();
        foreach ($app['data']->loaded() as $namespace => $types) {
            foreach ($types as $type) {
                $dataType = $app['data']->get("{$namespace}/{$type}");
                $blueprint = $dataType->getBlueprint();
                $blueprint->create();
                $blueprint->build($connection, $grammar);
                $output->write(sprintf(
                    'Table %s created for data type %s',
                    $dataType->table,
                    $dataType->singularHumanName
                ), true);
            }
        }
    }

}