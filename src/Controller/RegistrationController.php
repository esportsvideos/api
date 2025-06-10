<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\UriSigner;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\GoneHttpException;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    #[Route('/users/{id}/verify/email', name: 'verify_email', methods: ['GET'])]
    public function index(Request $request, User $user, UriSigner $uriSigner, EntityManagerInterface $entityManager): JsonResponse
    {
        if ($user->isEmailVerified()) {
            throw new GoneHttpException();
        }

        if (!$uriSigner->checkRequest($request)) {
            throw new BadRequestHttpException();
        }

        $user->setEmailVerified(true);
        $entityManager->flush();

        return new JsonResponse(['msg' => 'account have been verified successfully.']);
    }
}
