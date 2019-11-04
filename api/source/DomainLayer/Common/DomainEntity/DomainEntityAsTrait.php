<?php

namespace DomainLayer\Common\DomainEntity;

/**
 * Class DomainEntityAsTrait
 * @package DomainLayer\Common\DomainEntity
 */
trait DomainEntityAsTrait {

	/** $id
	 *
	 *  Id of the domain entity.
	 *
	 * @var string
	 */
	protected $id;

	/** $timestampCreated
	 *
	 *  The timestamp the entity was created.
	 *
	 * @var \DateTime
	 */
	protected $timestampCreated;

	/** $timestampEdited
	 *
	 *  The timestamp the entity was edited.
	 *
	 * @var \DateTime
	 */
	protected $timestampUpdated;

	/** getId
	 *
	 *  Returns the entity Id.
	 *
	 * @return string
	 */
	public function getId(){
		return $this->id;
	}

	/** domainEntityPreUpdate
	 *
	 *  Doctrine calls this function before saving a changed dirty version.
	 *  Do NOT override this. Instead, override the preUpdate() function.
	 *
	 */
	public final function domainEntityPreUpdate(){
		$this->timestampUpdated = new \DateTime();
		$this->preUpdate();
	}

	/** domainEntityPrePersist
	 *
	 *  Doctrine calls this function before saving a new version.
	 *  Do NOT override this. Instead, override the prePersist() function.
	 *
	 */
	public final function domainEntityPrePersist(){
		$dateTime = new \DateTime();
		$this->timestampCreated = $dateTime;
		$this->timestampUpdated = $dateTime;
		$this->prePersist();
	}

	/** preUpdate
	 *
	 *  Exposed to allow for pre-updating hooks.
	 *
	 */
	public function preUpdate(){
		return ;
	}

	/** prePersist
	 *
	 *  Exposed to allow for pre-persist hooks.
	 *
	 */
	public function prePersist(){
		return ;
	}

	public function timestampUpdated(){
		return $this->timestampUpdated;
	}

	public function timestampCreated(){
		return $this->timestampCreated;
	}

	/**
	 * @param \DateTime $timestampUpdated
	 */
	public function setTimestampUpdated($timestampUpdated) {
		$this->timestampUpdated = $timestampUpdated;
	}

}