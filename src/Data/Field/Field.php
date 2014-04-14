<?php

namespace Layer\Data\Field;

use Layer\Data\Blueprint;
use Layer\Data\DataType;
use Layer\Utility\SetPropertiesTrait;
use Silex\Application;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class Field
 *
 * @package Layer\DataScaffold\DataType\Field
 */
abstract class Field {

	use SetPropertiesTrait;

	/**
	 * @var \Silex\Application
	 */
	protected $app;

	/**
	 * @var DataType
	 */
	protected $DataType;

	/**
	 * @var array
	 */
	protected $_paramKeys = [
		'type',
		'length',
		'precision',
		'default',
		'null',
		'fixed',
		'unsigned',
		'comment',
		'autoIncrement'
	];

	/**
	 * @var null
	 */
	public $name;

	/**
	 * @var string
	 */
	public $label;

	/**
	 * @var string
	 */
	public $type;

	/**
	 * @var int|null
	 */
	public $length;

	/**
	 * @var int|null
	 */
	public $precision;

	/**
	 * @var mixed
	 */
	public $default;

	/**
	 * @var bool
	 */
	public $allowEmpty = true;

	/**
	 * @var bool
	 */
	public $null = true;

	/**
	 * @var null|bool
	 */
	public $fixed;

	/**
	 * @var null|bool
	 */
	public $unsigned;

	/**
	 * @var null|string
	 */
	public $comment;

	/**
	 * @var null|bool
	 */
	public $autoIncrement;

	/**
	 * @var bool
	 */
	public $index = false;

	/**
	 * @var bool
	 */
	public $fulltext = false;

	/**
	 * @var bool
	 */
	public $unique = false;

	/**
	 * @var bool
	 */
	public $primaryKey = false;

	/**
	 * @var bool
	 */
	public $visible = true;

	/**
	 * @var bool
	 */
	public $editable = true;

	/**
	 * @var bool
	 */
	public $important = true;

	/**
	 * @var string
	 */
	public $inputType = 'text';

	/**
	 * @var bool
	 */
	public $titleField = false;

	/**
	 * @var bool
	 */
	public $htmlContent = false;

	/**
	 * @var bool
	 */
	public $htmlSafe = false;

	/**
	 * @param DataType $DataType
	 * @param null $name
	 * @param array $config
	 */
	public function __construct(Application $app, DataType $DataType, $name = null, array $config = []) {

		if (is_array($name)) {
			$config = $name;
			$name = null;
		}
		$this->_setProperties($config);
		$this->app = $app;
		$this->DataType = $DataType;
		if ($name !== null) {
			$this->name = $name;
		}
		if (empty($this->name)) {
			throw new \Exception('No field name was specified!');
		}
		if ($this->label === null) {
			$this->label = $this->app['inflector']->humanize($this->name);
		}
	}

	/**
	 * @param Blueprint $blueprint
	 */
	public function prepareBlueprint(Blueprint $blueprint) {
		$blueprint->add($this->type, $this->name, $this->params());
	}

	/**
	 * Get the field parameters for the schema
	 *
	 * @return array
	 */
	public function params() {

		$params = [];
		foreach ($this->_paramKeys as $key) {
			if ($this->{$key} !== null) {
				$params[$key] = $this->{$key};
			}
		}

		return $params;
	}

	/**
	 * @param FormBuilder $builder
	 * @param array $options
	 * @return FormBuilder
	 */
	public function addFormField(FormBuilder $builder, array $options = []) {
		$options = array_merge([
			'required' => !$this->null
		], $options);
		if(!isset($options['constraints'])) {
			$options['constraints'] = $this->getConstraints();
		}
		if(isset($options['type'])) {
			$type = $options['type'];
			unset($options['type']);
		} else {
			$type = $this->inputType;
		}
		$builder->add($this->name, $type, $options);
		return $builder;
	}

	/**
	 * @return array
	 */
	public function getConstraints() {
		$constraints = [];
		if(!$this->allowEmpty) {
			$constraints[] = new NotBlank();
		}
		return $constraints;
	}

}