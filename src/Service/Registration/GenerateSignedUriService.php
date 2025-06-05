<?php

namespace App\Service\Registration;

use App\Entity\User;
use Symfony\Component\HttpFoundation\UriSigner;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final readonly class GenerateSignedUriService
{
    public function __construct(private UriSigner $uriSigner, private UrlGeneratorInterface $urlGenerator)
    {
    }

    public function generateSignedUri(User $user): string
    {
        return $this->uriSigner->sign(
            $this->urlGenerator->generate(
                'verify_email',
                ['id' => $user->getId()],
                UrlGeneratorInterface::ABSOLUTE_URL
            ),
            new \DateTimeImmutable('+1 day')
        );
    }
}
