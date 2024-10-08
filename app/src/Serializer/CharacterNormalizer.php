<?php

namespace App\Serializer;

use App\Entity\Character;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CharacterNormalizer implements NormalizerInterface
{
    public function __construct(
        #[Autowire(service: 'serializer.normalizer.object')]
        private readonly NormalizerInterface $normalizer,
        private readonly UrlGeneratorInterface $urlGenerator
    )
    {
    }

    public function normalize($object, ?string $format = null, array $context = []): array
    {
        $data = $this->normalizer->normalize($object, $format, array_merge($context, [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['faction', 'equipment']
        ]));

        $data['birthDate'] = $object->getBirthDate()->format('Y-m-d');
        $data['equipment'] = $this->urlGenerator->generate("an_equipment", [ 'id' => $object->getEquipment()->getId()]);
        $data['faction'] = $this->urlGenerator->generate("a_faction", [ 'id' => $object->getFaction()->getId()]);

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