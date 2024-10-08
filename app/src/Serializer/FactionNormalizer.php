<?php

namespace App\Serializer;

use App\Entity\Character;
use App\Entity\Faction;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class FactionNormalizer implements NormalizerInterface
{
    public function __construct(
        #[Autowire(service: 'serializer.normalizer.object')]
        private readonly NormalizerInterface $normalizer
    )
    {
    }

    public function normalize($faction, ?string $format = null, array $context = []): array
    {
        $data = $this->normalizer->normalize($faction, $format, array_merge($context, [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['characters']
        ]));

        $data['characters'] = array_map(fn (Character $character) => $character->getName(), $faction->getCharacters()->toArray());

        return $data;
    }

    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof Faction;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Character::class => true,
        ];
    }
}