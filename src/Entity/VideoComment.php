<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Dto\VideoCommentDto;
use App\Entity\Traits\EntityIdTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\VideoCommentRepository;
use App\State\Processor\VideoCommentModeratePersistProcessor;
use App\State\Processor\VideoCommentPatchPersistProcessor;
use App\State\Processor\VideoCommentPostPersistProcessor;
use App\State\Processor\VideoCommentUnmoderatePersistProcessor;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/videos/{videoId}/video_comments',
            uriVariables: [
                'videoId' => new Link(toProperty: 'video', fromClass: Video::class),
            ]
        ),
        new Get(
            uriTemplate: '/videos/{videoId}/video_comments/{id}',
            uriVariables: [
                'videoId' => new Link(toProperty: 'video', fromClass: Video::class),
                'id' => new Link(fromClass: self::class),
            ],
        ),
        new Post(
            uriTemplate: '/videos/{videoId}/video_comments',
            uriVariables: [
                'videoId' => new Link(toProperty: 'video', fromClass: Video::class),
            ],
            denormalizationContext: [
                'groups' => ['video_comment:write'],
                'allow_extra_attributes' => false,
            ],
            security: "is_granted('ROLE_USER')",
            input: VideoCommentDto::class,
            read: false,
            processor: VideoCommentPostPersistProcessor::class,
        ),
        new Patch(
            uriTemplate: '/videos/{videoId}/video_comments/{id}',
            uriVariables: [
                'videoId' => new Link(toProperty: 'video', fromClass: Video::class),
                'id' => new Link(fromClass: self::class),
            ],
            denormalizationContext: [
                'groups' => ['video_comment:write'],
                'allow_extra_attributes' => false,
            ],
            security: "is_granted('ROLE_ADMIN') or (object.user == user and object.moderatedAt == null)",
            input: VideoCommentDto::class,
            processor: VideoCommentPatchPersistProcessor::class,
        ),
        new Delete(
            uriTemplate: '/videos/{videoId}/video_comments/{id}',
            uriVariables: [
                'videoId' => new Link(toProperty: 'video', fromClass: Video::class),
                'id' => new Link(fromClass: self::class),
            ],
            security: "is_granted('ROLE_ADMIN') or (object.user == user and object.moderatedAt == null)",
            input: false,
        ),
        new Post(
            uriTemplate: '/videos/{videoId}/video_comments/{id}/moderate',
            uriVariables: [
                'videoId' => new Link(toProperty: 'video', fromClass: Video::class),
                'id' => new Link(fromClass: self::class),
            ],
            status: 200,
            security: "is_granted('ROLE_ADMIN')",
            input: false,
            processor: VideoCommentModeratePersistProcessor::class,
        ),
        new Delete(
            uriTemplate: '/videos/{videoId}/video_comments/{id}/moderate',
            uriVariables: [
                'videoId' => new Link(toProperty: 'video', fromClass: Video::class),
                'id' => new Link(fromClass: self::class),
            ],
            security: "is_granted('ROLE_ADMIN')",
            input: false,
            processor: VideoCommentUnmoderatePersistProcessor::class,
        ),
    ],
    normalizationContext: [
        'groups' => ['video_comment:read'],
        'enable_max_depth' => true,
    ],
    order: ['createdAt' => 'DESC']
)]
#[ORM\Entity(repositoryClass: VideoCommentRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(name: '`video_comment`')]
class VideoComment
{
    use EntityIdTrait;
    use TimestampableTrait;

    #[ORM\ManyToOne(targetEntity: Video::class, cascade: ['persist'], inversedBy: 'videoComments')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    public Video $video;

    #[Groups(['video_comment:read'])]
    #[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist'], inversedBy: 'videoComments')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    public User $user;

    #[Groups(['video_comment:read'])]
    #[ORM\Column(type: Types::TEXT)]
    public string $comment;

    #[Groups(['video_comment:read'])]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['default' => 'CURRENT_TIMESTAMP'])]
    public ?\DateTimeImmutable $moderatedAt;

    #[Groups(['admin:video_comment:read'])]
    #[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist'], inversedBy: 'moderatedVideoComments')]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    public ?User $moderatedBy;
}
