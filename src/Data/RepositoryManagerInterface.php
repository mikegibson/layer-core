<?php

namespace Layer\Data;

interface RepositoryManagerInterface {

	/**
	 * Gets the repository class for the given name
	 *
	 * @param $name
	 * @return ManagedRepositoryInterface
	 * @throws \InvalidArgumentException if repository not found
	 */
	public function getRepository($name);

	/**
	 * Get an array of registered repositories
	 *
	 * @return array
	 */
	public function getRepositoryList();

	/**
	 * Check if a repository is registered
	 *
	 * @param string $name
	 * @return bool
	 */
	public function hasRepository($name);

}