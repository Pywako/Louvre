<?php


namespace AppBundle\Validator\Constraints;

use AppBundle\Entity\Booking;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ConstraintFullValidator extends ConstraintValidator
{
    private $requestStack;
    private $em;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $em)
    {
        $this->requestStack = $requestStack;
        $this->em = $em;
    }

    /**
     * @param object $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        $request = $this->requestStack->getCurrentRequest();
        $dateVisit = $value->getDateVisit();

        $dateVisitTickets = $this->em
            ->getRepository(Booking::class)
            ->findBy(
                array('dateVisit' => $dateVisit));
        //Récupérer le nombre total de billet pour la date en question en bdd
        $nbTicketBought = 0;
        foreach ($dateVisitTickets as $ticket) {
            $nbTicketBought += $ticket->getNbTicket();
        }
        $nbTotalTicket = $nbTicketBought + $value->getNbTicket();

        // si nb de billet à acheter + nb total > 1000 billets ->erreur
        if ($nbTotalTicket > 1000) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', $dateVisit->format('d-m-Y'))
                ->addViolation();
        }
    }
}