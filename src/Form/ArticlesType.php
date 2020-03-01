<?php

namespace App\Form;

use App\Entity\Articles;
use App\Form\ContentsType;
use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ArticlesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [ 'label' => 'Titre de l\'article' ])
            ->add('chapeau', CKEditorType::class, [ 'label' => 'Chapeau de l\'article' ])
            ->add('contents', CollectionType::class, 
            [
                'label' => 'Conténu',
                'entry_type' => ContentsType::class,
                'allow_add' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Articles::class,
            'attr' => 
            [
                'novalidate' => 'novalidate',
            ]
        ]);
    }
}
