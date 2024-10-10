<?php

namespace App\Serializer;

use App\Entity\Character;
use App\Entity\Faction;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

const CHARACTERS_DETAIL_ROUTE = "characters_detail";
class FactionNormalizer implements NormalizerInterface
{
    public function __construct(
        #[Autowire(service: 'serializer.normalizer.object')]
        private readonly NormalizerInterface $normalizer,
        private readonly UrlGeneratorInterface $urlGenerator,
    )
    {
    }

    public function normalize($faction, ?string $format = null, array $context = []): array
    {
        $data = $this->normalizer->normalize($faction, $format, array_merge($context, [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['characters']
        ]));

        $data['faction_name'] = $faction->getFactionName();
        unset($data['factionName']);
        $data['characters'] = array_map(
            fn (Character $character) =>
            $this->urlGenerator->generate(
                CHARACTERS_DETAIL_ROUTE, ['id' => $character->getId()]
            ),
            $faction->getCharacters()->toArray()
        );

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