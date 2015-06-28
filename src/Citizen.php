<?php
namespace ChillGeneticAlgorithm;

abstract class Citizen
{
    /**
     * Get this citizen's unique identifier.
     * @return string
     */
    public function getUniqueIdentifier()
    {
        /**
         * Usage of spl_object_hash is to ensure simultaneous uniqueness.
         * If you override this, do not rely -only- on the properties of an object
         * to determine what the unique identifier should be, remember there may be
         * multiple elements in the population with the same properties.
         */
        return md5(spl_object_hash($this) . json_encode($this));
    }

    /**
     * This should return a NEW citizen with mixed traits from both partners
     * @param Citizen $partner
     * @return Citizen
     */
    abstract function mate(Citizen $partner);

    /**
     * Public entry point to mutate a citizen.
     * The mutation probability can be used to determine 'how much' to mutate.
     * If the mutation probability is zero then there will be no mutation at all.
     * This is just a wrapper, the actual logic is defined in _mutate.
     * @param float $mutationProbability
     */
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
     */
    abstract function _mutate($mutationProbability);
}