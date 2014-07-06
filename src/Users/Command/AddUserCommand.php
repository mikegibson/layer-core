<?php

namespace Sentient\Users\Command;

use Knp\Command\Command;
use Sentient\Users\User;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddUserCommand extends Command {

	protected function configure() {
		parent::configure();
		$this
			->setName('addUser')
			->setDescription('Add a user record')
			->addArgument(
				'username',
				InputArgument::REQUIRED,
				'Username?'
			)
			->addArgument(
				'email',
				InputArgument::REQUIRED,
				'Email?'
			)
			->addArgument(
				'password',
				InputArgument::REQUIRED,
				'Password?'
			)
		;
	}

	/**
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @return int|null|void
	 * @todo Validation
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {

		$app = $this->getSilexApplication();

		$user = new User();

		$username = $input->getArgument('username');

		$user->setUsername($username);
		$user->setEmail($input->getArgument('email'));
		$user->setPlainPassword($input->getArgument('password'));
		$entityManager = $app['orm.em'];
		$entityManager->persist($user);
		$entityManager->flush();

		$output->writeln(sprintf('Added new user %s', $username));

	}

}