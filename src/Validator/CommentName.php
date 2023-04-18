<?php

declare(strict_types=1);

namespace App\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
/**
 * @Annotation
 */
class CommentName extends Constraint
{
    public string $message = 'Write a valid Name';
}
