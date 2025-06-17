<?php

namespace App\State\Processor;

use ApiPlatform\Doctrine\Common\State\PersistProcessor;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use App\Entity\VideoComment;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

/**
 * @implements ProcessorInterface<VideoComment, VideoComment>
 */
readonly class VideoCommentModeratePersistProcessor implements ProcessorInterface
{
    /**
     * @param PersistProcessor $persistProcessor
     */
    public function __construct(
        private Security $security,
        #[Autowire(service: PersistProcessor::class)]
        private ProcessorInterface $persistProcessor,
    ) {
    }

    /**
     * @param VideoComment $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $currentUser = $this->security->getUser();

        if (!$currentUser instanceof User) {
            throw new \LogicException('No authenticated user found.');
        }

        if (null !== $data->moderatedAt) {
            throw new ConflictHttpException('Comment already moderated.');
        }

        $data->moderatedAt = new \DateTimeImmutable();
        $data->moderatedBy = $currentUser;

        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}
