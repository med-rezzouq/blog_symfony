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

class CommentNameValidator extends ConstraintValidator
{
    private const PATTERN = '/^(\+212)\s((\d+){3})(\-(\d+){2}){3}$/';
    private SessionInterface $session;

    public function __construct(private readonly Security $security, private readonly RequestStack $request)
    {
        $this->session = $this->request->getSession();
    }

    public function validate(mixed $value, Constraint $constraint)
    {


        if (!$this->session->get('_security.last_username')) {
            if (!$constraint instanceof CommentName) {
                throw new UnexpectedTypeException($constraint, CommentName::class);
            }

            if (null === $value || '' === $value) {
                return;
            }

            if (!\is_string($value)) {
                throw new UnexpectedValueException($value, 'string');
            }

            if (strlen($value) < 3) {
                $this->context->buildViolation($constraint->message)
                    ->addViolation();
            }
        }
    }
}
