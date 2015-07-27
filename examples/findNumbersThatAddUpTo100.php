<?php
require '../vendor/autoload.php';

class ThreeDigits extends \ChillGeneticAlgorithm\Citizen
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

    public function mate(\ChillGeneticAlgorithm\Citizen $partner)
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

class DigitsAddUpTo100 extends \ChillGeneticAlgorithm\Citizen\Evaluator
{
    public function getScore(\ChillGeneticAlgorithm\Citizen $citizen)
    {
        $sum = abs($citizen->digit1 + $citizen->digit2 + $citizen->digit3);
        if ($sum > 100) {
            return rand(0, 10);
        }
        return $sum;
    }
}

$citizens = [];
//Lets create our initial population.
for ($i=0; $i<10000; $i++) {
    $citizen = new ThreeDigits();
    $citizen->digit1 = rand(-10000, 10000);
    $citizen->digit2 = rand(-10000, 10000);
    $citizen->digit3 = rand(-10000, 10000);
    $citizens[] = $citizen;
}

$population = new \ChillGeneticAlgorithm\Population($citizens);
$evaluator = new DigitsAddUpTo100();
$scoreManager = new \ChillGeneticAlgorithm\Population\ScoreManager($population, $evaluator);
$solverEngine = new \ChillGeneticAlgorithm\SolverEngine($population, $scoreManager, 0, 500, 35, 1000);
print_r($solverEngine->solve());