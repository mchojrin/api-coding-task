<?php

namespace App\Tests\Controller;

use App\Entity\Equipment;
use App\Entity\User;
use App\Repository\EquipmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Exception;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class EquipmentControllerTest extends WebTestCase
{
    private const BASE_URI = '/equipments/';

    const CHARACTERS_DETAILS_PREFIX = '/characters/';
    private readonly EquipmentRepository $equipmentRepository;
    private readonly EntityManagerInterface $entityManager;
    private string $token;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = self::getContainer()->get('doctrine')->getManager();
        $this->equipmentRepository = $this->entityManager->getRepository(Equipment::class);

        $this->createUser();
    }

    protected function tearDown(): void
    {
        $this->removeUser();
        parent::tearDown();
    }

    #[Test]
    public function shouldAllowCreatingNewEquipments(): void
    {
        $newEquipmentData = [
            'name' => 'New equipment',
            'type' => 'New equipment type',
            'made_by' => 'New equipment made by',
        ];

        $this->client->request(
            'POST',
            self::BASE_URI,
            server: [
                'HTTP_X-AUTH-TOKEN' => $this->token,
            ],
            content: json_encode($newEquipmentData),
        );

        $this->assertResponseIsSuccessful();
        $actualResult = json_decode(
            $this->client->getResponse()->getContent(),
            true
        );
        $newEquipment = $this->equipmentRepository->findOneBy(['id' => $actualResult['id']]);
        $this->assertEquals($newEquipment->getName(), $newEquipmentData['name']);
        $this->assertEquals($newEquipment->getType(), $newEquipmentData['type']);
        $this->assertEquals($newEquipment->getMadeBy(), $newEquipmentData['made_by']);
    }

    #[Test]
    public function shouldAllowDeletingEquipments(): void
    {
        $toDelete = new Equipment(
            name: 'Delete me',
            type: 'Equipment to be deleted',
            made_by: 'Made by to be deleted'
        );

        $this->entityManager->persist($toDelete);
        $this->entityManager->flush();

        $this->assertNotEmpty($this->equipmentRepository->find($toDelete->getId()));

        $this->client->request(
            'DELETE',
            self::BASE_URI . $toDelete->getId(),
            server: [
                'HTTP_X-AUTH-TOKEN' => $this->token,
            ],
        );

        $this->assertResponseIsSuccessful();
        $this->assertEmpty($this->equipmentRepository->findOneBy(['id' => $toDelete->getId()]));
    }

    /**
     * @throws ORMException
     */
    #[Test]
    public function shouldAllowUpdatingEquipments(): void
    {
        $toUpdate = new Equipment(
            name: 'Change me',
            type: 'Equipment to be updated type',
            made_by: 'Equipment to be updated made by',
        );

        $this->entityManager->persist($toUpdate);
        $this->entityManager->flush();
        $this->assertNotEmpty($this->equipmentRepository->find($toUpdate->getId()));

        $this->client->request(
            'PATCH',
            self::BASE_URI . $toUpdate->getId(),
            server: [
                'HTTP_X-AUTH-TOKEN' => $this->token,
            ],
            content: json_encode(['name' => "Changed"])
        );

        $this->assertResponseIsSuccessful();
        $this->entityManager->refresh($toUpdate);
        $this->assertEquals("Changed", $toUpdate->getName());
    }

    #[Test]
    public function shouldReturnCompleteEquipmentList(): void
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

        $equipments = $this->equipmentRepository->findAll();
        foreach ($equipments as $equipment) {
            $this->assertTrue($this->in_array($equipment, $obtainedArray));
        }
    }

    #[Test]
    public function shouldReturnEquipmentDetails(): void
    {
        $equipment = $this->createEquipment();
        $this->entityManager->persist($equipment);
        $this->entityManager->flush();

        $this->client->request(
            'GET',
            self::BASE_URI.$equipment->getId(),
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseFormatSame("json");

        $obtainedArray = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals($equipment->getName(), $obtainedArray['name']);
        $this->assertEquals($equipment->getMadeBy(), $obtainedArray['made_by']);
        $this->assertEquals($equipment->getType(), $obtainedArray['type']);
        foreach ($equipment->getCharacters() as $character) {
            $this->assertContains( self::CHARACTERS_DETAILS_PREFIX.$character->getId(), $obtainedArray['characters']);
        }

        $this->entityManager->remove($equipment);
        $this->entityManager->flush();
    }

    private function in_array(Equipment $needle, array $haystack): bool
    {
        foreach ($haystack as $item) {
            if ($this->hasExpectedProperties($item, $needle)) {

                return true;
            }
        }

        return false;
    }

    private function hasExpectedProperties(array $item, Equipment $needle): bool
    {
        return
            $item['id'] === $needle->getId() &&
            $item['name'] === $needle->getName() &&
            $item['type'] === $needle->getType() &&
            $item['made_by'] === $needle->getMadeBy()
            ;
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

    private function createEquipment(): Equipment
    {
        return new Equipment(
            name: 'New equipment',
            type: 'Equipment to be deleted',
            made_by: 'New equipment made by',
        );
    }
}
