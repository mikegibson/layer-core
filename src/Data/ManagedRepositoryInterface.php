<?php

namespace Layer\Data;

use Doctrine\Common\Persistence\ObjectRepository;

interface ManagedRepositoryInterface extends ObjectRepository {

	public function queryMetadata($name, array $options = []);

	public function getClassMetadata();

	public function getName();

	public function getEntityManager();

	public function createEntity();

	public function createQueryBuilder($alias = null, $indexBy = null);

	public function createResultSetMappingBuilder($alias);

	public function createNamedQuery($queryName);

	public function createNativeNamedQuery($queryName);

	//public function createFormType(MetadataDriverInterface $driver = null);

	public function clear();

	public function find($id, $lockMode = null, $lockVersion = null);

	public function findAll();

	public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

	public function findOneBy(array $criteria, array $orderBy = null);

}