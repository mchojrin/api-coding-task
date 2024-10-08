<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CharacterControllerTest extends WebTestCase
{
    /**
     * @return void
     * @test
     */
    public function shouldReturnCompleteCharacterList(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/character');

        $this->assertResponseIsSuccessful();
    }
}
