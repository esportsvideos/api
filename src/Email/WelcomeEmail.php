<?php

namespace App\Email;

use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;

class WelcomeEmail extends TemplatedEmail
{
    public function __construct(User $user, string $signedUri)
    {
        parent::__construct();

        $this
            ->to(new Address($user->getEmail()))
            ->subject('Welcome to Esports Videos !')
            ->htmlTemplate('emails/signup.html.twig')
            ->textTemplate('emails/signup.txt.twig')
            ->context([
                'activationLink' => $signedUri,
            ])
        ;
    }
}
