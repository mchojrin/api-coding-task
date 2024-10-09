<?php

namespace App\Tests\Controller;

use App\Entity\Character;
use App\Repository\CharacterRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class CharacterControllerTest extends WebTestCase
{
    const BASE_URI = '/characters/';
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
}
