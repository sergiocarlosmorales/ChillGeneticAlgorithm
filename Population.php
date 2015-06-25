<?php
namespace ChillGeneticAlgorithm;

class Population
{
    /**
     * Array keyed by the citizen's unique identifier.
     * @var Citizen[]|array
     */
    protected $population = [];

    public function __construct(array $citizens = [])
    {
        foreach ($citizens as $citizen) {
            /* @var $citizen Citizen */
            $this->addCitizen($citizen);
        }
    }

    /**
     * Get all Citizen's unique identifiers
     * @return string|array
     */
    public function getAllCitizenUniqueIdentifiers()
    {
        return array_keys($this->population);
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return count($this->population);
    }

    /**
     * @param string $identifier
     * @return Citizen
     */
    public function getCitizenByUniqueIdentifier($identifier)
    {
        return $this->population[$identifier];
    }

    /**
     * @param string $identifier
     */
    public function removeCitizenByUniqueIdentifier($identifier)
    {
        unset($this->population[$identifier]);
    }

    public function addCitizen(Citizen $citizen)
    {
        $this->population[$citizen->getUniqueIdentifier()] = $citizen;
    }
}