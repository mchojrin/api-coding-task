<?php

namespace App\Tests\Serializer;

use App\Entity\Character;
use App\Entity\Equipment;
use App\Entity\Faction;
use App\Serializer\CharacterNormalizer;
use DateTimeImmutable;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

const EQUIPMENT_DETAIL_ROUTE = "an_equipment";
const ID_FIELD = 'id';
const EQUIPMENT_DETAIL_URL_PREFIX = '/equipment/';
const FACTION_DETAIL_ROUTE = "a_faction";
const FACTION_DETAIL_URL_PREFIX = '/faction/';
class CharacterNormalizerTest extends TestCase
{
    /**
     * @param $aCharacter
     * @throws ExceptionInterface
     * @test
     * @dataProvider characterProvider
     */
    public function shouldNormalizeACharacter(Character $aCharacter): void
    {
        $baseNormalizer = $this->createMock(NormalizerInterface::class);
        $this->configureBaseNormalizer($baseNormalizer, $aCharacter);

        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $this->configureUrlGenerator($urlGenerator, $aCharacter);

        $characterNormalizer = new CharacterNormalizer($baseNormalizer,$urlGenerator);

        $this->assertEquals($this->buildExpectedReturn($aCharacter), $characterNormalizer->normalize($aCharacter));
    }

    public static function characterProvider(): array
    {
        return
            [
                [
                    new Character(
                        1,
                        "Mauro",
                        new DateTimeImmutable("1977-12-22"),
                        "Westeros",
                        new Equipment(2, "Valyrian Steel Sword", "sword", "Valyrian forgers"),
                        new Faction(3, "Night's Watch", "Guards of the northern wall")
                    )
                ],
                [
                    new Character(
                        6,
                        "Luke Skywalker",
                        new DateTimeImmutable("2097-10-12"),
                        "Tatooine",
                        new Equipment(6, "Light Saber", "sword", "Obiwan Kenoby"),
                        new Faction(7, "Jedis", "Peace keepers of the Galaxy")
                    )
                ],
            ];
    }

    /**
     * @param MockObject $baseNormalizer
     * @param Character $aCharacter
     * @return void
     */
    protected function configureBaseNormalizer(MockObject $baseNormalizer, Character $aCharacter): void
    {
        $baseNormalizer
            ->expects(self::once())
            ->method('normalize')
            ->willReturn([
                'id' => $aCharacter->getId(),
                'name' => $aCharacter->getName(),
                'birth_date' => $aCharacter->getBirthDate()->format('Y-m-d'),
                'kingdom' => $aCharacter->getKingdom(),
            ]);
    }

    /**
     * @param MockObject $urlGenerator
     * @param Character $aCharacter
     * @return void
     */
    protected function configureUrlGenerator(MockObject $urlGenerator, Character $aCharacter): void
    {
        $urlGenerator
            ->expects($this->exactly(2))
            ->method('generate')
            ->willReturnCallback(function (string $routeName, array $params) use ($aCharacter) {
                if ($routeName == EQUIPMENT_DETAIL_ROUTE && $params == [ID_FIELD => $aCharacter->getEquipment()->getId()]) {
                    return EQUIPMENT_DETAIL_URL_PREFIX . $aCharacter->getEquipment()->getId();
                }

                if ($routeName == FACTION_DETAIL_ROUTE && $params == [ID_FIELD => $aCharacter->getFaction()->getId()]) {

                    return FACTION_DETAIL_URL_PREFIX . $aCharacter->getFaction()->getId();
                }
            });
    }

    /**
     * @param Character $aCharacter
     * @return array
     */
    protected function buildExpectedReturn(Character $aCharacter): array
    {
        return [
            'id' => $aCharacter->getId(),
            'name' => $aCharacter->getName(),
            'birth_date' => $aCharacter->getBirthDate()->format("Y-m-d"),
            'kingdom' => $aCharacter->getKingdom(),
            'equipment' => EQUIPMENT_DETAIL_URL_PREFIX . $aCharacter->getEquipment()->getId(),
            'faction' => FACTION_DETAIL_URL_PREFIX . $aCharacter->getFaction()->getId(),
        ];
    }
}
