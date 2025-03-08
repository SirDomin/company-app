<?php

namespace App\Entity;

use App\Repository\EmployeeRepository;
use App\Validator\UniqueEmail;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EmployeeRepository::class)]
class Employee
{
    #[Groups(['employee:read', 'employee:write', 'employee:list', 'company:read'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['employee:read', 'employee:write', 'employee:list', 'company:read'])]
    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Length(max: 100)]
    private string $firstName;

    #[Groups(['employee:read', 'employee:write', 'employee:list', 'company:read'])]
    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Length(max: 100)]
    private string $lastName;

    #[Groups(['employee:read', 'employee:write', 'employee:list', 'company:read'])]
    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Email]
    #[UniqueEmail]
    #[Assert\Length(max: 255)]
    private string $email;

    #[Groups(['employee:read', 'employee:write', 'employee:list', 'company:read'])]
    #[ORM\Column(length: 20, nullable: true)]
    #[Assert\Length(max: 20)]
    private string $phoneNumber;

    #[Groups(['employee:read', 'employee:write'])]
    #[ORM\ManyToOne(inversedBy: 'employees')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    private ?Company $company = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): static
    {
        $this->company = $company;

        return $this;
    }
}
