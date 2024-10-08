<?php

namespace App\Tests\Controller;

use App\Entity\Character;
use App\Repository\CharacterRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class CharacterControllerTest extends WebTestCase
{
    private readonly CharacterRepository $characterRepository;
    private readonly KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $entityManager = self::getContainer()->get('doctrine')->getManager();
        $this->characterRepository = $entityManager->getRepository(Character::class);
    }

    /**
     * @return void
     * @test
     */
    public function shouldReturnCompleteCharacterList(): void
    {
        $this->client->request('GET', '/character');

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseFormatSame("json");

        $jsonResponse = $this->client->getResponse()->getContent();

        $obtainedArray = json_decode($jsonResponse, true);
        $characters = $this->characterRepository->findAll();
        foreach ($characters as $character) {
            $this->assertTrue($this->contains($obtainedArray, $character));
        }
    }

    private function contains(array $obtainedArray, Character $character): bool
    {
        $lookingFor = [
            'id' => $character->getId(),
            'name' => $character->getName(),
            'birth_date' => $character->getBirthDate()->format('Y-m-d'),
            'kingdom' => $character->getKingdom(),
            'equipment' => '/equipment/'.$character->getEquipment()->getId(),
            'faction' => '/faction/'.$character->getFaction()->getId(),
            ];

        return in_array($lookingFor, $obtainedArray);
    }
}
