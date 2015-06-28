<?php

/**
 * This file contains dummy classes for tests.
 * KEEP THESE CLASSES AS DUMB AS POSSIBLE !!!!one!!1!!uno!!!!
 * You do not want to have a bug here, otherwise
 * it could be a false positive test failure.
 */




class DummyCitizen extends \ChillGeneticAlgorithm\Citizen
{
    /**
     * @var string
     */
    public $favoriteFood;

    /**
     * @param float $mutationProbability
     */
    public function _mutate($mutationProbability)
    {
        $this->favoriteFood .= ' with cheese';
    }

    public function mate(\ChillGeneticAlgorithm\Citizen $partner)
    {
        return new DummyCitizen();
    }
}

/**
 * Class DummyEvaluator
 * This dummy evaluator gives a score based on how long is the
 * 'favoriteFood' property of a citizen.
 * If it is 100 characters long or greater, it gives a perfect score.
 * If the string is empty, it returns a score of 0.
 */
class DummyEvaluator extends \ChillGeneticAlgorithm\Citizen\Evaluator
{
    /**
     * @param \ChillGeneticAlgorithm\Citizen $citizen
     * @return float
     */
    public function getScore(\ChillGeneticAlgorithm\Citizen $citizen)
    {
        $length = strlen($citizen->favoriteFood);
        if ($length >= 100) {
            return 100.0;
        }

        return floatval($length);
    }
}