<?php

namespace App\Tests\Functional\VideoComment;

use App\Tests\Functional\ApiTestCase;
use DataFixtures\Data\UserFixtures;
use DataFixtures\Data\VideoCommentFixtures;
use DataFixtures\Data\VideoFixtures;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Response;

#[Group('video_comment')]
#[Group('debug')]
class VideoCommentTest extends ApiTestCase
{
    #[DataProvider('getPublishCommentAccessCases')]
    #[Test]
    public function iCanOrCannotPublishACommentOnAVideo(?string $userUlid, int $expectedStatusCode): void
    {
        $client = null !== $userUlid
            ? $this->authenticateTestClientAs($userUlid)
            : $this->client;

        $response = $client->request('POST', sprintf('/videos/%s/video_comments', VideoFixtures::COMPLETE_ULID),
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'body' => json_encode(
                    [
                        'comment' => 'I love this video!',
                    ],
                    JSON_THROW_ON_ERROR
                ),
            ]
        );

        self::assertResponseStatusCodeSame($expectedStatusCode);

        if (Response::HTTP_CREATED === $expectedStatusCode) {
            $videoCommentId = $response->toArray()['id'];

            $this->authenticateTestClientAs(UserFixtures::USER_ULID)
                ->request(
                    'GET',
                    sprintf(
                        '/videos/%s/video_comments/%s',
                        VideoFixtures::COMPLETE_ULID,
                        $videoCommentId
                    ),
                );

            self::assertResponseIsSuccessful();
            self::assertJsonContains(['comment' => 'I love this video!']);
        }
    }

    /**
     * @return iterable<string, mixed>
     */
    public static function getPublishCommentAccessCases(): iterable
    {
        yield 'anonymous cannot post a comment' => [
            'userUlid' => null,
            'expectedStatusCode' => Response::HTTP_UNAUTHORIZED,
        ];

        yield 'user can post a comment' => [
            'userUlid' => UserFixtures::USER_ULID,
            'expectedStatusCode' => Response::HTTP_CREATED,
        ];

        yield 'admin can post a comment' => [
            'userUlid' => UserFixtures::ADMIN_ULID,
            'expectedStatusCode' => Response::HTTP_CREATED,
        ];
    }

    #[DataProvider('getEditCommentAccessCases')]
    #[Test]
    public function iCanOrCannotEditACommentOnAVideo(?string $userUlid, string $videoCommentUlid, string $newMessage, int $expectedStatusCode): void
    {
        $client = null !== $userUlid
            ? $this->authenticateTestClientAs($userUlid)
            : $this->client;

        $client->request(
            'PATCH',
            sprintf('/videos/%s/video_comments/%s', VideoFixtures::COMPLETE_WITH_SPECIFIC_COMMENTS_ULID, $videoCommentUlid),
            [
                'headers' => [
                    'Content-Type' => 'application/merge-patch+json',
                ],
                'body' => json_encode(
                    [
                        'comment' => $newMessage,
                    ],
                    JSON_THROW_ON_ERROR
                ),
            ]
        );

        self::assertResponseStatusCodeSame($expectedStatusCode);

        if (Response::HTTP_OK === $expectedStatusCode) {
            self::assertJsonContains(['comment' => $newMessage]);
        }
    }

    /**
     * @return iterable<string, mixed>
     */
    public static function getEditCommentAccessCases(): iterable
    {
        yield 'anonymous cannot edit a comment' => [
            'userUlid' => null,
            'videoCommentUlid' => VideoCommentFixtures::COMMENT_FROM_USER_ULID,
            'expectedStatusCode' => Response::HTTP_UNAUTHORIZED,
            'newMessage' => 'I cannot edit a comment because I am anonymous user.',
        ];

        yield 'user can edit his own comment' => [
            'userUlid' => UserFixtures::USER_ULID,
            'videoCommentUlid' => VideoCommentFixtures::COMMENT_FROM_USER_ULID,
            'expectedStatusCode' => Response::HTTP_OK,
            'newMessage' => 'I have edited my comment!',
        ];

        yield 'user cannot edit comment from another user' => [
            'userUlid' => UserFixtures::USER_ULID,
            'videoCommentUlid' => VideoCommentFixtures::COMMENT_FROM_ANOTHER_USER,
            'expectedStatusCode' => Response::HTTP_FORBIDDEN,
            'newMessage' => 'I cannot edit a comment because this is not my comment.',
        ];

        yield 'user cannot edit a moderated comment' => [
            'userUlid' => UserFixtures::USER_ULID,
            'videoCommentUlid' => VideoCommentFixtures::MODERATED_COMMENT_ULID,
            'expectedStatusCode' => Response::HTTP_FORBIDDEN,
            'newMessage' => 'I cannot edit a comment because this is comment is moderated.',
        ];

        yield 'admin can edit a comment' => [
            'userUlid' => UserFixtures::ADMIN_ULID,
            'videoCommentUlid' => VideoCommentFixtures::COMMENT_FROM_USER_ULID,
            'expectedStatusCode' => Response::HTTP_OK,
            'newMessage' => 'I have edited a comment that is not mine.',
        ];
    }

    #[DataProvider('getDeleteCommentAccessCases')]
    #[Test]
    public function iCanOrCannotDeleteACommentOnAVideo(?string $userUlid, string $videoCommentUlid, int $expectedStatusCode): void
    {
        $client = null !== $userUlid
            ? $this->authenticateTestClientAs($userUlid)
            : $this->client;

        $client->request(
            'DELETE',
            sprintf('/videos/%s/video_comments/%s', VideoFixtures::COMPLETE_WITH_SPECIFIC_COMMENTS_ULID, $videoCommentUlid),
        );

        self::assertResponseStatusCodeSame($expectedStatusCode);

        if (Response::HTTP_NO_CONTENT === $expectedStatusCode) {
            $this->authenticateTestClientAs(UserFixtures::USER_ULID)
                ->request(
                    'GET',
                    sprintf(
                        '/videos/%s/video_comments/%s',
                        VideoFixtures::COMPLETE_ULID,
                        VideoFixtures::COMPLETE_WITH_SPECIFIC_COMMENTS_ULID
                    ),
                );

            self::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @return iterable<string, mixed>
     */
    public static function getDeleteCommentAccessCases(): iterable
    {
        yield 'anonymous cannot delete a comment' => [
            'userUlid' => null,
            'videoCommentUlid' => VideoCommentFixtures::COMMENT_FROM_USER_ULID,
            'expectedStatusCode' => Response::HTTP_UNAUTHORIZED,
        ];

        yield 'user can edit his own comment' => [
            'userUlid' => UserFixtures::USER_ULID,
            'videoCommentUlid' => VideoCommentFixtures::COMMENT_FROM_USER_ULID,
            'expectedStatusCode' => Response::HTTP_NO_CONTENT,
        ];

        yield 'user cannot delete comment from another user' => [
            'userUlid' => UserFixtures::USER_ULID,
            'videoCommentUlid' => VideoCommentFixtures::COMMENT_FROM_ANOTHER_USER,
            'expectedStatusCode' => Response::HTTP_FORBIDDEN,
        ];

        yield 'admin can delete any comment' => [
            'userUlid' => UserFixtures::ADMIN_ULID,
            'videoCommentUlid' => VideoCommentFixtures::COMMENT_FROM_USER_ULID,
            'expectedStatusCode' => Response::HTTP_NO_CONTENT,
        ];
    }
}
