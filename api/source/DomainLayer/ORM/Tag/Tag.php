<?php

namespace DomainLayer\ORM\Tag;

use Doctrine\Common\Collections\Collection;
use DomainLayer\ORM\ApprovalStatus\ApprovalStatus;
use DomainLayer\ORM\DomainEntity\DomainEntity;
use InfrastructureLayer\Sluggify\HasSluggify;

/**
 * Class Tag
 * @package DomainLayer\ORM\Tag
 */
class Tag extends DomainEntity{

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
     * @var Collection
     */
    protected $persistedTimeSeries;

    /** __construct
     *
     *  Tag constructor.
     *
     * @param $name
     */
    public function __construct($name, $approvalStatus = null){
        $this->name = $name;
        $this->approvalStatus = $approvalStatus ?: ApprovalStatus::unapproved();
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
     *  Returns the approval status of the tag.
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