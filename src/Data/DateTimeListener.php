<?php

namespace Layer\Data;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;

class DateTimeListener implements EventSubscriber {

	/**
	 * Specifies the list of events to listen
	 *
	 * @return array
	 */
	public function getSubscribedEvents() {
		return [
			'postLoad'
		];
	}

	public function postLoad(LifecycleEventArgs $event) {
		$entity = $event->getEntity();
		$reflClass = new \ReflectionClass($entity);
		foreach($reflClass->getProperties() as $property) {
			$property->setAccessible(true);
			$value = $property->getValue($entity);
			if($value instanceof \DateTime && !method_exists($value, '__toString')) {
				$dateTime = new DateTime(null, $value->getTimezone());
				$dateTime->setTimestamp($value->getTimestamp());
				$property->setValue($entity, $dateTime);
			}
		}
	}

}