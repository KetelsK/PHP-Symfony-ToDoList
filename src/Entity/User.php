<?php

namespace App\Entity;

use App\Entity\ToDoItem;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;



/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(
 * fields={"username"},
 * message="User already exists"
 * )
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min="6",minMessage="Your password must be at least 6 characters")
     */
    private $password;

    /**
     * @Assert\EqualTo(propertyPath="password", message="Your password should be the same")
     */
    public $confirm_password;

    /**
     * @ORM\OneToMany(targetEntity=ToDoItem::class, mappedBy="createdby", orphanRemoval=true)
     */
    private $toDoItems;

    public function __construct()
    {
        $this->toDoItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return Collection|ToDoItem[]
     */
    public function getToDoItems(): Collection
    {
        return $this->toDoItems;
    }

    public function addToDoItem(ToDoItem $toDoItem): self
    {
        if (!$this->toDoItems->contains($toDoItem)) {
            $this->toDoItems[] = $toDoItem;
            $toDoItem->setCreatedby($this);
        }

        return $this;
    }

    public function removeToDoItem(ToDoItem $toDoItem): self
    {
        if ($this->toDoItems->contains($toDoItem)) {
            $this->toDoItems->removeElement($toDoItem);
            // set the owning side to null (unless already changed)
            if ($toDoItem->getCreatedby() === $this) {
                $toDoItem->setCreatedby(null);
            }
        }

        return $this;
    }

    public function getRoles()
    {
        return ['ROLE_USER'];
    }
    public function getSalt()
    {
    }

    public function eraseCredentials()
    {
    }
}
