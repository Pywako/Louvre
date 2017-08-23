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
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookingCommanderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email',      EmailType::class)
            ->add('dateVisite',      DateType::class, [
                'widget'=> 'single_text',
                'html5'=> false,
                'attr' =>['class' => 'datepicker']
                ])
            ->add('nbTicket',       IntegerType::class)
            ->add('type',      CheckboxType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Booking'
        ));
    }
}