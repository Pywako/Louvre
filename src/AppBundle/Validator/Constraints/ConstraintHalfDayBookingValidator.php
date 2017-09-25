<?php


namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use AppBundle\Entity\Booking;

class ConstraintHalfDayBookingValidator extends ConstraintValidator
{

    /**
     * @param object $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        $type = $value->getType();
        $dateVisit = $value->getDateVisit();
        $today = new \dateTime();
        $hours = $today->format('G');

        if($type == Booking::TYPE_DAY)
        {
            if($dateVisit->format('d-m-Y') == $today->format('d-m-Y'))
            {
                if ($hours >= 14) {
                    $this->context->buildViolation($constraint->message)
                        ->setParameter('{{ string }}', $dateVisit->format('d-m-Y'))
                        ->addViolation();
                }
            }
        }

    }
}