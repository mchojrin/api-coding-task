<?php

namespace App\Entity;

use App\Repository\EquipmentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EquipmentRepository::class)]
#[ORM\Table(name: '`equipments`')]
class Equipment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 128)]
    private ?string $name = null;

    #[ORM\Column(length: 128)]
    private ?string $type = null;

    #[ORM\Column(length: 128)]
    private ?string $made_by = null;

    #[ORM\OneToMany(targetEntity: Character::class, mappedBy: 'equipment')]
    private Collection $characters;

    public function __construct(string $name, string $type, string $made_by, ?int $id = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->type = $type;
        $this->made_by = $made_by;
        $this->characters = new ArrayCollection();
    }

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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getMadeBy(): ?string
    {
        return $this->made_by;
    }

    public function setMadeBy(string $made_by): static
    {
        $this->made_by = $made_by;

        return $this;
    }

    /**
     * @return Collection<int, Character>
     */
    public function getCharacters(): Collection
    {
        return $this->characters;
    }

    public function addCharacter(Character $character): static
    {
        if (!$this->characters->contains($character)) {
            $this->characters->add($character);
            $character->setEquipment($this);
        }

        return $this;
    }

    public function removeCharacter(Character $character): static
    {
        if ($this->characters->removeElement($character)) {
            // set the owning side to null (unless already changed)
            if ($character->getEquipment() === $this) {
                $character->setEquipment(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->getName() ?? '';
    }
}
