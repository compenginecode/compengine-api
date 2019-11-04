<?php

namespace DomainLayer\ORM\Source;

use DomainLayer\ORM\ApprovalStatus\ApprovalStatus;
use DomainLayer\ORM\DomainEntity\DomainEntity;
use DomainLayer\ORM\TimeSeries\PersistedTimeSeries\PersistedTimeSeries;
use InfrastructureLayer\Sluggify\HasSluggify;

/**
 * Class Source
 * @package DomainLayer\ORM\Source
 */
class Source extends DomainEntity{

    use HasSluggify;

    /** $name
     *
     *  The display name of the name.
     *
     * @var string
     */
    protected $name;

    /** $approvalStatus
     *
     *  The approval status of the name.
     *
     * @var ApprovalStatus
     */
    protected $approvalStatus;

    /** persistedTimeSeries
     *
     *
     *
     * @var PersistedTimeSeries
     */
    protected $persistedTimeSeries;

    /** __construct
     *
     *  Source constructor.
     *
     * @param $name
     */
    public function __construct($name){
        $this->name = $name;
        $this->approvalStatus = ApprovalStatus::unapproved();
    }

    /** getName
     *
     *  Returns the name of the tag.
     * 
     * @return string
     */
    public function getName(){
        return $this->name;
    }

    /** getApprovalStatus
     *
     *  Returns the approval status of the source.
     *
     * @return ApprovalStatus
     */
    public function getApprovalStatus() {
        return $this->approvalStatus;
    }

    /** Name
     *
     *  Sets the name
     *
     * @param string $name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /** ApprovalStatus
     *
     *  Sets the approval status
     *
     * @param ApprovalStatus $approvalStatus
     */
    public function setApprovalStatus($approvalStatus) {
        $this->approvalStatus = $approvalStatus;
    }

    /** preUpdate
     *
     *  Exposed to allow for pre-updating hooks.
     *
     */
    public function preUpdate(){
        $this->sluggify();
        return;
    }

    /** prePersist
     *
     *  Exposed to allow for pre-persist hooks.
     *
     */
    public function prePersist(){
        $this->sluggify();
        return;
    }

}