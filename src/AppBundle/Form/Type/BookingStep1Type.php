<?php

namespace AppBundle\Form\Type;


use AppBundle\Entity\Booking;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookingStep1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email',      RepeatedType::class, array(
                'type'=> EmailType::class,
                'invalid_message' => 'Les deux emails ne correspondent pas',
                'options' => array('attr' =>array('class' => 'email')),
                'required' => true,
                'first_options'  => array('label' => 'Email'),
                'second_options' => array('label' => 'Confirmation Email'),
                ))
            ->add('dateVisit',      DateType::class, [
                'widget'=> 'single_text',
                'format' => 'dd/MM/yyyy',
                'html5'=> false,
                'attr' =>['class' => 'datepicker']
                ])
            ->add('nbTicket',       IntegerType::class)
            ->add('type',      ChoiceType::class, array(
                'choices' =>array(
                    'Journée' => Booking::TYPE_DAY,
                    'Demi-journée' => Booking::TYPE_HALF_DAY,
                )))
            ->add('continuer', SubmitType::class );
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'validation_groups' => array('step1'),
            'data_class' => 'AppBundle\Entity\Booking'
        ));
    }
}
