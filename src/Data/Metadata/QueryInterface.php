<?php

namespace Sentient\Data\Metadata;

use Doctrine\ORM\Mapping\ClassMetadata;

interface QueryInterface {

	public function getName();

	public function getResult(ClassMetadata $classMetadata, array $options = []);

}