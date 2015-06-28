<?php

class BasicTest extends PHPUnit_Framework_TestCase {

    public function testSomething()
    {
        $citizens = [];

        $population = new \ChillGeneticAlgorithm\Population($citizens);
        $evaluator = new DigitsAddUpTo100();
        $scoreManager = new \ChillGeneticAlgorithm\Population\ScoreManager($population, $evaluator);
        $solverEngine = new \ChillGeneticAlgorithm\SolverEngine($population, $scoreManager, 0, 500, 35, 1000);
        // Dummy test.
        $this->assertNotNull($solverEngine);
    }

}