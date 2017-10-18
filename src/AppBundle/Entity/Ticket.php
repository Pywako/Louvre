<?php

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Ticket
 *
 * @ORM\Table(name="ticket")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TicketRepository")
 */
class Ticket
{
    const TARIF_BABY        = 0.00;
    const TARIF_CHILD       = 8.00;
    const TARIF_DISCOUNT    = 10.00;
    const TARIF_SENIOR      = 12.00;
    const TARIF_STANDARD    = 16.00;
    const COEFICIENT_HALF_DAY = 0.5;
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @Assert\Type(
     *     type = "string",
     *     message = "nom.not.correct"
     *     )
     * @Assert\NotNull(groups={"step2"}, message="nom.not.null")
     * @ORM\Column(name="nom", type="string", length=255)
     */
    private $nom;

    /**
     * @var string
     * @Assert\Type(
     *     type = "string",
     *     message = "prenom.not.correct",
     *     groups={"step1"})
     * @Assert\NotNull(groups={"step2"}, message="prenom.not.null")
     * @ORM\Column(name="prenom", type="string", length=255)
     */
    private $prenom;

    /**
     * @var \DateTime
     * @Assert\NotNull(groups={"step2"}, message="dateNaissance.not.null")
     * @ORM\Column(name="dateNaissance", type="date")
     */
    private $dateNaissance;

    /**
     * @var bool
     * @Assert\Type("bool",
     *     groups={"step2"},
     *     message="reduit.error")
     *
     * @ORM\Column(name="reduit", type="boolean")
     */
    private $reduit;

    /**
     * @var int
     * @Assert\NotBlank(groups={"step2"}, message="prix.not.blank")
     * @ORM\Column(name="prix", type="smallint")
     */
    private $prix;

    /**
     * @var string
     * @Assert\Country(groups={"step2"}, message="pays.not.correct")
     * @Assert\NotNull(groups={"step2"}, message="pays.not.null")
     * @ORM\Column(name="pays", type="string")
     */
    private $pays;

    /**
     * @var Booking
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Booking", inversedBy="tickets")
     */
    private $booking;
    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return Ticket
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set prenom
     *
     * @param string $prenom
     *
     * @return Ticket
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get prenom
     *
     * @return string
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Set dateNaissance
     *
     * @param \DateTime $dateNaissance
     *
     * @return Ticket
     */
    public function setDateNaissance($dateNaissance)
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    /**
     * Get dateNaissance
     *
     * @return \DateTime
     */
    public function getDateNaissance()
    {
        return $this->dateNaissance;
    }

    /**
     * Set reduit
     *
     * @param boolean $reduit
     *
     * @return Ticket
     */
    public function setReduit($reduit)
    {
        $this->reduit = $reduit;

        return $this;
    }

    /**
     * Get reduit
     *
     * @return boolean
     */
    public function getReduit()
    {
        return $this->reduit;
    }

    /**
     * Set prix
     *
     * @param integer $prix
     *
     * @return Ticket
     */
    public function setPrix($prix)
    {
        $this->prix = $prix;

        return $this;
    }

    /**
     * Get prix
     *
     * @return integer
     */
    public function getPrix()
    {
        return $this->prix;
    }

    /**
     * Set pays
     *
     * @param string $pays
     *
     * @return Ticket
     */
    public function setPays($pays)
    {
        $this->pays = $pays;

        return $this;
    }

    /**
     * Get pays
     *
     * @return string
     */
    public function getPays()
    {
        return $this->pays;
    }

    /**
     * Set booking
     *
     * @param \AppBundle\Entity\Booking $booking
     *
     * @return Ticket
     */
    public function setBooking(\AppBundle\Entity\Booking $booking = null)
    {
        $this->booking = $booking;

        return $this;
    }

    /**
     * Get booking
     *
     * @return \AppBundle\Entity\Booking
     */
    public function getBooking()
    {
        return $this->booking;
    }
}
