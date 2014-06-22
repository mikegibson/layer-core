<?php

namespace Layer\Form;

use Doctrine\Common\Annotations\Reader;
use Symfony\Component\Form\FormTypeGuesserInterface;
use Symfony\Component\Form\Guess\Guess;
use Symfony\Component\Form\Guess\TypeGuess;

class HtmlTypeGuesser implements FormTypeGuesserInterface {

	private $reader;

	protected $annotationClass = 'Layer\\Data\\Metadata\\Annotation\\HtmlProperty';

	public function __construct(Reader $reader) {
		$this->reader = $reader;
	}

	public function guessType($class, $property) {
		$reflClass = new \ReflectionClass($class);
		$reflProperty = $reflClass->getProperty($property);
		if($annotation = $this->reader->getPropertyAnnotation($reflProperty, $this->annotationClass)) {
			return new TypeGuess('html', [], Guess::HIGH_CONFIDENCE);
		}
	}

	public function guessRequired($class, $property) {

	}

	public function guessMaxLength($class, $property) {

	}

	public function guessPattern($class, $property) {

	}

}