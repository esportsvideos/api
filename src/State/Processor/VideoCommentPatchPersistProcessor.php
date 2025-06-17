<?php

namespace App\State\Processor;

use ApiPlatform\Doctrine\Common\State\PersistProcessor;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\VideoCommentDto;
use App\Entity\VideoComment;
use App\Repository\VideoCommentRepository;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @implements ProcessorInterface<VideoCommentDto, VideoComment>
 */
readonly class VideoCommentPatchPersistProcessor implements ProcessorInterface
{
    /**
     * @param PersistProcessor $persistProcessor
     */
    public function __construct(
        private VideoCommentRepository $videoCommentRepository,
        #[Autowire(service: PersistProcessor::class)]
        private ProcessorInterface $persistProcessor,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): VideoComment
    {
        if (!$operation instanceof Patch) {
            throw new \RuntimeException('This processor only supports PATCH operations');
        }

        $videoComment = $this->videoCommentRepository->find($uriVariables['id']);

        if (!$videoComment instanceof VideoComment) {
            throw new NotFoundHttpException('VideoComment not found.');
        }

        $videoComment->comment = $data->comment;

        return $this->persistProcessor->process($videoComment, $operation, $uriVariables, $context);
    }
}
