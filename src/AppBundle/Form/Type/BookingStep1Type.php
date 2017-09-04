<?php
/**
 * Created by PhpStorm.
 * User: Pywako
 * Date: 21/08/2017
 * Time: 14:41
 */

namespace AppBundle\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookingStep1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email',      EmailType::class)
            ->add('dateVisit',      DateType::class, [
                'widget'=> 'single_text',
                'format' => 'dd/MM/yyyy',
                'html5'=> false,
                'attr' =>['class' => 'datepicker']
                ])
            ->add('nbTicket',       IntegerType::class)
            ->add('type',      ChoiceType::class, array(
                'choices' =>array(
                    'Journée' => 1,
                    'Demi-journée' => 2,
                )))
            ->add('continuer', SubmitType::class );
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Booking'
        ));
    }
}