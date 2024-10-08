<?php

namespace App\Tests\Controller;

use App\Entity\Character;
use App\Entity\Faction;
use App\Repository\FactionRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FactionControllerTest extends WebTestCase
{
    private readonly FactionRepository $factionRepository;
    protected function setUp(): void
    {
        $this->client = static::createClient();

        $entityManager = self::getContainer()->get('doctrine')->getManager();
        $this->factionRepository = $entityManager->getRepository(Faction::class);
    }


    /**
     * @test
     */
    public function shouldAllowCreatingNewFactions(): void
    {
        $newFactionData = [
            'faction_name' => 'New faction',
            'description' => 'New faction description',
        ];

        $this->client->request(
            'POST',
            '/faction',
            content: json_encode($newFactionData)
        );

        $this->assertResponseIsSuccessful();
        $actualResult = json_decode(
            $this->client->getResponse()->getContent(),
            true
        );
        $newFactionId = $actualResult['id'];
        $newFaction = $this->factionRepository->findOneBy(['id' => $newFactionId]);
        $this->assertEquals($newFaction->getFactionName(), $newFactionData['faction_name']);
        $this->assertEquals($newFaction->getDescription(), $newFactionData['description']);
    }
}
