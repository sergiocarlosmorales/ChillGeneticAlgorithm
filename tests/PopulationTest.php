<?php

class PopulationTest extends PHPUnit_Framework_TestCase
{
    public function testCitizenCount()
    {
        $citizens = $this->getCitizens();
        $population = new \ChillGeneticAlgorithm\Population($citizens);
        $this->assertEquals(
            count($citizens),
            $population->getCount()
        );
    }

    public function testGetAllCitizenUniqueIdentifiersReturnsAllCitizensIdentifiers()
    {
        $citizens = $this->getCitizens();
        $population = new \ChillGeneticAlgorithm\Population($citizens);
        $uniqueIdentifiersFromCitizens = [];
        foreach ($citizens as $citizen) {
            $uniqueIdentifiersFromCitizens[] = $citizen->getUniqueIdentifier();
        }
        $uniqueIdentifiersFromPopulation = $population->getAllCitizenUniqueIdentifiers();
        $this->assertEquals(
            count($citizens),
            count($uniqueIdentifiersFromPopulation)
        );
        foreach ($uniqueIdentifiersFromPopulation as $identifier) {
            $this->assertContains(
                $identifier,
                $uniqueIdentifiersFromPopulation
            );
        }
    }

    public function testCanRetrieveCitizenByUniqueIdentifier()
    {
        $citizens = $this->getCitizens();
        $citizen = $citizens[0];
        $population = new \ChillGeneticAlgorithm\Population($citizens);
        $this->assertEquals(
            $citizen,
            $population->getCitizenByUniqueIdentifier($citizen->getUniqueIdentifier())
        );
    }

    public function testIfCitizenIdentifierDoesNotExistInPopulationReturnsNull()
    {
        $citizens = $this->getCitizens();
        $population = new \ChillGeneticAlgorithm\Population($citizens);
        $this->assertNull($population->getCitizenByUniqueIdentifier('somethingSuperRandom-OMG-BBQ-LOL-KEK'));
    }

    public function testRemoveCitizen()
    {
        $citizens = $this->getCitizens();
        $citizen = $citizens[0];
        $population = new \ChillGeneticAlgorithm\Population($citizens);
        $population->removeCitizenByUniqueIdentifier($citizen->getUniqueIdentifier());
        $this->assertNull($population->getCitizenByUniqueIdentifier($citizen->getUniqueIdentifier()));
    }

    public function testAddCitizen()
    {
        $citizens = $this->getCitizens();
        $population = new \ChillGeneticAlgorithm\Population($citizens);
        $newCitizen = new DummyCitizen();
        $this->assertNull(
            $population->getCitizenByUniqueIdentifier($newCitizen->getUniqueIdentifier())
        );
        $population->addCitizen($newCitizen);
        $this->assertEquals(
            $newCitizen,
            $population->getCitizenByUniqueIdentifier($newCitizen->getUniqueIdentifier())
        );
    }

    /**
     * Utility function to return citizens.
     * @return DummyCitizen[]|array
     */
    protected function getCitizens()
    {
        $romeo = new DummyCitizen();
        $romeo->favoriteFood = 'pizza';
        $juliet = new DummyCitizen();
        $juliet->favoriteFood = 'tacos';

        return [$romeo, $juliet];
    }
}