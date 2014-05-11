<?php

namespace Layer\Data;

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
	public $entityClass;

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

	public $titleField;

	/**
	 * @var array
	 * /
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
	 * /
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
	];*/

	/**
	 * Constructor
	 */
	public function __construct(Application $app) {

		$this->app = $app;
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

	}

	public function getEditableFields() {
		$reflection = $this->_getEntityReflection();
		$methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
		$fields = [];
		foreach($methods as $method) {
			if(preg_match('/^set([A-Z][A-Za-z]+)/', $method->name, $matches)) {
				$fields[] = lcfirst($matches[1]);
			}
		}
		return $fields;
	}

	protected function _getEntityReflection() {
		$metadata = $this->getMetadata();
		return $metadata->reflClass;
	}

    public function find($id, $lockMode = null, $lockVersion = null) {
		return $this->app['orm.em']->find($this->entityClass, $id, $lockMode, $lockVersion);
	}

	public function getMetadata() {
		return $this->app['orm.em']->getClassMetadata($this->entityClass);
	}

	public function createQueryBuilder() {
		$queryBuilder = $this->app['orm.em']->createQueryBuilder();
		$queryBuilder->select($this->name)
			->from($this->entityClass, $this->name);
		return $queryBuilder;
	}

	public function createEntity() {
		$metadata = $this->getMetadata();
		return $metadata->reflClass->newInstance();
	}

}