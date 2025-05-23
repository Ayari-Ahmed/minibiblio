<?php

namespace App\Form;

use App\Entity\Livre;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
            ->add('price', NumberType::class, [
                'attr' => [
                'class' => 'form-control',
                    'placeholder' => 'Entrez le prix du livre'
                ],
                'label' => 'Prix'
            ])
            ->add('publicationYear', NumberType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez l\'année de publication'
                ],
                'label' => 'Année de Publication',
                'required' => false,
            ])
            ->add('genre', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez le genre du livre'
                ],
                'label' => 'Genre',
                'required' => false,
            ])
            ->add('language', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez la langue du livre'
                ],
                'label' => 'Langue',
                'required' => false,
            ])
            ->add('publisher', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez le nom de l\'éditeur'
                ],
                'label' => 'Éditeur',
                'required' => false,
            ])
            ->add('numberOfPages', NumberType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez le nombre de pages'
                ],
                'label' => 'Nombre de Pages',
                'required' => false,
            ])
            ->add('discount', NumberType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez le discount du livre'
                ],
                'label' => 'Discount',
                'required' => false,
            ])
            ->add('availability', CheckboxType::class, [
                'label' => 'Availability',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Livre::class,
        ]);
    }
}
