<?php

class Population_ScoreManagerTest extends PHPUnit_Framework_TestCase {
    /**
     * @var DummyEvaluator
     */
    public $evaluator;

    /**
     * Because we know our heuristic, we'll put the high score citizen
     * to help us compare in our tests.
     * @var DummyCitizen
     */
    public $highestScoreCitizen;

    /**
     * Because we know our heuristic, we'll put the lowest score citizen
     * to help us compare in our tests.
     * @var DummyCitizen
     */
    public $lowestScoreCitizen;

    /**
     * @var \ChillGeneticAlgorithm\Population\ScoreManager
     */
    public $scoreManager;

    public function setUp()
    {
        parent::setUp();
        $romeo = new DummyCitizen();
        $romeo->favoriteFood = 'beefAndChickenFajitas';
        $this->highestScoreCitizen = $romeo;
        $juliet = new DummyCitizen();
        $juliet->favoriteFood = 'bbq';
        $this->lowestScoreCitizen = $juliet;
        $population = new \ChillGeneticAlgorithm\Population([$romeo, $juliet]);
        $this->evaluator = new DummyEvaluator();
        $this->scoreManager = new \ChillGeneticAlgorithm\Population\ScoreManager(
            $population,
            $this->evaluator
        );
        $this->scoreManager->evaluate();
    }

    public function testGetHighestScore()
    {
        $this->assertEquals(
            $this->evaluator->getScore($this->highestScoreCitizen),
            $this->scoreManager->getHighestScore()
        );
    }

    public function testGetTopPerformerUniqueIdentifier()
    {
        $topPerformersIdentifiers = $this->scoreManager->getTopPerformersUniqueIdentifiers(1);
        $this->assertEquals(
            $this->highestScoreCitizen->getUniqueIdentifier(),
            $topPerformersIdentifiers[0]
        );
    }

    public function testWorstPerformerUniqueIdentifier()
    {
        $worstPerformersIdentifiers = $this->scoreManager->getWorstPerformersUniqueIdentifiers(1);
        $this->assertEquals(
            $this->lowestScoreCitizen->getUniqueIdentifier(),
            $worstPerformersIdentifiers[0]
        );
    }
}