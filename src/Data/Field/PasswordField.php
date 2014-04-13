<?php

namespace Layer\Data\Field;

	//use Cake\Event\Event;
	//use Cake\Utility\Security;
	//use Layer\DataScaffold\ORM\Entity;

/**
 * Class PasswordField
 *
 * @package Layer\Users\DataType\Field
 */
class PasswordField extends StringField {

	/**
	 * @var bool
	 */
	public $null = false;

	/**
	 * Hashing algorithm to use
	 *
	 * @var string
	 */
	public $hashType = 'blowfish';

	/**
	 * Salt for password hashing
	 *
	 * @var bool
	 */
	public $salt = false;

	/**
	 * @var bool
	 */
	public $allowEmpty = false;

	/**
	 * @param $value
	 * @param Entity $entity
	 * @return string
	 * /
	 * public function beforeSave(Event $event, Entity $entity, $value) {
	 *
	 * $value = parent::beforeSave($event, $entity, $value);
	 *
	 * return Security::hash($value, $this->hashType, $this->salt);
	 * }*/

}