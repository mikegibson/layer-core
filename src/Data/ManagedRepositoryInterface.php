<?php

namespace Layer\Data;

use Doctrine\Common\Persistence\ObjectRepository;

interface ManagedRepositoryInterface extends ObjectRepository {

	public function getEntityMetadata();

	public function getName();

	public function getNamespace();

	public function getBasename();

	public function getSingularHumanName();

	public function getPluralHumanName();

	public function getEditableFields();

	public function createEntity();

	public function createQueryBuilder($alias = null, $indexBy = null);

	public function createResultSetMappingBuilder($alias);

	public function createNamedQuery($queryName);

	public function createNativeNamedQuery($queryName);

	public function clear();

	public function find($id, $lockMode = null, $lockVersion = null);

	public function findAll();

	public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

	public function findOneBy(array $criteria, array $orderBy = null);

}