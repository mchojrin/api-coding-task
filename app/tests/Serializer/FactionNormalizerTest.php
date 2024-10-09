<?php

declare(strict_types=1);

namespace App\Tests\Serializer;

use App\Entity\Character;
use App\Entity\Equipment;
use App\Entity\Faction;
use App\Serializer\FactionNormalizer;
use DateTimeImmutable;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class FactionNormalizerTest extends TestCase
{
    private const CHARACTER_DETAIL_ROUTE = "characters_detail";
    private const CHARACTER_DETAIL_URL_PREFIX = "/characters/detail/";
    private const ID_FIELD = 'id';
    private NormalizerInterface $baseNormalizer;
    private UrlGeneratorInterface $urlGenerator;
    private FactionNormalizer $factionNormalizer;

    protected function setUp(): void
    {
        $this->baseNormalizer = $this->createMock(NormalizerInterface::class);
        $this->urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $this->factionNormalizer = new FactionNormalizer($this->baseNormalizer, $this->urlGenerator);
    }

    /**
     * @param Faction $aFaction
     * @return void
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     * @test
     * @dataProvider factionProvider
     */
    public function shouldNormalizeAFaction(Faction $aFaction): void
    {
        $this->configureBaseNormalizer($this->baseNormalizer, $aFaction);
        $this->configureUrlGenerator($this->urlGenerator, $aFaction);

        $this->assertEquals($this->buildExpectedReturn($aFaction), $this->factionNormalizer->normalize($aFaction));
    }

    private function configureBaseNormalizer(MockObject $baseNormalizer, Faction $aFaction): void
    {
        $baseNormalizer
            ->expects(self::once())
            ->method('normalize')
            ->willReturn([
                'id' => $aFaction->getId(),
                'faction_name' => $aFaction->getFactionName(),
                'description' => $aFaction->getDescription(),
            ]);
    }

    private function configureUrlGenerator(MockObject $urlGenerator, Faction $aFaction): void
    {
        $urlGenerator
            ->expects($this->exactly($aFaction->getCharacters()->count()))
            ->method('generate')
            ->willReturnCallback(function (string $routeName, array $params) use ($aFaction) {
                if ($routeName == self::CHARACTER_DETAIL_ROUTE && array_key_exists(self::ID_FIELD, $params)) {
                    return self::CHARACTER_DETAIL_URL_PREFIX . $params[self::ID_FIELD];
                }

                return null;
            });
    }

    private function buildExpectedReturn(Faction $aFaction): array
    {
        return [
            'id' => $aFaction->getId(),
            'faction_name' => $aFaction->getFactionName(),
            'description' => $aFaction->getDescription(),
            'characters' => $this->buildExpectedCharacters($aFaction),
        ];
    }

    private function buildExpectedCharacters(Faction $aFaction): array
    {
        return array_map(
            fn(Character $character) => self::CHARACTER_DETAIL_URL_PREFIX . $character->getId()
            , $aFaction->getCharacters()->toArray()
        );
    }

    public static function factionProvider(): array
    {
        $emptyFaction = new Faction(
            "Empty faction",
            "A nice faction",
        );
        $nonEmptyFaction = new Faction(
            "With characters",
            "Another nice faction",
        );
        $nonEmptyFaction->addCharacter(new Character(
            name: "A new character",
            birth_date: new DateTimeImmutable(),
            kingdom: "A kingdom",
            equipment: new Equipment(
                "An equipment",
                "A very special type",
                "The creator"
            ),
            faction: $nonEmptyFaction,
            id: 98
        ));

        return
            [
                [
                    $emptyFaction
                ],
                [
                    $nonEmptyFaction
                ],
            ];
    }
}