<?php

namespace Sentient\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class HtmlType extends AbstractType {

	private $purifier;

	public function __construct(HtmlPurifierInterface $purifier) {
		$this->purifier = $purifier;
	}

	public function getName() {
		return 'html';
	}

	public function getParent() {
		return 'text';
	}

	public function buildForm(FormBuilderInterface $builder, array $options) {
		if($options['purify']) {
			$purifier = $this->purifier;
			$builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use($purifier) {
				$html = $event->getData();
				if(!empty($html)) {
					$event->setData($purifier->purify($html));
				}
			});
		}
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(['purify' => true]);
	}

}