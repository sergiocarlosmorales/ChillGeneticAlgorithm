<?php

class Citizen_EvaluatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * Because we know our dummy heuristic, we can test this.
     * This may not apply to all heuristics.
     */
    public function testEvaluatorGivesTwoDifferentScoresToDifferentObjects()
    {
        $juan = new DummyCitizen();
        $juan->favoriteFood = 'tacos';

        $pedro = new DummyCitizen();
        $pedro->favoriteFood = 'tamales';

        $evaluator = new DummyEvaluator();
        $this->assertNotEquals(
            $evaluator->getScore($juan),
            $evaluator->getScore($pedro)
        );
    }
}