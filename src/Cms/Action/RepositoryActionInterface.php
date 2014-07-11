<?php

namespace Sentient\Cms\Action;

use Sentient\Data\ManagedRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;

interface RepositoryActionInterface {

	/**
	 * @return string
	 */
	public function getName();

	/**
	 * @return bool
	 */
	public function isEntityRequired();

	/**
	 * @param ManagedRepositoryInterface $repository
	 * @return bool
	 */
	public function isRepositoryEligible(ManagedRepositoryInterface $repository);

	/**
	 * @param ManagedRepositoryInterface $repository
	 * @return string
	 */
	public function getLabel(ManagedRepositoryInterface $repository);

	/**
	 * @param ManagedRepositoryInterface $repository
	 * @return string
	 */
	public function getTemplate(ManagedRepositoryInterface $repository);

	/**
	 * @param ManagedRepositoryInterface $repository
	 * @param Request $request
	 * @return mixed
	 */
	public function invoke(ManagedRepositoryInterface $repository, Request $request);

}