<?php

namespace App\Form;

use App\Entity\UserAdresses;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class LivraisonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class)
            ->add('prenom', TextType::class, [
                'label' => 'Prénom'
            ])
            ->add('telephone', IntegerType::class, [
                'label' => 'Téléphone'
            ])
            ->add('adresse', TextareaType::class)
            ->add('codePostal', IntegerType::class)
            ->add('pays', TextType::class)
            ->add('ville', TextType::class)
            ->add('complement', TextType::class)
            //->add('avatar')
            //->add('updatedAt')
            //->add('profession')
            //->add('birthday')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserAdresses::class,
            'attr' => [
                'novalidate' => 'novalidate',
            ]
        ]);
    }
}
