<?php
namespace ChillGeneticAlgorithm\Citizen;
use ChillGeneticAlgorithm\Citizen;

abstract class Evaluator
{
    /**
     * Evaluate how fit an individual is.
     * 0.00 being the least fit individual, 100.0 being the perfect individual (like me).
     * @param Citizen $citizen
     * @return float [0.00-100.0]
     */
    abstract function getScore(Citizen $citizen);
}