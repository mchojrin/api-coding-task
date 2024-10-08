<?php

namespace App\Factory;

use App\Entity\Equipment;
use App\Repository\EquipmentRepository;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;

/**
 * @extends PersistentProxyObjectFactory<Equipment>
 *
 * @method        Equipment|Proxy                              create(array|callable $attributes = [])
 * @method static Equipment|Proxy                              createOne(array $attributes = [])
 * @method static Equipment|Proxy                              find(object|array|mixed $criteria)
 * @method static Equipment|Proxy                              findOrCreate(array $attributes)
 * @method static Equipment|Proxy                              first(string $sortedField = 'id')
 * @method static Equipment|Proxy                              last(string $sortedField = 'id')
 * @method static Equipment|Proxy                              random(array $attributes = [])
 * @method static Equipment|Proxy                              randomOrCreate(array $attributes = [])
 * @method static EquipmentRepository|ProxyRepositoryDecorator repository()
 * @method static Equipment[]|Proxy[]                          all()
 * @method static Equipment[]|Proxy[]                          createMany(int $number, array|callable $attributes = [])
 * @method static Equipment[]|Proxy[]                          createSequence(iterable|callable $sequence)
 * @method static Equipment[]|Proxy[]                          findBy(array $attributes)
 * @method static Equipment[]|Proxy[]                          randomRange(int $min, int $max, array $attributes = [])
 * @method static Equipment[]|Proxy[]                          randomSet(int $number, array $attributes = [])
 */
final class EquipmentFactory extends PersistentProxyObjectFactory
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
        return Equipment::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'made_by' => self::faker()->text(128),
            'name' => self::faker()->text(128),
            'type' => self::faker()->text(128),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Equipment $equipment): void {})
        ;
    }
}
