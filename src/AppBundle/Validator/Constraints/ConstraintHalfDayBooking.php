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
    public $message = "{{ string }} : la réservation pour la journée n'est plus disponible à partir de 14h, 
    veuillez sélectionner le type demi-journée";

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
