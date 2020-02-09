<?php

namespace App\Form;

use App\Entity\UserAdresses;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class UserAdressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', HiddenType::class)
            ->add('prenom', HiddenType::class)
            ->add('telephone', IntegerType::class, [ 
                'label' => 'Téléphone',
            ])
            ->add('codePostal', IntegerType::class)
            ->add('pays', TextType::class)
            ->add('ville', TextType::class, )
            ->add('adresse', TextareaType::class)
            ->add('birthday', DateType::class, [
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                // adds a class that can be selected in JavaScript
                'attr' => ['class' => 'js-datepicker'],
                // prevents rendering it as type="date", to avoid HTML5 date pickers
                'html5' => false,

            ])
            ->add('profession', ChoiceType::class, [
                'placeholder' => 'Sélectionnez ici...',
                'choices' => [
                    'Agriculteur' => 'agriculteur',
                    'Artisan, commerçant, chef d\'entreprise de moins de 10 salariés' => 'artisan, commerçant, chef d\'entreprise de moins de 10 salariés',
                    'Cadre supérieur, chef d\'entreprise de plus de 10 salariés' => 'cadre supérieur, chef d\'entreprise de plus de 10 salariés',
                    'Profession libérale' => 'profession libérale',
                    'Profession intermédiaire (cadre moyen, technicien,…)' => 'profession intermédiaire (cadre moyen, technicien,…)',
                    'Employé' => 'employé',
                    'Ouvrier' => 'ouvrier',
                    'Étudiant' => 'étudiant',
                    'Homme / Femme au foyer' => 'Homme / Femme au foyer',
                    'Journaliste' => 'journaliste',
                    'Informatique' => 'informatique',
                    'Autre…' => 'autre…',
                ]
            ])
            ->add('complement', TextType::class, [ 
                'label' => 'Complément adresse'
            ])
            ->add('avatarFile', FileType::class, [ 
                'label' => false,
                'required' => false,
                //'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserAdresses::class,
            'attr' => [
                'novalidate' => 'novalidate'
            ]
        ]);
    }
}
