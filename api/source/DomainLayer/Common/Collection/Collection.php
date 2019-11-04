<?php

namespace DomainLayer\Common\Collection;

use Doctrine\Common\Collections\AbstractLazyCollection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;

/**
 * Class Collection
 * @package DomainLayer\Common\Collection
 */
class Collection implements \IteratorAggregate{

    /** $owner
     *
     *  Entity responsible for managing the collection.
     *
     * @var object
     */
    protected $owner;

    /** $arrayCollection
     *
     *  Doctrine array collection.
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $arrayCollection;

    /** __construct
     *
     *  Constructor. $owner is the entity who is responsible for the collection.
     *
     * @param $arrayCollection
     * @param $owner
     */
    public function __construct($arrayCollection, $owner){
        $this->arrayCollection = $arrayCollection;
        $this->owner = $owner;
    }


    /** count
     *
     *  Returns the collection count
     *
     * @return int
     */
    public function count(){
        return $this->arrayCollection->count();
    }

    /** clear
     *
     *  Clears the collection. If orphan-removal is on, then
     *  this is remove all the entities from the persistence store
     *  when the transaction is flushed.
     *
     */
    public function clear(){
        $this->arrayCollection->clear();
    }

    /** getIterator
     *
     *  Returns an Iterator traversable object.
     *
     * @return \ArrayIterator|\Traversable
     * @inherits Inherited from IteratorAggregate interface
     */
    public function getIterator(){
        return new \ArrayIterator($this->arrayCollection->toArray());
    }

    /** contains
     *
     *  Returns TRUE if the value is contained in the collection, FALSE otherwise.
     *
     * @param $object
     * @return bool
     */
    public function contains($object){
        return $this->arrayCollection->contains($object);
    }

    public function remove($entity){
        return $this->arrayCollection->removeElement($entity);
    }

    /**
     * @return array
     */
    public function toArray(){
        return $this->arrayCollection->toArray();
    }

} 