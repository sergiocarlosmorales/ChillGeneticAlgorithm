<?php
namespace ChillGeneticAlgorithm\Population;
use ChillGeneticAlgorithm\Population;
use ChillGeneticAlgorithm\Citizen\Evaluator;
use ChillGeneticAlgorithm\Citizen;

class ScoreManager
{
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
     * @var Evaluator
     */
    protected $evaluator;

    /**
     * @var int
     */
    protected $scoreBoardLastSortType;

    const TOP_PERFORMERS_FIRST_SCORE_BOARD_SORT_TYPE = 1;
    const WORST_PERFORMERS_FIRST_SCORE_BOARD_SORT_TYPE = 2;
    const UNSORTED_SCORE_BOARD_SORT_TYPE = 3;

    /**
     * @param Evaluator $evaluator
     * @param Population $population
     */
    public function __construct(Population $population, Evaluator $evaluator)
    {
        $this->population = $population;
        $this->evaluator = $evaluator;
        $this->scoreBoardLastSortType = self::UNSORTED_SCORE_BOARD_SORT_TYPE;
    }

    public function evaluate()
    {
        foreach ($this->getCitizens() as $citizenUniqueIdentifier => $citizen) {
            $this->scoreBoard[$citizenUniqueIdentifier] = $this->evaluator->getScore($citizen);
        }
    }

    /**
     * Map CitizenUniqueIdentifier => Citizen
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
     * @param int $howMany
     * @return Citizen[]
     */
    public function getTopPerformersUniqueIdentifiers($howMany)
    {
        $this->sortScoreBoard(self::TOP_PERFORMERS_FIRST_SCORE_BOARD_SORT_TYPE);
        return $this->getFirstCitizensUniqueIdentifiersFromScoreBoard($howMany);
    }

    /**
     * @param int $howMany
     * @return Citizen[]
     */
    public function getWorstPerformersUniqueIdentifiers($howMany)
    {
        $this->sortScoreBoard(self::WORST_PERFORMERS_FIRST_SCORE_BOARD_SORT_TYPE);
        return $this->getFirstCitizensUniqueIdentifiersFromScoreBoard($howMany);

    }

    /**
     * @param int $sortType
     */
    protected function sortScoreBoard($sortType)
    {
        if ($sortType === $this->scoreBoardLastSortType) {
            // It is already sorted how we want.
            return;
        }

        switch ($sortType) {
            case self::TOP_PERFORMERS_FIRST_SCORE_BOARD_SORT_TYPE:
                arsort($this->scoreBoard);
                break;
            case self::WORST_PERFORMERS_FIRST_SCORE_BOARD_SORT_TYPE:
                asort($this->scoreBoard);
                break;
            case self::UNSORTED_SCORE_BOARD_SORT_TYPE:
                // Do nothing.
                break;
        }

        $this->scoreBoardLastSortType = $sortType;
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
        $this->sortScoreBoard(self::TOP_PERFORMERS_FIRST_SCORE_BOARD_SORT_TYPE);
        $scores = array_values($this->scoreBoard);
        return array_shift($scores);
    }
}