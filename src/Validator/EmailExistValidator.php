<?php

declare(strict_types=1);

namespace App\Validator;

use App\Repository\UserRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class EmailExistValidator extends ConstraintValidator
{

    private UserRepository $userRepository;

    /**
     * EmailExistValidator constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }


    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof EmailExist) {
            throw new UnexpectedTypeException($constraint, EmailExist::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (null === $this->userRepository->findOneBy(["email" => $value])) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ email }}', $value)
                ->addViolation();
        }
    }
}
