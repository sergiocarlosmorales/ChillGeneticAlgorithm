<?php
namespace ChillGeneticAlgorithm;
use ChillGeneticAlgorithm\Population;
use ChillGeneticAlgorithm\Population\ScoreManager;
use ChillGeneticAlgorithm\Citizen;

class SolverEngine
{
    /**
     * @var Population
     */
    protected $population;

    /**
     * @var Population_ScoreManager
     */
    protected $scoreManager;

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
     * @param Population $population
     * @param ScoreManager $scoreManager
     * @param float $acceptableErrorRate [0-100]
     * @param int $citizensToReplacePerGeneration
     * @param float $mutationProbability [0-100]
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

    /**
     * @param float $score
     * @return bool
     */
    protected function isScoreAcceptable($score)
    {
        return (100 - $score <= $this->acceptableErrorRate);
    }

    /**
     * @return Citizen[]|array
     */
    protected function getTopPerformers()
    {
        $topPerformers = [];
        $topPerformerIdentifiers = $this->scoreManager->getTopPerformersUniqueIdentifiers(
            $this->citizensCountToReplacePerGeneration
        );
        foreach ($topPerformerIdentifiers as $identifier) {
            $topPerformers[] = $this->population->getCitizenByUniqueIdentifier($identifier);
        }

        return $topPerformers;
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
     * @return Citizen
     */
    protected function getTopPerformer()
    {
        $topPerformers = $this->getTopPerformers();
        return reset($topPerformers);
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

    protected function shouldMutate()
    {
        $randomNumber = ((float) mt_rand() / (float) mt_getrandmax()) * 100;
        return ($randomNumber <= $this->mutationProbability);
    }
}