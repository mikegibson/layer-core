<?php

namespace Layer\Users;

use Layer\Data\ManagedRepositoryInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserSecurityProvider implements UserProviderInterface {

	/**
	 * @var \Layer\Data\ManagedRepositoryInterface
	 */
	private $repository;

	/**
	 * @param ManagedRepositoryInterface $repository
	 */
	public function __construct(ManagedRepositoryInterface $repository) {
		$this->repository = $repository;
	}

	/**
	 * @param string $username
	 * @return UserInterface
	 * @throws \Symfony\Component\Security\Core\Exception\UsernameNotFoundException
	 */
	public function loadUserByUsername($username) {
		if($result = $this->getRepository()->findOneBy(compact('username'))) {
			return $result;
		}
		throw new UsernameNotFoundException(sprintf('User %s was not found.', $username));
	}

	/**
	 * @param UserInterface $user
	 * @return UserInterface
	 * @throws \Symfony\Component\Security\Core\Exception\UnsupportedUserException
	 */
	public function refreshUser(UserInterface $user) {
		if (!is_a($user, $this->getRepository()->getClassName())) {
			throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
		}

		return $this->loadUserByUsername($user->getUsername());
	}

	/**
	 * @param string $class
	 * @return bool
	 */
	public function supportsClass($class) {
		return $class === $this->getRepository()->getClassName();
	}

	/**
	 * @return ManagedRepositoryInterface
	 */
	protected function getRepository() {
		return $this->repository;
	}

}