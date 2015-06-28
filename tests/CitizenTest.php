<?php

class CitizenTest extends PHPUnit_Framework_TestCase
{
    public function testTwoCitizensWithSamePropertiesHaveDifferentUniqueIdentifiers()
    {
        $favoriteFood = 'tacos';
        $romeo = new DummyCitizen();
        $romeo->favoriteFood = $favoriteFood;
        $juliet = new DummyCitizen();
        $juliet->favoriteFood = $favoriteFood;
        $this->assertNotEquals(
            $romeo->getUniqueIdentifier(),
            $juliet->getUniqueIdentifier()
        );
    }

    public function testMutateAltersTheCitizen()
    {
        $citizen = new DummyCitizen();
        $reference = clone $citizen;
        $citizen->mutate(100);
        $this->assertNotEquals(
            $reference,
            $citizen
        );
    }

    public function testMateReturnsANewCitizen()
    {
        $romeo = new DummyCitizen();
        $romeo->favoriteFood = 'pizza';
        $juliet = new DummyCitizen();
        $juliet->favoriteFood = 'tacos';
        $littleRomy = $romeo->mate($juliet);
        $this->assertNotEquals(
            $romeo,
            $littleRomy
        );
        $this->assertNotEquals(
            $juliet,
            $littleRomy
        );
    }

}