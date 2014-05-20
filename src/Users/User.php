<?php

namespace Layer\Users;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Behavior;
use Layer\Data\Metadata\Annotation as Meta;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Layer\Users\User
 *
 * @ORM\Entity
 * @ORM\Table(name="users")
 * @Meta\CrudEntity
 */
class User {

	/**
	 * @var int $id
	 *
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 */
	protected $id;

	/**
	 * @var string $email
	 *
	 * @ORM\Column(type="string")
	 * @Assert\NotBlank
	 * @Assert\Email
	 */
	protected $email;

	/**
	 * @var string $password
	 *
	 * @ORM\Column(type="string")
	 */
	protected $password;

	/**
	 * @ORM\Column(type="datetime")
	 * @Behavior\Timestampable(on="create")
	 */
	protected $created;

	/**
	 * @ORM\Column(type="datetime")
	 * @Behavior\Timestampable(on="update")
	 */
	protected $updated;

	public function getId() {
		return $this->id;
	}

	public function getUsername() {
		return $this->getEmail();
	}

	public function getEmail() {
		return $this->email;
	}

	public function getPassword() {
		return $this->password;
	}

	public function setEmail($email) {
		$this->email = $email;
	}

	public function setPassword($password) {
		$this->password = $password;
	}

}