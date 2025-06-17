<?php

namespace App\Dto;

use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class VideoCommentDto
{
    #[Assert\NotBlank(allowNull: false)]
    #[Groups('video_comment:write')]
    public mixed $comment = null;
}
