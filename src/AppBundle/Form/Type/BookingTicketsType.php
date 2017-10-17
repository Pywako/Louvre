<?php

namespace AppBundle\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookingTicketsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom',      TextType::class, array('label' => 'ticket.nom'))
            ->add('prenom', TextType::class, array('label' => 'ticket.prenom'))
            ->add('dateNaissance',      DateType::class, [
                'widget'=> 'single_text',
                'html5'=> true,
                'attr' =>['class' => 'datepicker'],
                'label' => 'ticket.date.naissance',
                'format' => 'dd/MM/yyyy'
                ])
            ->add('pays',      CountryType::class, array('label' => 'ticket.pays'))
            ->add('reduit', CheckboxType::class, array('required' => false, 'label' => 'ticket.reduit'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Ticket'
        ));
    }
}
