<?php

namespace Layer\Users;

use Layer\Data\ManagedRepository;

class UserRepository extends ManagedRepository {

	public function getName() {
		return 'users:users';
	}

}