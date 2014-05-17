<?php

namespace Layer\Pages;

use Layer\Data\EntityRepository;

class PageRepository extends EntityRepository {

	public function getNamespace() {
		return 'content';
	}

}