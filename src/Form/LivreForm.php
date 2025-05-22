<?php

namespace App\Form;

use App\Entity\Livre;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class LivreForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez le titre du livre'
                ],
                'label' => 'Titre du Livre'
            ])
            ->add('author', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez le nom de l\'auteur'
                ],
                'label' => 'Auteur'
            ])
            ->add('publishedAt', DateType::class, [
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Sélectionnez la date de publication'
                ],
                'label' => 'Date de Publication'
            ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez la description du livre',
                    'style' => 'height: 150px;'
                ],
                'label' => 'Description'
            ])
            ->add('price', IntegerType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez le prix du livre'
                ],
                'label' => 'Prix',
                'required' => true,
            ])
            ->add('imageFile', VichImageType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Téléchargez l\'image du livre'
                ],
                'label' => 'Image du livre',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Livre::class,
        ]);
    }
}
