<?php

namespace App\State\Processor;

use ApiPlatform\Doctrine\Common\State\PersistProcessor;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\VideoCommentDto;
use App\Entity\User;
use App\Entity\Video;
use App\Entity\VideoComment;
use App\Repository\VideoRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @implements ProcessorInterface<VideoCommentDto, VideoComment>
 */
readonly class VideoCommentPostPersistProcessor implements ProcessorInterface
{
    /**
     * @param PersistProcessor $persistProcessor
     */
    public function __construct(
        private Security $security,
        private VideoRepository $videoRepository,
        #[Autowire(service: PersistProcessor::class)]
        private ProcessorInterface $persistProcessor,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): VideoComment
    {
        if (!$operation instanceof Post) {
            throw new \RuntimeException('This processor only supports POST operations');
        }

        /** @var User $user */
        $user = $this->security->getUser();
        $video = $this->videoRepository->find($uriVariables['videoId']);

        if (!$video instanceof Video) {
            throw new NotFoundHttpException('Video not found.');
        }

        $videoComment = new VideoComment();

        $videoComment->user = $user;
        $videoComment->video = $video;
        $videoComment->comment = $data->comment;

        return $this->persistProcessor->process($videoComment, $operation, $uriVariables, $context);
    }
}
