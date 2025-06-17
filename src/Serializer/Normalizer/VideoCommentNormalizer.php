<?php

namespace App\Serializer\Normalizer;

use App\Entity\VideoComment;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class VideoCommentNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    public function __construct(private readonly Security $security)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function normalize(mixed $data, ?string $format = null, array $context = []): array
    {
        /** @var array<string, mixed> $normalizedData */
        $normalizedData = $this->normalizer->normalize($data, $format, [self::class => true] + $context);

        if (null !== $data->moderatedAt && isset($normalizedData['comment']) && !$this->security->isGranted('ROLE_ADMIN')) {
            $normalizedData['comment'] = 'This comment has been moderated.';
        }

        return $normalizedData;
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof VideoComment && !isset($context[self::class]);
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            VideoComment::class => false,
        ];
    }
}
