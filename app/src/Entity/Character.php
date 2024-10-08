<?php

namespace App\Entity;

use App\Repository\CharacterRepository;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CharacterRepository::class)]
#[ORM\Table(name: '`characters`')]
class Character
{
    public function __construct(int $id, string $name, DateTimeInterface $birth_date, string $kingdom, Equipment $equipment, Faction $faction)
    {
        $this->id = $id;
        $this->name = $name;
        $this->birth_date = $birth_date;
        $this->equipment = $equipment;
        $this->faction = $faction;
        $this->kingdom = $kingdom;
    }

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 128)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?DateTimeInterface $birth_date = null;

    #[ORM\Column(length: 128)]
    private ?string $kingdom = null;

    #[ORM\ManyToOne(inversedBy: 'characters')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Equipment $equipment = null;

    #[ORM\ManyToOne(inversedBy: 'characters')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Faction $faction = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getBirthDate(): ?DateTimeInterface
    {
        return $this->birth_date;
    }

    public function setBirthDate(DateTimeInterface $birth_date): static
    {
        $this->birth_date = $birth_date;

        return $this;
    }

    public function getKingdom(): ?string
    {
        return $this->kingdom;
    }

    public function setKingdom(string $kingdom): static
    {
        $this->kingdom = $kingdom;

        return $this;
    }

    public function getEquipment(): ?Equipment
    {
        return $this->equipment;
    }

    public function setEquipment(?Equipment $equipment): static
    {
        $this->equipment = $equipment;

        return $this;
    }

    public function getFaction(): ?Faction
    {
        return $this->faction;
    }

    public function setFaction(?Faction $faction): static
    {
        $this->faction = $faction;

        return $this;
    }
}
