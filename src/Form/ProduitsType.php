<?php

namespace App\Form;

use App\Form\TvaType;
use App\Form\MediaType;
use App\Entity\Produits;
use App\Form\TailleType;
use App\Form\CouleurType;
use App\Form\CategorieType;
use App\Form\PromotionType;
use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ProduitsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre', TextType::class)
            ->add('description', CKEditorType::class)
            ->add('couleur', CouleurType::class)
            ->add('taille', TailleType::class, [
                'required' => false,
            ])
            ->add('public', ChoiceType::class, [
                'required' => false,
                'placeholder' => 'Votre civilité',
                'choices' => [
                    'Homme' => 'm',
                    'Femme' => 'f',
                    'Mixte' => 'mixte'
                ]
            ])
            ->add('prix', MoneyType::class)
            ->add('disponible', IntegerType::class)
            ->add('image', CollectionType::class, [
                'entry_type' => MediaType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
            ])
            ->add('tva', TvaType::class, [ 'label' => false ])
            ->add('categorie', CategorieType::class, [ 'label' => false ])
            ->add('promotion', PromotionType::class, [ 
                'label' => false,
                'required' => false,
            ])
            ->add('rubrique', ChoiceType::class, [
                'placeholder' => 'Veuillez choisir la rubrique',
                'choices' => [
                    'Tendance' => 'tendance',
                    'Art déco' => 'art déco',
                    'Automobile' => 'automobile',
                    'Sport et loisir' => 'sport et loisir',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Produits::class,
            'attr' => [
                'novalidate' => 'novalidate'
            ]
        ]);
    }
}
