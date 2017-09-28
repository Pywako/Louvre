<?php

namespace AppBundle\Validator\Constraints;


use Symfony\Component\Validator\Constraint;

/**
 * Class ConstraintFull
 * @package AppBundle\Validator\Constraints
 * @Annotation
 */
class ConstraintFull extends Constraint
{
    public $message = "{{ string }} : Il n'y a plus de billets disponibles pour la date choisie";

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}