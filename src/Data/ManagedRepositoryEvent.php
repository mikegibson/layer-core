<?php

namespace Layer\Data;

use Symfony\Component\EventDispatcher\Event;

class ManagedRepositoryEvent extends Event {

	const REGISTER = 'orm.rm.register';

	private $repository;

	public function __construct(ManagedRepositoryInterface $repository) {
		$this->repository = $repository;
	}

	public function getRepository() {
		return $this->repository;
	}

	public function setRepository(ManagedRepositoryInterface $repository) {
		$this->repository = $repository;
	}

}