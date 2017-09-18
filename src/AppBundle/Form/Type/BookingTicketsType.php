<?php

namespace AppBundle\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookingTicketsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom',      TextType::class, array('label' => 'Nom'))
            ->add('prenom', TextareaType::class, array('label' => 'Prénom'))
            ->add('dateNaissance',      DateType::class, [
                'widget'=> 'single_text',
                'html5'=> true,
                'attr' =>['class' => 'datepicker'],
                'label' => 'Date de naissance',
                'format' => 'dd/MM/yyyy'
                ])
            ->add('pays',      CountryType::class, array('label' => 'Pays', 'placeholder' => 'Choisissez un pays'))
            ->add('reduit', CheckboxType::class, array('required' => false, 'label' => 'Tarif réduit'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Ticket'
        ));
    }
}