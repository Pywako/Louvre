<?php

namespace AppBundle\Validator\Constraints;


use Symfony\Component\Validator\Constraint;

/**
 * Class ConstraintHalfDayBooking
 * @package AppBundle\Validator\Constraints
 * @Annotation
 */
class ConstraintHalfDayBooking extends Constraint
{
    public $message = "{{ string }} : la réservation pour l'après midi n'est plus disponible pour aujourd'hui";

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}