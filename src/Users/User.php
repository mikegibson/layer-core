<?php

namespace Sentient\Users;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Behavior;
use Sentient\Cms\Data\Metadata\Annotation as Cms;
use Sentient\Data\Metadata\Annotation as Sentient;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Sentient\Users\User
 *
 * @ORM\Entity
 * @ORM\Table(name="users")
 * @Sentient\CrudEntity
 * @Sentient\EntityName("users")
 */
class User implements UserInterface {

	/**
	 * @var int $id
	 *
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 */
	protected $id;

	/**
	 * @var string $username
	 *
	 * @ORM\Column(type="string")
	 * @Assert\NotBlank
	 * @Sentient\TitleProperty
	 */
	protected $username;

	/**
	 * @var string $email
	 *
	 * @ORM\Column(type="string")
	 * @Assert\NotBlank
	 * @Assert\Email
	 */
	protected $email;

	/**
	 * @var string $plainPassword
	 *
	 * @Cms\FormFieldProperty("password")
	 * @Sentient\CrudProperty(editable=true,visible=false)
	 * @Sentient\PropertyLabel("Password")
	 */
	protected $plainPassword;

	/**
	 * @var string $password
	 *
	 * @ORM\Column(type="string")
	 * @Sentient\CrudProperty(editable=false,visible=false)
	 */
	protected $password;

	/**
	 * @var \DateTime $birthDate
	 *
	 * @ORM\Column(type="date", nullable=true)
	 */
	protected $birthDate;

	/**
	 * @var string $salt
	 *
	 * @ORM\Column(type="string")
	 * @Sentient\CrudProperty(editable=false,visible=false)
	 */
	protected $salt;

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
		return $this->username;
	}

	public function getEmail() {
		return $this->email;
	}

	public function getPlainPassword() {
		return $this->plainPassword;
	}

	public function getPassword() {
		return $this->password;
	}

	public function getBirthDate() {
		return $this->birthDate;
	}

	public function getCreated() {
		return $this->created;
	}

	public function getUpdated() {
		return $this->updated;
	}

	public function getSalt() {
		return $this->salt;
	}

	public function getRoles() {
		return ['ROLE_ADMIN'];
	}

	public function setUsername($username) {
		$this->username = $username;
	}

	public function setEmail($email) {
		$this->email = $email;
	}

	public function setPlainPassword($plainPassword) {
		$this->plainPassword = $plainPassword;
		$this->password = null;
		$this->refreshSalt();
	}

	public function setPassword($password) {
		$this->password = $password;
		$this->plainPassword = null;
	}

	public function setBirthDate(\DateTime $birthDate) {
		$this->birthDate = $birthDate;
	}

	public function refreshSalt() {
		$this->salt = md5($this->__toString() . time());
	}

	public function eraseCredentials() {
		$this->plainPassword = null;
	}

	public function __toString() {
		return $this->getUsername();
	}

}