<?php

namespace AppBundle\Validator\Constraints;


use Symfony\Component\Validator\Constraint;

/**
 * Class ConstraintNotTuesdaySunday
 * @package AppBundle\Validator\Constraints
 * @Annotation
 */
class ConstraintNotTuesdaySunday extends Constraint
{
    public $message = "{{ string }} : La date de visite ne peut pas être un mardi ou dimanche";
    
}
