<?php


namespace AppBundle\Validator\Constraints;

use AppBundle\Manager\HolidayManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ConstraintHolidayValidator extends ConstraintValidator
{
    private $holiday;

    public function __construct(HolidayManager $holiday)
    {
        $this->holiday = $holiday;
    }

    /**
     * @param \DateTime $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        $year = $value->format('Y');
        $holidays = $this->holiday->getHolidays($year);
        $visitDay = $value->getTimestamp();

        if (in_array($visitDay, $holidays)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', $value->format('d-m-Y'))
                ->addViolation();
        }
    }
}
