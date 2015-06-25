<?php
// Need to set up composer.
// Quick test.
require "Citizen/Evaluator.php";
require "Population/ScoreManager.php";
require "Citizen.php";
require "Population.php";
require "SolverEngine.php";
use ChillGeneticAlgorithm\Citizen;
use ChillGeneticAlgorithm\Citizen_Evaluator;
use ChillGeneticAlgorithm\Population;
use ChillGeneticAlgorithm\Population_ScoreManager;
use ChillGeneticAlgorithm\SolverEngine;

class ThreeDigits extends Citizen
{
    public $digit1;
    public $digit2;
    public $digit3;

    public function _mutate($mutationProbability)
    {
        $randomNumberToAdd = rand(-200000, 200000);
        $randomPropertyNumber = rand(1,3);
        $propertyToMutate = 'digit' . $randomPropertyNumber;
        $this->$propertyToMutate += $randomNumberToAdd;
    }

    public function mate(Citizen $partner)
    {
        $children = new ThreeDigits();
        $children->digit1 = $this->digit1;
        $children->digit2 = (rand(1,2) % 2 == 0)
            ? $this->digit2
            : $partner->digit2;
        $children->digit3 = $partner->digit3;
        return $children;
    }
}

class DigitsAddUpTo100 extends Citizen_Evaluator
{
    public function getScore(Citizen $citizen)
    {
        $sum = abs($citizen->digit1 + $citizen->digit2 + $citizen->digit3);
        if ($sum > 100) {
            return rand(0, 10);
        }
        return $sum;
    }
}

$citizens = [];
//Lets create our initial population
for ($i=0; $i<1000; $i++) {
    $citizen = new ThreeDigits();
    $citizen->digit1 = rand(-500000, 500000);
    $citizen->digit2 = rand(-500000, 500000);
    $citizen->digit3 = rand(-500000, 500000);
    $citizens[] = $citizen;
}

$population = new Population($citizens);
$scoreManager = new Population_ScoreManager($population, 'DigitsAddUpTo100');
$solverEngine = new SolverEngine($population, $scoreManager, 0, 500, 35, 1000);
print_r($solverEngine->solve());
