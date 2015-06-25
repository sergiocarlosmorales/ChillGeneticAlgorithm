<?php
namespace ChillGeneticAlgorithm;

abstract class Citizen
{
    /**
     * This should return a NEW citizen with mixed traits from both partners
     * @param Citizen $partner
     * @return Citizen
     */
    abstract function mate(Citizen $partner);

    public function mutate($mutationProbability)
    {
        if ($mutationProbability == 0) {
            return;
        }
        $this->_mutate($mutationProbability);
    }

    /**
     * Mutate this Citizen
     * We pass the mutation probability just as a reference as it might help
     * to the logic in this method to determine 'how much' to mutate.
     * @param float $mutationProbability
     * @return Citizen
     */
    abstract function _mutate($mutationProbability);

    /**
     * @return string
     */
    public function getUniqueIdentifier()
    {
        /**
         * Usage of spl_object_hash is to ensure simultaneous uniqueness,
         * even if multiple objects have the same properties.
         */
        return md5(spl_object_hash($this) . json_encode($this));
    }
}