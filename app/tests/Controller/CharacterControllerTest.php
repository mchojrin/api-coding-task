<?php

namespace App\Tests\Controller;

use App\Entity\Character;
use App\Entity\Equipment;
use App\Entity\Faction;
use App\Repository\CharacterRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class CharacterControllerTest extends WebTestCase
{
    const BASE_URI = '/characters/';
    private readonly CharacterRepository $characterRepository;

    private readonly EntityManagerInterface $entityManager;

    private readonly KernelBrowser $client;
    private Equipment $equipment;
    private Faction $faction;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = self::getContainer()->get('doctrine')->getManager();
        $this->characterRepository = $this->entityManager->getRepository(Character::class);

        $this->equipment = $this->createEquipment();
        $this->faction = $this->createFaction();

        $this->entityManager->persist($this->equipment);
        $this->entityManager->persist($this->faction);
        $this->entityManager->flush();
    }

    protected function tearDown(): void
    {
        $this->removeEquipment();
        $this->removeFaction();

        $this->entityManager->flush();
    }
    /**
     * @test
     */
    public function shouldReturnCompleteCharacterList(): void
    {
        $this->client->request(
            'GET',
            self::BASE_URI
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseFormatSame("json");

        $jsonResponse = $this->client->getResponse()->getContent();

        $obtainedArray = json_decode($jsonResponse, true);
        $characters = $this->characterRepository->findAll();
        foreach ($characters as $character) {
            $this->assertTrue($this->in_array($character, $obtainedArray));
        }
    }

    /**
     * @test
     */
    public function shouldAllowCreatingNewCharacters(): void
    {
        $newCharacterData = [
            'name' => 'New Character',
            'kingdom' => 'New Kingdom',
            'equipment_id' => $this->equipment->getId(),
            'faction_id' => $this->faction->getId(),
            'birth_date' => '1981-04-13',
        ];
        $this->client->request(
            'POST',
            self::BASE_URI,
            content: json_encode($newCharacterData)
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseFormatSame("json");

        $jsonResponse = $this->client->getResponse()->getContent();

        $obtainedArray = json_decode($jsonResponse, true);
        $foundCharacter = $this->findCharacter($obtainedArray['id']);
        $this->assertNotEmpty($foundCharacter);
        $this->assertEquals($newCharacterData['name'], $foundCharacter->getName());
        $this->assertEquals($newCharacterData['kingdom'], $foundCharacter->getKingdom());
        $this->assertEquals($newCharacterData['birth_date'], $foundCharacter->getBirthDate()->format('Y-m-d'));
        $this->assertEquals($newCharacterData['equipment_id'], $foundCharacter->getEquipment()->getId());
        $this->assertEquals($newCharacterData['faction_id'], $foundCharacter->getFaction()->getId());

        $this->entityManager->remove($foundCharacter);
    }

    /**
     * @test
     */
    public function shouldAllowDeletingCharacters(): void
    {
        $newCharacter = new Character(
            name: 'New Character',
            birth_date: new DateTimeImmutable('1981-04-13'),
            kingdom: 'New Kingdom',
            equipment: $this->equipment,
            faction: $this->faction,
        );
        $this->entityManager->persist($newCharacter);
        $this->entityManager->flush();
        $newCharacterId = $newCharacter->getId();

        $this->client->request(
            'DELETE',
            self::BASE_URI.$newCharacter->getId()
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseFormatSame("json");

        $foundCharacter = $this->findCharacter($newCharacterId);
        $this->assertEmpty($foundCharacter);
    }

    private function in_array(Character $needle, array $haystack): bool
    {
        return in_array([
            'id' => $needle->getId(),
            'name' => $needle->getName(),
            'birth_date' => $needle->getBirthDate()->format('Y-m-d'),
            'kingdom' => $needle->getKingdom(),
            'equipment' => '/equipments/'.$needle->getEquipment()->getId(),
            'faction' => '/factions/'.$needle->getFaction()->getId(),
            ], $haystack);
    }

    private function findCharacter(int $id): ?Character
    {
        return $this->characterRepository->find($id);
    }

    private function createFaction(): Faction
    {
        return new Faction(
            'New faction name',
            'New faction description',
        );
    }

    private function createEquipment(): Equipment
    {
        return new Equipment(
            'New equipment name',
            'New equipment type',
            'New equipment maker',
        );
    }

    private function removeEquipment(): void
    {
        $this->entityManager->remove($this->equipment);
    }

    private function removeFaction(): void
    {
        $this->entityManager->remove($this->faction);
    }
}
