<?php

namespace App\Serializer;

use App\Entity\Character;
use App\Entity\Equipment;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class EquipmentNormalizer implements NormalizerInterface
{
    public function __construct(
        #[Autowire(service: 'serializer.normalizer.object')]
        private readonly NormalizerInterface $normalizer,
        private readonly UrlGeneratorInterface $urlGenerator,
    )
    {
    }

    public function normalize($equipment, ?string $format = null, array $context = []): array
    {
        $data = $this->normalizer->normalize($equipment, $format, array_merge($context, [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['characters']
        ]));

        $data['characters'] = array_map(
            fn (Character $character) =>
            $this->urlGenerator->generate(
                "a_character", ['id' => $character->getId()]
            ),
            $equipment->getCharacters()->toArray()
        );

        return $data;
    }

    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof Equipment;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Character::class => true,
        ];
    }
}