<?php

namespace App\Tests\Controller;

use App\Entity\Faction;
use App\Entity\User;
use App\Factory\FactionFactory;
use App\Repository\FactionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class FactionControllerTest extends WebTestCase
{
    const BASE_URI = '/factions/';
    const CHARACTERS_DETAILS_PREFIX = '/characters/';
    private readonly FactionRepository $factionRepository;
    private readonly EntityManagerInterface $entityManager;
    private string $token;

    /**
     */
    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = self::getContainer()->get('doctrine')->getManager();
        $this->factionRepository = $this->entityManager->getRepository(Faction::class);

        $this->createUser();
    }

    protected function tearDown(): void
    {
        $this->removeUser();
        parent::tearDown();
    }

    #[Test]
    public function shouldReturnCompleteFactionList(): void
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

        $factions = $this->factionRepository->findAll();
        foreach ($factions as $faction) {
            $this->assertTrue($this->in_array($faction, $obtainedArray));
        }
    }

    #[Test]
    public function shouldReturnFactionDetails(): void
    {
        $faction = $this->createFaction();
        $this->entityManager->persist($faction);
        $this->entityManager->flush();

        $this->client->request(
            'GET',
            self::BASE_URI.$faction->getId()
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseFormatSame("json");

        $obtainedArray = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals($faction->getId(), $obtainedArray['id']);
        $this->assertEquals($faction->getFactionName(), $obtainedArray['faction_name']);
        $this->assertEquals($faction->getDescription(), $obtainedArray['description']);
        foreach ($faction->getCharacters() as $character) {
            $this->assertContains( self::CHARACTERS_DETAILS_PREFIX.$character->getId(), $obtainedArray['characters']);
        }

        $this->entityManager->remove($faction);
        $this->entityManager->flush();
    }
    
    #[Test]
    public function shouldAllowCreatingNewFactions(): void
    {
        $newFactionData = [
            'faction_name' => 'New faction',
            'description' => 'New faction description',
        ];

        $this->client->request(
            'POST',
            self::BASE_URI,
            server: [
                'HTTP_X-AUTH-TOKEN' => $this->token,
            ],
            content: json_encode($newFactionData),
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

    #[Test]
    public function shouldAllowDeletingFactions(): void
    {
        $toDelete = new Faction(faction_name: 'Delete me', description: 'Faction to be deleted');

        $this->entityManager->persist($toDelete);
        $this->entityManager->flush();

        $this->assertNotEmpty($this->factionRepository->find($toDelete->getId()));

        $this->client->request(
            'DELETE',
            self::BASE_URI . $toDelete->getId(),
            server: [
                'HTTP_X-AUTH-TOKEN' => $this->token,
            ],
        );

        $this->assertResponseIsSuccessful();
        $this->assertEmpty($this->factionRepository->findOneBy(['id' => $toDelete->getId()]));
    }

    /**
     * @throws ORMException
     */
    #[Test]
    public function shouldAllowUpdatingFactions(): void
    {
        $toUpdate = new Faction(faction_name: 'Change me', description: 'Faction to be updated');

        $this->entityManager->persist($toUpdate);
        $this->entityManager->flush();
        $this->assertNotEmpty($this->factionRepository->find($toUpdate->getId()));

        $this->client->request(
            'PATCH',
            self::BASE_URI . $toUpdate->getId(),
            server: [
                'HTTP_X-AUTH-TOKEN' => $this->token,
            ],
            content: json_encode(['faction_name' => "Changed"])
        );

        $this->assertResponseIsSuccessful();
        $this->entityManager->refresh($toUpdate);
        $this->assertEquals("Changed", $toUpdate->getFactionName());
    }

    protected function createUser(): void
    {
        $this->token = bin2hex(random_bytes(36));
        $user = new User();
        $user->setToken($this->token);
        $user->setRoles(['ROLE_USER']);
        $user->setEmail("test@test.com");
        $user->setPassword("password");
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    protected function removeUser(): void
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $this->token]);
        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }

    private function in_array(Faction $needle, array $haystack): bool
    {
        foreach ($haystack as $item) {
            if ($this->hasExpectedProperties($item, $needle)) {

                return true;
            }
        }

        return false;
    }

    private function hasExpectedProperties(array $item, Faction $needle): bool
    {
        return
            $item['id'] === $needle->getId() &&
            $item['description'] === $needle->getDescription() &&
            $item['faction_name'] === $needle->getFactionName()
            ;
    }

    protected function createFaction(): Faction
    {
       return new Faction(
           'New faction name',
           'New faction description',
       );
    }
}
