<?php

namespace Layer\Data;

use Illuminate\Database\Capsule\Manager as Capsule;
use Layer\Application;

/**
 * Class DataTypeRegistry
 * Holds all loaded data types for the app
 *
 * @package Layer\DataScaffold\DataType
 */
class DataTypeRegistry {

    /**
     * @var \Layer\Application
     */
    protected $app;

    /**
     * Array of DataType instances
     *
     * @var array
     */
    protected $_loaded = [];

    /**
     * @param Capsule $capsule
     */
    public function __construct(Application $app) {
        $this->app = $app;
    }

    /**
     * Load a data type
     *
     * @param DataType|string|array $class
     * @param array $options
     * @return DataType
     * @throws MissingDataTypeException
     */
    public function load(DataType $dataType) {

        $this->_loaded[$dataType->namespace][$dataType->slug] = $dataType;

    }

    /**
     * Get all namespaces of loaded data types
     *
     * @return array
     */
    public function namespaces() {

        return array_keys($this->_loaded);
    }

    /**
     * Get an array of loaded namespaces and data types, or check if a datatype is loaded
     *
     * @param string|null $namespace
     * @param string|null $slug
     * @return array|bool
     */
    public function loaded($namespace = null, $slug = null) {

        if ($namespace === null) {
            $namespaces = $this->namespaces();

            return array_combine($namespaces, array_map([$this, 'loaded'], $namespaces));
        }
        $path = self::_resolvePath($namespace, $slug);
        if ($path === false) {
            if ($slug === null) {
                return isset($this->_loaded[$namespace]) ?
                    array_keys($this->_loaded[$namespace]) : [];
            }

            return false;
        }

        return true;
    }

    /**
     * Get a loaded data type
     *
     * @param string $namespace
     * @param string $slug
     * @return DataType|bool
     */
    public function get($namespace, $slug = null) {

        $path = $this->_resolvePath($namespace, $slug);
        if ($path === false) {
            return false;
        }
        list($namespace, $slug) = $path;

        return $this->_loaded[$namespace][$slug];
    }

    public function getConnection($connection = null) {

        return $this->app['db']->getConnection($connection);
    }

    /**
     * Try to resolve a namespace and slug to a data type, allowing for namespace/slug style
     *
     * @param $namespace
     * @param $slug
     * @return array|bool
     */
    protected function _resolvePath($namespace, $slug) {

        if ($slug === null) {
            if (preg_match('/^([a-z_\-]+)\/([a-z_\-]+)$/', $namespace, $matches)) {
                list(, $namespace, $slug) = $matches;
            } else {
                return false;
            }
        }
        if (!isset($this->_loaded[$namespace]) || !isset($this->_loaded[$namespace][$slug])) {
            return false;
        }

        return [$namespace, $slug];
    }

}