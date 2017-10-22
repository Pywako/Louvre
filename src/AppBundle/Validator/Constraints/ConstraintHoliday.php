<?php

namespace AppBundle\Validator\Constraints;


use Symfony\Component\Validator\Constraint;

/**
 * Class ConstraintHoliday
 * @package AppBundle\Validator\Constraints
 * @Annotation
 */
class ConstraintHoliday extends Constraint
{
    public $message = "{{ string }} : Jour férier, veuillez choisir une autre date";
    
}
