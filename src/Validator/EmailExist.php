<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * Class EmailExist
 * @package App\Validator
 * @Annotation
 */
class EmailExist extends Constraint
{
    public string $message = "l'adresse email \"{{ email }}\" n'existe pas";
}
