<?php

namespace App\Serializer;

use App\Entity\Character;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CharacterNormalizer implements NormalizerInterface
{
    public function __construct(
        #[Autowire(service: 'serializer.normalizer.object')]
        private readonly NormalizerInterface $normalizer
    )
    {
    }

    public function normalize($character, ?string $format = null, array $context = []): array
    {
        $data = $this->normalizer->normalize($character, $format, array_merge($context, [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['faction', 'equipment']
        ]));

        $data['equipment'] = $character->getEquipment()->getName();
        $data['faction'] = $character->getFaction()->getFactionName();

        return $data;
    }

    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof Character;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Character::class => true,
        ];
    }
}