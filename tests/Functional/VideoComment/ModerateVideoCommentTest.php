<?php

namespace App\Tests\Functional\VideoComment;

use App\Tests\Functional\ApiTestCase;
use DataFixtures\Data\UserFixtures;
use DataFixtures\Data\VideoCommentFixtures;
use DataFixtures\Data\VideoFixtures;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\ResponseInterface;

#[Group('video_comment_moderation')]
class ModerateVideoCommentTest extends ApiTestCase
{
    #[Test]
    public function asAnonymousICantSeeAModeratedComment(): void
    {
        $response = $this->requestComment(VideoCommentFixtures::MODERATED_COMMENT_ULID);

        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            'comment' => 'This comment has been moderated.',
        ]);
        self::assertStringNotContainsString('moderatedBy', $response->getContent());
    }

    #[Test]
    public function asUserICantSeeAModeratedComment(): void
    {
        $response = $this->requestComment(VideoCommentFixtures::MODERATED_COMMENT_ULID, UserFixtures::USER_ULID);

        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            'comment' => 'This comment has been moderated.',
        ]);
        self::assertStringNotContainsString('moderatedBy', $response->getContent());
    }

    #[Test]
    public function asAdminICanSeeAModeratedComment(): void
    {
        $this->requestComment(VideoCommentFixtures::MODERATED_COMMENT_ULID, UserFixtures::ADMIN_ULID);

        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            'comment' => 'This is a moderated comment.',
            'moderatedBy' => ['id' => UserFixtures::ADMIN_ULID],
        ]);
    }

    #[Test]
    public function asAnonymousICantModerateAComment(): void
    {
        $this->client
            ->request(
                'POST',
                sprintf(
                    '/videos/%s/video_comments/%s/moderate',
                    VideoFixtures::COMPLETE_WITH_SPECIFIC_COMMENTS_ULID,
                    VideoCommentFixtures::MODERATED_COMMENT_ULID
                )
            );

        self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    #[Test]
    public function asUserICantModerateAComment(): void
    {
        $this->authenticateTestClientAs(UserFixtures::USER_ULID)
            ->request(
                'POST',
                sprintf(
                    '/videos/%s/video_comments/%s/moderate',
                    VideoFixtures::COMPLETE_WITH_SPECIFIC_COMMENTS_ULID,
                    VideoCommentFixtures::MODERATED_COMMENT_ULID
                )
            );

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    #[Test]
    public function asAdminICantModerateACommentAlreadyModerated(): void
    {
        $this->authenticateTestClientAs(UserFixtures::ADMIN_ULID)
            ->request(
                'POST',
                sprintf(
                    '/videos/%s/video_comments/%s/moderate',
                    VideoFixtures::COMPLETE_WITH_SPECIFIC_COMMENTS_ULID,
                    VideoCommentFixtures::MODERATED_COMMENT_ULID
                )
            );

        self::assertResponseStatusCodeSame(Response::HTTP_CONFLICT);
    }

    #[Test]
    public function asAdminICanModerateAComment(): void
    {
        $this->authenticateTestClientAs(UserFixtures::ADMIN_ULID)
            ->request(
                'POST',
                sprintf(
                    '/videos/%s/video_comments/%s/moderate',
                    VideoFixtures::COMPLETE_WITH_SPECIFIC_COMMENTS_ULID,
                    VideoCommentFixtures::COMMENT_FROM_USER_ULID
                )
            );

        self::assertResponseIsSuccessful();

        $this->requestComment(VideoCommentFixtures::COMMENT_FROM_USER_ULID, UserFixtures::ADMIN_ULID);
        self::assertJsonContains([
            'comment' => 'This is a simple comment.',
            'moderatedBy' => ['id' => UserFixtures::ADMIN_ULID],
        ]);
    }

    #[Test]
    public function asAnonymousICantUnmoderateAComment(): void
    {
        $this->client
            ->request(
                'DELETE',
                sprintf(
                    '/videos/%s/video_comments/%s/moderate',
                    VideoFixtures::COMPLETE_WITH_SPECIFIC_COMMENTS_ULID,
                    VideoCommentFixtures::MODERATED_COMMENT_ULID
                )
            );

        self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    #[Test]
    public function asUserICantUnmoderateAComment(): void
    {
        $this->authenticateTestClientAs(UserFixtures::USER_ULID)
            ->request(
                'DELETE',
                sprintf(
                    '/videos/%s/video_comments/%s/moderate',
                    VideoFixtures::COMPLETE_WITH_SPECIFIC_COMMENTS_ULID,
                    VideoCommentFixtures::MODERATED_COMMENT_ULID
                )
            );

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    #[Test]
    public function asAdminICantUnmoderateACommentNotModerated(): void
    {
        $this->authenticateTestClientAs(UserFixtures::ADMIN_ULID)
            ->request(
                'DELETE',
                sprintf(
                    '/videos/%s/video_comments/%s/moderate',
                    VideoFixtures::COMPLETE_WITH_SPECIFIC_COMMENTS_ULID,
                    VideoCommentFixtures::COMMENT_FROM_USER_ULID
                )
            );

        self::assertResponseStatusCodeSame(Response::HTTP_CONFLICT);
        self::assertJsonContains(['detail' => 'Cannot unmoderate a comment that has not been moderated.']);
    }

    #[Test]
    public function asAdminICanUnmoderateACommentModerated(): void
    {
        $this->authenticateTestClientAs(UserFixtures::ADMIN_ULID)
            ->request(
                'DELETE',
                sprintf(
                    '/videos/%s/video_comments/%s/moderate',
                    VideoFixtures::COMPLETE_WITH_SPECIFIC_COMMENTS_ULID,
                    VideoCommentFixtures::MODERATED_COMMENT_ULID
                )
            );

        self::assertResponseIsSuccessful();

        $response = $this->requestComment(VideoCommentFixtures::MODERATED_COMMENT_ULID, UserFixtures::ADMIN_ULID);

        self::assertJsonContains([
            'moderatedBy' => null,
        ]);
    }

    private function requestComment(string $commentId, ?string $userUlid = null): ResponseInterface
    {
        $client = null !== $userUlid ? $this->authenticateTestClientAs($userUlid) : $this->client;

        return $client->request(
            'GET',
            sprintf(
                '/videos/%s/video_comments/%s',
                VideoFixtures::COMPLETE_WITH_SPECIFIC_COMMENTS_ULID,
                $commentId
            )
        );
    }
}
