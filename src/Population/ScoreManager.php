<?php
namespace ChillGeneticAlgorithm\Population;

use ChillGeneticAlgorithm\Citizen;
use ChillGeneticAlgorithm\Citizen\Evaluator;
use ChillGeneticAlgorithm\Population;

class ScoreManager
{
    /**
     * @var Evaluator
     */
    protected $evaluator;

    /**
     * @var Population
     */
    protected $population;

    /**
     * Map: CitizenUniqueIdentifier => score
     * CitizenUniqueIdentifier is a string.
     * score is a float [0.0 - 100.0] where 100 is the perfect individual.
     *
     * @var array
     */
    protected $scoreBoard = [];

    /**
     * @var int
     */
    protected $scoreBoardLastSortType;

    const SORT_TYPE_SCORE_BOARD_TOP_PERFORMERS_FIRST = 1;
    const SORT_TYPE_SCORE_BOARD_WORST_PERFORMERS_FIRST = 2;
    const SORT_TYPE_SCORE_BOARD_UNSORTED = 3;

    /**
     * @param Evaluator $evaluator
     * @param Population $population
     */
    public function __construct(Population $population, Evaluator $evaluator)
    {
        $this->evaluator = $evaluator;
        $this->population = $population;
        $this->scoreBoardLastSortType = self::SORT_TYPE_SCORE_BOARD_UNSORTED;
    }

    /**
     * Iterate through the population and evaluate each citizen.
     */
    public function evaluate()
    {
        foreach ($this->getCitizens() as $citizenUniqueIdentifier => $citizen) {
            $this->scoreBoard[$citizenUniqueIdentifier] = $this->evaluator->getScore($citizen);
        }
    }

    /**
     * Returns a map CitizenUniqueIdentifier => Citizen
     * @return array
     */
    protected function getCitizens()
    {
        $citizens = [];
        foreach ($this->population->getAllCitizenUniqueIdentifiers() as $identifier) {
            $citizens[$identifier] = $this->population->getCitizenByUniqueIdentifier($identifier);
        }

        return $citizens;
    }

    /**
     * Get the first X citizen unique identifiers as they show up in the scoreboard.
     * You may want to sort the scoreboard before calling this method.
     * @param int $howMany
     * @return Citizen[]
     */
    protected function getFirstCitizensUniqueIdentifiersFromScoreBoard($howMany)
    {
        return array_keys(array_slice($this->scoreBoard, 0, $howMany));
    }

    /**
     * @return float
     */
    public function getHighestScore()
    {
        $this->sortScoreBoard(self::SORT_TYPE_SCORE_BOARD_TOP_PERFORMERS_FIRST);
        $scores = array_values($this->scoreBoard);
        return array_shift($scores);
    }

    /**
     * @param int $howMany
     * @return Citizen[]
     */
    public function getTopPerformersUniqueIdentifiers($howMany)
    {
        $this->sortScoreBoard(self::SORT_TYPE_SCORE_BOARD_TOP_PERFORMERS_FIRST);
        return $this->getFirstCitizensUniqueIdentifiersFromScoreBoard($howMany);
    }

    /**
     * @param int $howMany
     * @return Citizen[]
     */
    public function getWorstPerformersUniqueIdentifiers($howMany)
    {
        $this->sortScoreBoard(self::SORT_TYPE_SCORE_BOARD_WORST_PERFORMERS_FIRST);
        return $this->getFirstCitizensUniqueIdentifiersFromScoreBoard($howMany);

    }

    /**
     * Sort our internal score board with a given sort type.
     * @param int $sortType
     */
    protected function sortScoreBoard($sortType)
    {
        if ($sortType === $this->scoreBoardLastSortType) {
            // It is already sorted how we want.
            return;
        }

        switch ($sortType) {
            case self::SORT_TYPE_SCORE_BOARD_TOP_PERFORMERS_FIRST:
                arsort($this->scoreBoard);
                break;
            case self::SORT_TYPE_SCORE_BOARD_WORST_PERFORMERS_FIRST:
                asort($this->scoreBoard);
                break;
            case self::SORT_TYPE_SCORE_BOARD_UNSORTED:
                // Do nothing.
                break;
        }

        $this->scoreBoardLastSortType = $sortType;
    }
}