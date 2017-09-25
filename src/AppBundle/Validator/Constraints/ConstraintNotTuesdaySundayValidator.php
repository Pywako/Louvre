<?php


namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ConstraintNotTuesdaySundayValidator extends ConstraintValidator
{
    const THUESDAY = 2;
    const SUNDAY = 7;


    /**
     * @param \DateTime $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        $day = $value->format('N');

        if (in_array($day,[self::THUESDAY,self::SUNDAY])) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', $value->format('d-m-Y'))
                ->addViolation();
        }
    }
}