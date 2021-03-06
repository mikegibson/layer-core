<?php

namespace Sentient\Data;

use Doctrine\ORM\Tools\SchemaTool;
use Knp\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateSchemaCommand extends Command {

	protected function configure() {

		parent::configure();
		$this->setName('updateSchema');
		$this->setDescription('Write the schema to the database');
	}

	protected function execute(InputInterface $input, OutputInterface $output) {

		$app = $this->getSilexApplication();

		$names = $app['orm.rm']->getRepositoryList();

		$output->writeln(sprintf('Updating schema for entities: %s', implode(', ', $names)));

		$classes = [];
		foreach($names as $name) {
			$classes[] = $app['orm.rm']->getRepository($name)->getClassMetadata();
		}

		$tool = new SchemaTool($app['orm.em']);
		$tool->updateSchema($classes);

	}

}