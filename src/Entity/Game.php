<?php

/*
 * This file is part of the Esports Videos project.
 *
 * (c) Esports Videos <https://github.com/esportsvideos/api/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\DescriptionTrait;
use App\Entity\Traits\IdTrait;
use App\Entity\Traits\NameTrait;
use App\Entity\Traits\SluggableTrait;

/**
 * @author Maxime Cornet <xelysion@icloud.com>
 */
class Game
{
    use IdTrait, NameTrait, DescriptionTrait, SluggableTrait;
}
