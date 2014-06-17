<?php

namespace Layer\Users;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class UserListener implements EventSubscriber {

	/**
	 * @var \Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface
	 */
	protected $encoderFactory;

	/**
	 * @param EncoderFactoryInterface $encoderFactory
	 */
	public function __construct(EncoderFactoryInterface $encoderFactory) {
		$this->encoderFactory = $encoderFactory;
	}

	/**
	 * Specifies the list of events to listen
	 *
	 * @return array
	 */
	public function getSubscribedEvents() {
		return [
			'preUpdate',
			'prePersist'
		];
	}

	/**
	 * @param User $user
	 * @return \Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface
	 */
	public function getEncoder(User $user) {
		return $this->encoderFactory->getEncoder($user);
	}

	/**
	 * @param PreUpdateEventArgs $event
	 */
	public function preUpdate(PreUpdateEventArgs $event) {
		$user = $event->getEntity();

		if (!$user instanceof User) {
			return;
		}

		$this->updateUser($user);
		$event->setNewValue('password', $user->getPassword());
	}

	/**
	 * @param LifecycleEventArgs $event
	 */
	public function prePersist(LifecycleEventArgs $event) {
		$user = $event->getEntity();

		if (!$user instanceof User) {
			return;
		}

		$this->updateUser($user);
	}

	/**
	 * @param User $user
	 */
	protected function updateUser(User $user) {
		if(!$user->getSalt()) {
			$user->refreshSalt();
		}
		$plainPassword = $user->getPlainPassword();
		if (!empty($plainPassword)) {
			$encoder = $this->getEncoder($user);
			$user->setPassword($encoder->encodePassword($plainPassword, $user->getSalt()));
			$user->eraseCredentials();
		}
	}

	/**
	 * {@inheritDoc}
	 */
	protected function getNamespace() {
		return __NAMESPACE__;
	}

}