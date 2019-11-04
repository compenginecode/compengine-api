<?php

namespace DomainLayer\LocalitySensitiveHashing\LSH;
use DomainLayer\LocalitySensitiveHashing\Algebra\Vector;
use DomainLayer\LocalitySensitiveHashing\Family\CosineFamily;

/**
 * Class LSHProcessor
 * @package DomainLayer\LocalitySensitiveHashing\LSH
 */
class LSHProcessor {

    private $vectors = [];

    private $dimension;

    private $families = [];

    private $indices = [];

    public static function createVectorArray($documentPath){
        $documents = explode(PHP_EOL, file_get_contents($documentPath));
        $inputVectors = [];
        foreach($documents as $aDocument){
            $inputVectors[] = Vector::fromArray(explode(",", $aDocument));
        }

        return $inputVectors;
    }

    public function getVector($index){
        return $this->vectors[$index];
    }

    public function setVectors(array $arrayOfVectors){
        $this->vectors = $arrayOfVectors;
        $this->dimension = $arrayOfVectors[0]->dimension();
    }

    public function realTopN(Vector $candidateVector, $numberN){
        return $this->getTopN($candidateVector, $this->vectors, $numberN);
    }

    public function approximateTopN(Vector $candidateVector, $numberN){
        $nearestNeighbours = [];
        foreach($this->families as $aFamily){
            /** @var $familyObj CosineFamily */
            $familyObj = $aFamily["Family"];
            $myHash = $familyObj->hash($candidateVector);

            $index = $this->indices[$aFamily["IndexNumber"]];
            if (isset($index[$myHash])){
                $nearestNeighbours = array_merge($nearestNeighbours, $index[$myHash]);
            }
        }

        return $this->getTopN($candidateVector, array_unique($nearestNeighbours), $numberN);
    }

    public function totalThatQualifyAsApproximate(Vector $candidateVector){
        $nearestNeighbours = [];
        foreach($this->families as $aFamily){
            /** @var $familyObj CosineFamily */
            $familyObj = $aFamily["Family"];
            $myHash = $familyObj->hash($candidateVector);

            $index = $this->indices[$aFamily["IndexNumber"]];
            if (isset($index[$myHash])){
                $nearestNeighbours = array_merge($nearestNeighbours, $index[$myHash]);
            }
        }

        return count(array_unique($nearestNeighbours));
    }

    public function clearIndices(){
        $this->indices = [];
        $this->families = [];
    }

    public function createIndices($numberOfIndices, $numberOfHashes, callable $percentageDone = NULL){
        /** Create all the indices */
        for($i = 0; $i < $numberOfIndices; $i++){
            $newFamily = new CosineFamily($numberOfHashes, $this->dimension);
            $this->families[] = array(
                "Family" => $newFamily,
                "IndexNumber" => $i
            );

            if (!isset($this->indices[$i])){
                $this->indices[$i] = [];
            }

            foreach($this->vectors as $aVector){
                $hash = $newFamily->hash($aVector);
                if (!isset($this->indices[$i][$hash])){
                    $this->indices[$i][$hash] = [];
                }

                $this->indices[$i][$hash][] = $aVector;
            }

            if (NULL !== $percentageDone){
                $percentageDone($i, $numberOfIndices);
            }
        }
    }

    public function indexHashes($indexNumber){
        $results = [];
        $k = 0;
        foreach($this->indices[$indexNumber] as $aHash => $keys){
            $results[$aHash] = count($keys);
            $k += count($keys);
        }

        return $results;
    }

} 