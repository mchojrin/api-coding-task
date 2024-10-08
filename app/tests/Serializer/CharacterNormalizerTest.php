<?php

namespace App\Tests\Serializer;

use App\Entity\Character;
use App\Entity\Equipment;
use App\Entity\Faction;
use App\Serializer\CharacterNormalizer;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CharacterNormalizerTest extends TestCase
{
    /**
     * @throws ExceptionInterface
     * @test
     */
    public function shouldNormalizeACharacter(): void
    {
        $birth_date = new DateTimeImmutable("1977-12-22");

        $aCharacter = new Character(
            10,
            "Mauro",
            $birth_date,
            "My Kingdom",
            new Equipment(
                9,
                "My equipment",
                "EquipmentType",
                "Amazon"
            )
            , new Faction(
                7,
                "My Faction",
                "Faction description",
            )
        );
        $baseNormalizer = $this->createMock(NormalizerInterface::class);
        $baseNormalizer
            ->expects(self::once())
            ->method('normalize')
            ->willReturn([
                'id' => 10,
                'name' => 'Mauro',
                'birth_date' => $birth_date->format("Y-m-d"),
                'kingdom' => 'My Kingdom',
            ]);
        $urlGenerator = $this
            ->createMock(UrlGeneratorInterface::class);

        $urlGenerator
            ->expects($this->exactly(2))
            ->method('generate')
            ->willReturnCallback(function (string $routeName, array $params) {
                if ($routeName == "an_equipment" && $params == ['id' => 9]) {
                    return '/equipment/9';
                }

                if ($routeName == "a_faction" && $params == ['id' => 7]) {

                    return '/faction/7';
                }
            });

        $characterNormalizer = new CharacterNormalizer(
            $baseNormalizer,
            $urlGenerator
        );

        $this->assertEquals([
            'id' => 10,
            'name' => 'Mauro',
            'birth_date' => $birth_date->format("Y-m-d"),
            'kingdom' => 'My Kingdom',
            'equipment' => '/equipment/9',
            'faction' => '/faction/7',
        ], $characterNormalizer->normalize($aCharacter));
    }
}
