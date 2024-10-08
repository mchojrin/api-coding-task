<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FactionControllerTest extends WebTestCase
{
    /**
     * @test
     */
    public function shouldAllowCreatingNewFactions(): void
    {
        $client = static::createClient();
        $newFactionData = [
            'faction_name' => 'New faction',
            'description' => 'New faction description',
        ];

        $client->request(
            'POST',
            '/faction',
            content: json_encode($newFactionData)
        );

        $this->assertResponseIsSuccessful();
    }
}
