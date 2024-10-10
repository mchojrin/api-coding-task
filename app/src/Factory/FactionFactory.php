<?php

namespace App\Factory;

use App\Entity\Faction;
use App\Repository\FactionRepository;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;

/**
 * @extends PersistentProxyObjectFactory<Faction>
 *
 * @method        Faction|Proxy                              create(array|callable $attributes = [])
 * @method static Faction|Proxy                              createOne(array $attributes = [])
 * @method static Faction|Proxy                              find(object|array|mixed $criteria)
 * @method static Faction|Proxy                              findOrCreate(array $attributes)
 * @method static Faction|Proxy                              first(string $sortedField = 'id')
 * @method static Faction|Proxy                              last(string $sortedField = 'id')
 * @method static Faction|Proxy                              random(array $attributes = [])
 * @method static Faction|Proxy                              randomOrCreate(array $attributes = [])
 * @method static FactionRepository|ProxyRepositoryDecorator repository()
 * @method static Faction[]|Proxy[]                          all()
 * @method static Faction[]|Proxy[]                          createMany(int $number, array|callable $attributes = [])
 * @method static Faction[]|Proxy[]                          createSequence(iterable|callable $sequence)
 * @method static Faction[]|Proxy[]                          findBy(array $attributes)
 * @method static Faction[]|Proxy[]                          randomRange(int $min, int $max, array $attributes = [])
 * @method static Faction[]|Proxy[]                          randomSet(int $number, array $attributes = [])
 */
final class FactionFactory extends PersistentProxyObjectFactory
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
        return Faction::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'description' => self::faker()->text(),
            'faction_name' => self::faker()->text(128),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Faction $faction): void {})
        ;
    }
}
