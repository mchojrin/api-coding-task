<?php

namespace App\Factory;

use App\Entity\Character;
use App\Repository\CharacterRepository;
use DateTimeImmutable;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;

/**
 * @extends PersistentProxyObjectFactory<Character>
 *
 * @method        Character|Proxy                              create(array|callable $attributes = [])
 * @method static Character|Proxy                              createOne(array $attributes = [])
 * @method static Character|Proxy                              find(object|array|mixed $criteria)
 * @method static Character|Proxy                              findOrCreate(array $attributes)
 * @method static Character|Proxy                              first(string $sortedField = 'id')
 * @method static Character|Proxy                              last(string $sortedField = 'id')
 * @method static Character|Proxy                              random(array $attributes = [])
 * @method static Character|Proxy                              randomOrCreate(array $attributes = [])
 * @method static CharacterRepository|ProxyRepositoryDecorator repository()
 * @method static Character[]|Proxy[]                          all()
 * @method static Character[]|Proxy[]                          createMany(int $number, array|callable $attributes = [])
 * @method static Character[]|Proxy[]                          createSequence(iterable|callable $sequence)
 * @method static Character[]|Proxy[]                          findBy(array $attributes)
 * @method static Character[]|Proxy[]                          randomRange(int $min, int $max, array $attributes = [])
 * @method static Character[]|Proxy[]                          randomSet(int $number, array $attributes = [])
 */
final class CharacterFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
    }

    public static function class(): string
    {
        return Character::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'birth_date' => DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'equipment' => EquipmentFactory::new(),
            'faction' => FactionFactory::new(),
            'kingdom' => self::faker()->text(128),
            'name' => self::faker()->text(128),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Character $character): void {})
        ;
    }
}
