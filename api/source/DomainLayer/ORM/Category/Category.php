<?php

namespace DomainLayer\ORM\Category;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use DomainLayer\ORM\ApprovalStatus\ApprovalStatus;
use DomainLayer\ORM\DomainEntity\DomainEntity;
use DomainLayer\ORM\TopLevelCategory\TopLevelCategory;
use InfrastructureLayer\Sluggify\HasSluggify;

/**
 * Class Category
 * @package DomainLayer\ORM\Category
 */
class Category extends DomainEntity{

    use HasSluggify;

    /** $name
     *
     *  The display name of the category.
     *
     * @var string
     */
    protected $name;

    /** $approvalStatus
     *
     *  The approval status of the category.
     *
     * @var ApprovalStatus
     */
    protected $approvalStatus;

    /** $parent
     *
     *  The parent category, or NULL if there is none.
     *
     * @var Category|NULL
     */
    protected $parent;

    /** $children
     *
     *  Collection of the child categories of this category.
     *
     * @var Collection
     */
    protected $children;

    /** persistedTimeSeries
     *
     *
     *
     * @var Collection
     */
    protected $persistedTimeSeries;

    /**
     * Category constructor.
     *
     * @param $name
     * @param $parent
     */
    public function __construct($name, $parent){
        $this->name = $name;
        $this->parent = $parent;
        $this->approvalStatus = ApprovalStatus::unapproved();
        $this->children = new ArrayCollection();
    }

    /** hasParent
     *
     *  Returns TRUE if this category has a parent, and FALSE otherwise.
     *
     * @return bool
     */
    public function hasParent(){
        return NULL !== $this->parent;
    }

    /** getParentCategory
     *
     *  Returns the parent category, and NULL if the category has no parent.
     *
     * @return Category|NULL
     */
    public function getParentCategory(){
        return $this->parent;
    }

    /** setParentCategory
     *
     *  Sets the parent category.
     *
     * @param Category|NULL $parent
     */
    public function setParentCategory($parent){
        $this->parent = $parent;
    }

    /** getName
     *
     *  Returns the name of the category.
     * 
     * @return string
     */
    public function getName(){
        return $this->name;
    }

    /** getApprovalStatus
     *
     *  Returns the approval status of the category.
     *
     * @return ApprovalStatus
     */
    public function getApprovalStatus() {
        return $this->approvalStatus;
    }

    /** getChildren
     *
     *  Returns a Collection of all the children of this category.
     *
     * @return Collection
     */
    public function getChildren(){
        return $this->children;
    }

    /** addChild
     *
     *  Creates a new child category and returns it.
     *
     * @param $name
     * @return Category
     */
    public function addChild($name){
        return new Category($name, $this);
    }

    /**
     * @return Category
     */
    public function getTopLevelCategory(){
        $recurse = function(Category $category) use (&$recurse){
            if ($category->hasParent()){
                return $recurse($category->getParentCategory());
            }else{
                return $category;
            }
        };

        return $recurse($this);
    }

    /**
     * @return TopLevelCategory
     */
    public function asTopLevelCategory(){
        switch(strtolower($this->name)){
            case "real":
                return TopLevelCategory::real();
            case "synthetic":
                return TopLevelCategory::synthetic();
            default:
                return TopLevelCategory::unknown();
        }
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
     *  Sets the approvalStatus
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