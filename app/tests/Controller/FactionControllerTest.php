<?php

namespace App\Tests\Controller;

use App\Entity\Character;
use App\Entity\Faction;
use App\Repository\FactionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FactionControllerTest extends WebTestCase
{
    private readonly FactionRepository $factionRepository;
    private readonly EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = self::getContainer()->get('doctrine')->getManager();
        $this->factionRepository = $this->entityManager->getRepository(Faction::class);
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

    /**
     * @return void
     * @test
     */
    public function shouldAllowDeletingFactions(): void
    {
        $toDelete = new Faction( faction_name: 'Delete me', description: 'Faction to be deleted');

        $this->entityManager->persist($toDelete);
        $this->entityManager->flush();

        $this->assertNotEmpty($this->factionRepository->find($toDelete->getId()));

        $this->client->request(
            'DELETE',
            '/faction/' . $toDelete->getId()
        );

        $this->assertResponseIsSuccessful();
        $this->assertEmpty($this->factionRepository->findOneBy(['id' => $toDelete->getId()]));
    }
}
