<?php

namespace Install\DatabaseInstaller;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;

/**
 * Class DatabaseInstaller
 * @package Install\DatabaseInstaller
 */
class DatabaseInstaller {

	/** $schemaTool
	 *
	 * 	Tool used to create and drop schemas.
	 *
	 * @var SchemaTool
	 */
	private $schemaTool;

	/** $entityManager
	 *
	 * 	Interface used to obtain transaction control.
	 *
	 * @var EntityManager
	 */
	private $entityManager;

	/** getDoctrineManagedClasses
	 *
	 * 	Returns metadata for all classes managed by Doctrine.
	 *
	 * @return array
	 */
	private function getDoctrineManagedClasses(){
		return $this->entityManager->getMetadataFactory()->getAllMetadata();
	}

	/** __construct
	 *
	 * 	DatabaseInstaller constructor.
	 *
	 * @param EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager){
		$this->schemaTool = new SchemaTool($entityManager);
		$this->entityManager = $entityManager;
	}

	/** installDatabase
	 *
	 * 	Removes the existing schema and installs the new one.
	 *
	 * @param callable $reporter
	 * @throws \Doctrine\ORM\Tools\ToolsException
	 */
	public function installDatabase(callable $reporter){
		$reporter("Dropping existing tables.");
		$this->schemaTool->dropDatabase();
		$this->entityManager->flush();

		$reporter("Creating new tables as per XML definitions.");
		$this->schemaTool->createSchema($this->getDoctrineManagedClasses());
		$this->entityManager->flush();
	}

}