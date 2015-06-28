<?php
namespace ChillGeneticAlgorithm;
use ChillGeneticAlgorithm\Population;
use ChillGeneticAlgorithm\Population\ScoreManager;
use ChillGeneticAlgorithm\Citizen;

class SolverEngine
{
    /**
     * @var float
     */
    protected $acceptableErrorRate;

    /**
     * @var int
     */
    protected $citizensCountToReplacePerGeneration;

    /**
     * @var float
     */
    protected $mutationProbability;

    /**
     * @var int
     */
    protected $maximumIterations;

    /**
     * @var Population
     */
    protected $population;

    /**
     * @var ScoreManager
     */
    protected $scoreManager;

    /**
     * @param Population $population
     * @param ScoreManager $scoreManager
     * @param float $acceptableErrorRate [0-100] Where 0 is requiring the perfect solution.
     * @param int $citizensToReplacePerGeneration
     * @param float $mutationProbability [0-100] The mutation probability for each new offspring.
     * @param int $maximumIterations
     */
    public function __construct(
        Population $population,
        ScoreManager $scoreManager,
        $acceptableErrorRate,
        $citizensToReplacePerGeneration,
        $mutationProbability,
        $maximumIterations = PHP_INT_MAX
    ) {
        $this->population = $population;
        $this->scoreManager = $scoreManager;
        $this->acceptableErrorRate = $acceptableErrorRate;
        $this->citizensCountToReplacePerGeneration = $citizensToReplacePerGeneration;
        $this->mutationProbability = $mutationProbability;
        $this->maximumIterations = $maximumIterations;
    }

    /**
     * @param Citizen[]|array $children
     */
    protected function addChildrenToPopulation(array $children)
    {
        foreach ($children as $child) {
            $this->population->addCitizen($child);
        }
    }

    /**
     * @param Citizen $citizen
     * @param Citizen[]|array $potentialMates
     *
     * @return Citizen
     */
    protected function getPartnerForMating(Citizen $citizen, array $potentialMates)
    {
        return $potentialMates[array_rand($potentialMates)]; // This could return the same partner as $citizen.
    }

    /**
     * Return only the top performer. A single individual.
     * @return Citizen
     */
    protected function getTopPerformer()
    {
        $topPerformers = $this->getTopPerformers(1);
        return reset($topPerformers);
    }

    /**
     * Get the top performers for this iteration.
     * If you dont specify how many you want, it will return the same number
     * as defined in the citizensCountToReplacePerGeneration.
     *
     * @param int|null $howMany
     *
     * @return Citizen[]|array
     */
    protected function getTopPerformers($howMany = null)
    {
        $topPerformers = [];
        if ($howMany === null) {
            $howMany = $this->citizensCountToReplacePerGeneration;
        }
        $topPerformerIdentifiers = $this->scoreManager->getTopPerformersUniqueIdentifiers($howMany);
        foreach ($topPerformerIdentifiers as $identifier) {
            $topPerformers[] = $this->population->getCitizenByUniqueIdentifier($identifier);
        }

        return $topPerformers;
    }

    /**
     * @param float $score
     * @return bool
     */
    protected function isScoreAcceptable($score)
    {
        return (100 - $score <= $this->acceptableErrorRate);
    }

    /**
     * Should we mutate an individual? based on the mutationProbability.
     * @return bool
     */
    protected function shouldMutate()
    {
        $randomNumber = ((float) mt_rand() / (float) mt_getrandmax()) * 100;
        return ($randomNumber <= $this->mutationProbability);
    }

    /**
     * Call this to actually start the algorithm.
     * @return Citizen
     */
    public function solve()
    {
        $scoreManager = $this->scoreManager;
        $iterationCount = 0;
        while (true) {
            $scoreManager->evaluate();
            print_r($iterationCount . ":" . $scoreManager->getHighestScore() . PHP_EOL);
            if ($this->isScoreAcceptable($scoreManager->getHighestScore())
                || $iterationCount === $this->maximumIterations
            ) {
                break;
            }

            // Lets create new citizens from our top performers.
            $topPerformers = $this->getTopPerformers();
            /** @var Citizen[]|array $children */
            $children = [];
            foreach ($topPerformers as $topPerformer) {
                $partner = $this->getPartnerForMating($topPerformer, $topPerformers);
                $child = $topPerformer->mate($partner);
                if ($this->shouldMutate()) {
                    $child->mutate($this->mutationProbability);
                }
                $children[] = $child;
            }

            // Lets take out the bad performers.
            $this->removeWorstPerformersFromPopulation();
            // Add the kids.
            $this->addChildrenToPopulation($children);
            $iterationCount++;
        }

        return $this->getTopPerformer();
    }

    protected function removeWorstPerformersFromPopulation()
    {
        $worstPerformerIdentifiers = $this->scoreManager->getWorstPerformersUniqueIdentifiers(
            $this->citizensCountToReplacePerGeneration
        );
        foreach ($worstPerformerIdentifiers as $identifier) {
            $this->population->removeCitizenByUniqueIdentifier($identifier);
        }
    }
}