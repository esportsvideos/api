<?php

namespace App\Dto;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[UniqueEntity(fields: ['email'], entityClass: User::class, ignoreNull: true)]
#[UniqueEntity(fields: ['username'], entityClass: User::class, ignoreNull: true)]
class UserRegistrationDto
{
    #[Assert\Email]
    #[Assert\NotBlank(allowNull: false)]
    #[Groups('user:write')]
    public mixed $email = null;

    #[Assert\NotBlank(allowNull: false)]
    #[Groups('user:write')]
    public mixed $username = null;

    #[Assert\NotBlank(allowNull: false)]
    #[Assert\PasswordStrength(
        minScore: Assert\PasswordStrength::STRENGTH_WEAK,
    )]
    #[Groups('user:write')]
    public mixed $password = null;

    #[Assert\Country()]
    #[Groups('user:write')]
    public mixed $country = null;
}
