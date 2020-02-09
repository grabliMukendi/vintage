<?php

namespace App\Form;

use App\Entity\Promotion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class PromotionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, ['label' => 'Nom du code'])
            ->add('startDate', DateType::class, [
                'label' => 'Date de début de la promo',
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                // adds a class that can be selected in JavaScript
                'attr' => ['class' => 'js-datepicker'],
                // prevents rendering it as type="date", to avoid HTML5 date pickers
                'html5' => false,

            ])
            ->add('endDate', DateType::class, [
                'label' => 'Date de fin de la promo',
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                // adds a class that can be selected in JavaScript
                'attr' => ['class' => 'js-datepicker'],
                // prevents rendering it as type="date", to avoid HTML5 date pickers
                'html5' => false,

            ])
            ->add('remise', IntegerType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Promotion::class,
        ]);
    }
}
