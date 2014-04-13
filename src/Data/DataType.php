<?php

namespace Layer\Data;

use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Processors\Processor;
use Layer\Data\Field\Field;
use Silex\Application;

/**
 * Class DataType
 *
 * @package Layer\DataScaffold\DataType
 */
abstract class DataType {

	/**
	 * @var \Silex\Application
	 */
	protected $app;

	/**
	 * @var string
	 */
	public $namespace = 'content';

	/**
	 * @var string
	 */
	public $name;

	/**
	 * @var string
	 */
	public $slug;

	/**
	 * @var string
	 */
	public $table;

	/**
	 * @var string
	 */
	public $modelClass;

	/**
	 * @var string
	 */
	public $singularHumanName;

	/**
	 * @var string
	 */
	public $pluralHumanName;

	/**
	 * Variable name for individual records
	 *
	 * @var string
	 */
	public $singularVar;

	/**
	 * Variable name for multiple records
	 *
	 * @var string
	 */
	public $pluralVar;

	/**
	 * @var array
	 */
	protected $_fields = [];

	/**
	 * @var array
	 */
	protected $_fieldNameMap = [
		'id' => 'PrimaryKey',
		'uuid' => 'UUID',
		'title' => 'Title',
		'slug' => 'Slug',
		'content' => 'Html',
		'email' => 'Email',
		'password' => 'Password',
		//    'created' => 'Datetime',
		//    'modified' => 'Datetime',
	];

	/**
	 * @var array
	 */
	protected $_fieldTypeMap = [
		'integer' => 'Integer',
		'biginteger' => 'BigInteger',
		'boolean' => 'Boolean',
		'binary' => 'Binary',
		'float' => 'Float',
		'decimal' => 'Decimal',
		'text' => 'Text',
		'string' => 'String',
		'date' => 'Date',
		'time' => 'Time',
		'datetime' => 'Datetime',
		'timestamp' => 'Timestamp',
		'uuid' => 'UUID',
	];

	/**
	 * Constructor
	 */
	public function __construct(Application $app) {

		$this->app = $app;
		$this->registry = $app['data'];
		if ($this->name === null) {
			$class = get_class($this);
			if (($pos = strrpos($class, '\\')) !== false) {
				$class = substr($class, $pos + 1);
			}
			$this->name = preg_replace('/(Type)$/', '', $class);
		}
		if ($this->slug === null) {
			$this->slug = strtolower($this->app['inflector']->slug($this->app['inflector']->pluralize($this->name), '-'));
		}
		if ($this->table === null) {
			$table = str_replace('-', '_', $this->slug);
			if ($this->namespace !== $this->slug) {
				$table = str_replace('-', '_', $this->namespace) . '_' . $table;
			}
			$this->table = $table;
		}
		if ($this->modelClass === null) {
			$this->modelClass = $this->app['inflector']->pluralize($this->name);
			if ($this->plugin) {
				$this->modelClass = $this->plugin . '.' . $this->modelClass;
			}
		}
		if ($this->pluralVar === null) {
			$this->pluralVar = $this->app['inflector']->variable($this->name);
		}
		if ($this->singularVar === null) {
			$this->singularVar = $this->app['inflector']->singularize($this->pluralVar);
		}
		if ($this->pluralHumanName === null) {
			$this->pluralHumanName = $this->singularHumanName ?
				$this->app['inflector']->pluralize($this->singularHumanName) :
				$this->app['inflector']->humanize($this->slug);
		}
		if ($this->singularHumanName === null) {
			$this->singularHumanName = $this->app['inflector']->singularize($this->pluralHumanName);
		}

		$fields = $this->_fields;

		$this->_fields = [];

		foreach ($fields as $field => $attrs) {
			if (is_int($field)) {
				$field = $attrs;
				$attrs = [];
			}
			$this->addField($field, $attrs);
		}

	}

	/**
	 * @param $name
	 * @param array $config
	 * @return bool|mixed
	 */
	public function addField($name, $config = []) {

		if (is_string($config)) {
			$config = ['type' => $config];
		}
		if (!isset($config['className'])) {
			if (isset($config['type']) && isset($this->_fieldTypeMap[$config['type']])) {
				$class = $this->_fieldTypeMap[$config['type']];
			} elseif (isset($this->_fieldNameMap[$name])) {
				$class = $this->_fieldNameMap[$name];
			} else {
				$class = $this->_fieldTypeMap[substr($name, -3) === '_id' ? 'integer' : 'string'];
			}
			$config['className'] = '\\Layer\\Data\\Field\\' . $class . 'Field';
		}
		$className = $config['className'];
		if (!class_exists($className)) {
			throw new \Exception(sprintf('Class not found: %s', $className));
		}
		$field = new $className($this->app, $this, $name, $config);

		return $this->_addField($field);
	}

	/**
	 * @param Field $field
	 * @return Field
	 */
	protected function _addField(Field $field) {

		return $this->_fields[$field->name] = $field;
	}

	/**
	 * @param $name
	 * @return bool
	 */
	public function hasField($name) {

		return isset($this->_fields[$name]);
	}

	/**
	 * @return array
	 */
	public function fields() {

		return $this->_fields;
	}

	/**
	 * @param $name
	 * @return Field|bool
	 */
	public function field($name) {

		return $this->hasField($name) ? $this->_fields[$name] : false;
	}

	/**
	 * @param null $name
	 * @return array|bool
	 */
	public function schema($name = null) {

		if ($name !== null) {
			if ($this->hasField($name)) {
				return $this->_fields[$name]->params();
			}

			return false;
		}
		$fields = array();
		foreach ($this->_fields as $field) {
			$fields[$field->name] = $field->params();
		}

		return $fields;
	}

	/**
	 * @param array $attributes
	 * @return Model
	 */
	public function model(array $attributes = []) {
		return new Model($this->app, $this, $attributes);
	}

	public function query($connection = null, Processor $processor = null) {

		$connection = $this->getConnection($connection);

		if ($processor === null) {
			$processor = $connection->getPostProcessor();
		}

		$query = new Builder($connection, $connection->getQueryGrammar(), $processor);

		return $query->from($this->table);
	}

	public function getBlueprint() {
		$blueprint = new Blueprint($this->table);
		foreach ($this->_fields as $field) {
			$field->prepareBlueprint($blueprint);
		}
		return $blueprint;
	}

	public function getConnection($connection = null) {
		return $this->app['data']->getConnection($connection);
	}

}