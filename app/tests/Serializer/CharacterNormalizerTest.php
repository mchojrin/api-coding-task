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

class CharacterNormalizerTest extends TestCase
{
    private const EQUIPMENT_DETAIL_ROUTE = "equipments_detail";
    private const EQUIPMENT_DETAIL_URL_PREFIX = '/equipments/';
    private const ID_FIELD = 'id';
    private const FACTION_DETAIL_ROUTE = "factions_detail";
    private const FACTION_DETAIL_URL_PREFIX = '/factions/';

    /**
     * @param Character $aCharacter
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
                        "Mauro",
                        new DateTimeImmutable("1977-12-22"),
                        "Westeros",
                        new Equipment("Valyrian Steel Sword", "sword", "Valyrian forgers", 2),
                        new Faction("Night's Watch", "Guards of the northern wall", 3),
                        1
                    )
                ],
                [
                    new Character(
                        "Luke Skywalker",
                        new DateTimeImmutable("2097-10-12"),
                        "Tatooine",
                        new Equipment("Light Saber", "sword", "Obiwan Kenoby", 6),
                        new Faction("Jedis", "Peace keepers of the Galaxy", 7),
                        6
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
                if ($routeName == self::EQUIPMENT_DETAIL_ROUTE && $params == [self::ID_FIELD => $aCharacter->getEquipment()->getId()]) {
                    return self::EQUIPMENT_DETAIL_URL_PREFIX . $aCharacter->getEquipment()->getId();
                }

                if ($routeName == self::FACTION_DETAIL_ROUTE && $params == [self::ID_FIELD => $aCharacter->getFaction()->getId()]) {

                    return self::FACTION_DETAIL_URL_PREFIX . $aCharacter->getFaction()->getId();
                }

                return null;
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
            'equipment' => self::EQUIPMENT_DETAIL_URL_PREFIX . $aCharacter->getEquipment()->getId(),
            'faction' => self::FACTION_DETAIL_URL_PREFIX . $aCharacter->getFaction()->getId(),
        ];
    }
}
