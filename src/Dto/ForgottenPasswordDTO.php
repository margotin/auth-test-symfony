<?php

declare(strict_types=1);

namespace App\Dto;

use App\Validator\EmailExist;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @Assert\GroupSequence({"ForgottenPasswordDTO", "nextValidation"})
 */
class ForgottenPasswordDTO
{
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Email()
     * @EmailExist(groups={"nextValidation"})
     */
    private string $email;

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }
}
