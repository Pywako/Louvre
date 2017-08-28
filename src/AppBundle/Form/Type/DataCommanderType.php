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
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DataCommanderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom',      TextType::class)
            ->add('prenom', TextareaType::class)
            ->add('DateNaissance',      DateType::class, [
                'widget'=> 'single_text',
                'html5'=> false,
                'attr' =>['class' => 'datepicker']
                ])
            ->add('Pays',      CountryType::class, array(
                'choices' =>array(
                    'Journée' => 1,
                    'Demi-journée' => 2,
                )))
            ->add('reduit', CheckboxType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Ticket'
        ));
    }
}