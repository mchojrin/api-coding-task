<?php

namespace App\DataFixtures;

use App\Factory\CharacterFactory;
use App\Factory\EquipmentFactory;
use App\Factory\FactionFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        CharacterFactory::createMany(5);
        FactionFactory::createMany(5);
        EquipmentFactory::createMany(5);

        $manager->flush();
    }
}
