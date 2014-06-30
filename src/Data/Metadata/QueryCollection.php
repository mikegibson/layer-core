<?php

namespace Sentient\Data\Metadata;

class QueryCollection {

	private $queries = [];

	/**
	 * @param QueryInterface $query
	 * @return $this
	 * @throws \InvalidArgumentException
	 */
	public function registerQuery(QueryInterface $query) {
		$name = $query->getName();
		if($this->hasQuery($name)) {
			throw new \InvalidArgumentException(sprintf('Query "%s" is already registered!', $name));
		}
		$this->queries[$name] = $query;
		return $this;
	}

	/**
	 * @param $name
	 * @return bool
	 */
	public function hasQuery($name) {
		return isset($this->queries[$name]);
	}

	/**
	 * @param $name
	 * @return QueryInterface $query
	 * @throws \InvalidArgumentException
	 */
	public function getQuery($name) {
		if(!$this->hasQuery($name)) {
			throw new \InvalidArgumentException(sprintf('Query "%s" is not registered!', $name));
		}
		return $this->queries[$name];
	}

	/**
	 * @return array
	 */
	public function getRegisteredQueryNames() {
		return array_keys($this->queries);
	}

}