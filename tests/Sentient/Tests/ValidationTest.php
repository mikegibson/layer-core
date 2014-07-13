<?php

namespace Sentient\Tests;

use Sentient\Application;
use Sentient\Users\User;

class ValidationTest extends \PHPUnit_Framework_TestCase {

	public function testAnnotationValidation() {

		$app = new Application();

		$this->assertTrue($app->offsetExists('validator'));

		$validator = $app['validator'];

		$this->assertInstanceOf('Symfony\Component\Validator\ValidatorInterface', $validator);

		$validUser = new User();
		$validUser->setUsername('valid_username');
		$validUser->setEmail('validuser@validemail.com');
		$validUser->setPlainPassword('Password123.');
		$validUser->setBirthDate(new \DateTime('-20 YEARS'));

		$violations = $validator->validate($validUser);

		$this->assertEquals(0, $violations->count());

		$invalidUser = new User();
		$invalidUser->setEmail('INVALID_EMAIL_ADDRESS');

		$violations = $validator->validate($invalidUser);

		$this->assertGreaterThan(0, $violations->count());

	}

}