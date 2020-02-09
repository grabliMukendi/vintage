<?php

namespace App\Form;

use App\Entity\Media;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Validator\Constraints as Assert;

class MediaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('imageFile', VichImageType::class, [
                'label' => 'Insérer une image',
                'required' => false,
                'download_uri' => true,
                'image_uri' => true,
                /*'constraints' => [
                    new Assert\NotBlank(array(
						'message' => 'Veuillez insérer une image.'
					)),
                ]*/
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Media::class,
            'attr' => [
                'novalidate' => 'novalidate'
            ]
        ]);
    }
}
