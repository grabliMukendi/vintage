<?php

namespace App\Form;

use App\Entity\Tva;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class TvaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('multiplicate', ChoiceType::class, 
            [ 
                'label' => 'Multiplier',
                'choices' => [
                    '1.021' => '1.021',
                    '1.055' => '1.055',
                    '1.07' => '1.07',
                    '1.1' => '1.1',
                    '1.2' => '1.2',
                ]
                ])
            ->add('nom', ChoiceType::class, 
            [
                'choices' => [
                    'TVA 2.1%' => 'TVA 2.1%',
                    'TVA 5.5%' => 'TVA 5.5%',
                    'TVA 7%' => 'TVA 7%',
                    'TVA 10%' => 'TVA 10%',
                    'TVA 20%' => 'TVA 20%',
                ]
            ])
            ->add('valeur', ChoiceType::class, 
            [    
                'choices' => [
                    '2.1' => '2.1',
                    '5.5' => '5.5',
                    '7' => '7',
                    '10' => '10',
                    '20' => '20',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Tva::class,
            'attr' => [
                'novalidate' => 'novalidate'
            ]
        ]);
    }
}
