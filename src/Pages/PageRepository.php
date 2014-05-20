<?php

namespace Layer\Pages;

use Layer\Data\ManagedRepository;

class PageRepository extends ManagedRepository {

	public function getName() {
		return 'content:pages';
	}

}