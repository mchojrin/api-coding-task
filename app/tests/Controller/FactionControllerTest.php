<?php

namespace App\Tests\Controller;

use App\Entity\Faction;
use App\Entity\User;
use App\Repository\FactionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FactionControllerTest extends WebTestCase
{
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

    /**
     * @return void
     * @test
     */
    public function shouldAllowDeletingFactions(): void
    {
        $toDelete = new Faction(faction_name: 'Delete me', description: 'Faction to be deleted');

        $this->entityManager->persist($toDelete);
        $this->entityManager->flush();

        $this->assertNotEmpty($this->factionRepository->find($toDelete->getId()));

        $this->client->request(
            'DELETE',
            '/faction/' . $toDelete->getId(),
            server: [
                'HTTP_X-AUTH-TOKEN' => $this->token,
            ],
        );

        $this->assertResponseIsSuccessful();
        $this->assertEmpty($this->factionRepository->findOneBy(['id' => $toDelete->getId()]));
    }

    /**
     * @throws ORMException
     * @test
     */
    public function shouldAllowUpdatingFactions(): void
    {
        $toUpdate = new Faction(faction_name: 'Change me', description: 'Faction to be updated');

        $this->entityManager->persist($toUpdate);
        $this->entityManager->flush();
        $this->assertNotEmpty($this->factionRepository->find($toUpdate->getId()));

        $this->client->request(
            'PATCH',
            '/faction/' . $toUpdate->getId(),
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
}
