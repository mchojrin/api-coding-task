<?php

declare(strict_types=1);

namespace App\Tests\Serializer;

use App\Entity\Character;
use App\Entity\Equipment;
use App\Entity\Faction;
use App\Serializer\EquipmentNormalizer;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class EquipmentNormalizerTest extends TestCase
{
    private const CHARACTER_DETAIL_ROUTE = "characters_detail";
    private const CHARACTER_DETAIL_URL_PREFIX = "/characters/detail/";
    private const ID_FIELD = 'id';
    private NormalizerInterface $baseNormalizer;
    private UrlGeneratorInterface $urlGenerator;
    private EquipmentNormalizer $equipmentNormalizer;

    protected function setUp(): void
    {
        $this->baseNormalizer = $this->createMock(NormalizerInterface::class);
        $this->urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $this->equipmentNormalizer = new EquipmentNormalizer($this->baseNormalizer, $this->urlGenerator);
    }

    /**
     * @throws ExceptionInterface
     */
    #[Test]
    #[DataProvider(methodName: "equipmentProvider")]
    public function shouldNormalizeAnEquipment(Equipment $anEquipment): void
    {
        $this->configureBaseNormalizer($this->baseNormalizer, $anEquipment);
        $this->configureUrlGenerator($this->urlGenerator, $anEquipment);

        $this->assertEquals($this->buildExpectedReturn($anEquipment), $this->equipmentNormalizer->normalize($anEquipment));
    }

    private function configureBaseNormalizer(MockObject $baseNormalizer, Equipment $anEquipment): void
    {
        $baseNormalizer
            ->expects(self::once())
            ->method('normalize')
            ->willReturn([
                'id' => $anEquipment->getId(),
                'name' => $anEquipment->getName(),
                'type' => $anEquipment->getType(),
                'made_by' => $anEquipment->getMadeBy(),
            ]);
    }

    private function configureUrlGenerator(MockObject $urlGenerator, Equipment $anEquipment): void
    {
        $urlGenerator
            ->expects($this->exactly($anEquipment->getCharacters()->count()))
            ->method('generate')
            ->willReturnCallback(function (string $routeName, array $params) {
                if ($routeName == self::CHARACTER_DETAIL_ROUTE && array_key_exists(self::ID_FIELD, $params)) {
                    return self::CHARACTER_DETAIL_URL_PREFIX . $params[self::ID_FIELD];
                }

                return null;
            });
    }

    private function buildExpectedReturn(Equipment $anEquipment): array
    {
        return [
            'id' => $anEquipment->getId(),
            'name' => $anEquipment->getName(),
            'type' => $anEquipment->getType(),
            'made_by' => $anEquipment->getMadeBy(),
            'characters' => $this->buildExpectedCharacters($anEquipment),
        ];
    }

    private function buildExpectedCharacters(Equipment $aEquipment): array
    {
        return array_map(
            fn(Character $character) => self::CHARACTER_DETAIL_URL_PREFIX . $character->getId()
            , $aEquipment->getCharacters()->toArray()
        );
    }

    public static function equipmentProvider(): array
    {
        $emptyEquipment = new Equipment(
            "Empty equipment",
            "A nice equipment type",
            "The first maker"
        );
        $nonEmptyEquipment = new Equipment(
            "Non empty equipment",
            "Fine equipment type",
            "The second maker"
        );
        $nonEmptyEquipment->addCharacter(new Character(
            name: "A new character",
            birth_date: new DateTimeImmutable(),
            kingdom: "A kingdom",
            equipment: $nonEmptyEquipment,
            faction: new Faction(
                "A faction",
                "A nice faction"
            ),
            id: 98
        ));

        return
            [
                [
                    $emptyEquipment
                ],
                [
                    $nonEmptyEquipment
                ],
            ];
    }
}