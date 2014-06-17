<?php

namespace Layer\Data;

use Doctrine\Common\Persistence\ObjectRepository;

interface ManagedRepositoryInterface extends ObjectRepository {

	/**
	 * @param $name
	 * @param array $options
	 * @return mixed
	 */
	public function queryMetadata($name, array $options = []);

	/**
	 * @return \Doctrine\ORM\Mapping\ClassMetadata
	 */
	public function getClassMetadata();

	/**
	 * @return string
	 */
	public function getName();

	/**
	 * @return object
	 */
	public function createEntity();

	/**
	 * @param null $alias
	 * @param null $indexBy
	 * @return \Doctrine\ORM\QueryBuilder
	 */
	public function createQueryBuilder($alias = null, $indexBy = null);

	/**
	 * @param $alias
	 * @return \Doctrine\ORM\ResultSetMappingBuilder
	 */
	public function createResultSetMappingBuilder($alias);

	/**
	 * @param $queryName
	 * @return \Doctrine\ORM\Query
	 */
	public function createNamedQuery($queryName);

	/**
	 * @param $queryName
	 * @return \Doctrine\ORM\NativeQuery
	 */
	public function createNativeNamedQuery($queryName);

	public function clear();

	/**
	 * @param mixed $id
	 * @param null $lockMode
	 * @param null $lockVersion
	 * @return object
	 */
	public function find($id, $lockMode = null, $lockVersion = null);

	/**
	 * @return array
	 */
	public function findAll();

	/**
	 * @param array $criteria
	 * @param array $orderBy
	 * @param null $limit
	 * @param null $offset
	 * @return array
	 */
	public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

	/**
	 * @param array $criteria
	 * @param array $orderBy
	 * @return object
	 */
	public function findOneBy(array $criteria, array $orderBy = null);

	public function save($entity);

}