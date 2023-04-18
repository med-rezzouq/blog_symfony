<?php

declare(strict_types=1);

namespace App\Validator\Constraint;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class CommentEmailValidator extends ConstraintValidator
{
    private SessionInterface $session;
    private const PATTERN = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';

    public function __construct(private readonly Security $security, private readonly RequestStack $request)
    {
        $this->session = $this->request->getSession();
    }


    public function validate(mixed $value, Constraint $constraint)
    {

        if (!$this->session->get('_security.last_username')) {

            if (!$constraint instanceof CommentEmail) {
                throw new UnexpectedTypeException($constraint, CommentEmail::class);
            }

            if (null === $value || '' === $value) {
                return;
            }

            if (!\is_string($value)) {
                throw new UnexpectedValueException($value, 'string');
            }

            if (\preg_match(self::PATTERN, $value) === 0) {
                $this->context->buildViolation($constraint->message)
                    ->addViolation();
            }
        }
    }
}
