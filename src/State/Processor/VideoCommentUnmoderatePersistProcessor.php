<?php

namespace App\State\Processor;

use ApiPlatform\Doctrine\Common\State\PersistProcessor;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\VideoComment;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

/**
 * @implements ProcessorInterface<VideoComment, VideoComment>
 */
readonly class VideoCommentUnmoderatePersistProcessor implements ProcessorInterface
{
    /**
     * @param PersistProcessor $persistProcessor
     */
    public function __construct(
        #[Autowire(service: PersistProcessor::class)]
        private ProcessorInterface $persistProcessor,
    ) {
    }

    /**
     * @param VideoComment $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if (null === $data->moderatedAt) {
            throw new ConflictHttpException('Cannot unmoderate a comment that has not been moderated.');
        }

        $data->moderatedAt = null;
        $data->moderatedBy = null;

        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}
