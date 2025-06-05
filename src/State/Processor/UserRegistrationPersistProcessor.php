<?php

namespace App\State\Processor;

use ApiPlatform\Doctrine\Common\State\PersistProcessor;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\UserRegistrationDto;
use App\Email\WelcomeEmail;
use App\Entity\User;
use App\Service\Registration\GenerateSignedUriService;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @implements ProcessorInterface<UserRegistrationDto, User>
 */
readonly class UserRegistrationPersistProcessor implements ProcessorInterface
{
    /**
     * @param PersistProcessor $persistProcessor
     */
    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher,
        #[Autowire(service: PersistProcessor::class)]
        private ProcessorInterface $persistProcessor,
        private GenerateSignedUriService $generateSignedUriService,
        private MailerInterface $mailer,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): User
    {
        $user = new User();
        $user->setRoles(['ROLE_USER']);
        $user->setEmail($data->email);
        $user->setUsername($data->username);
        $user->setCountry($data->country);
        $user->setPassword($this->userPasswordHasher->hashPassword($user, $data->password));

        $persistedUser = $this->persistProcessor->process($user, $operation, $uriVariables, $context);

        $this->mailer->send(
            new WelcomeEmail(
                $persistedUser,
                $this->generateSignedUriService->generateSignedUri($persistedUser)
            )
        );

        return $persistedUser;
    }
}
