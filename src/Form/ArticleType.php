<?php

namespace App\Form;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'form-label mt-3'],
            ])
            ->add('texte', TextareaType::class, [
                'attr' => ['class' => 'form-control', 'rows' => 5],
                'label_attr' => ['class' => 'form-label mt-3'],
            ])
            ->add('publie', CheckboxType::class, [
                'attr' => ['class' => 'form-check-input mt-2'],
                'label' => 'Publié',
                'label_attr' => ['class' => 'form-check-label mt-2'],
                'required' => false
            ])
            ->add('date', DateTimeType::class, [
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'form-label mt-3'],
                'widget' => 'single_text',
                'data' => new \DateTimeImmutable(), 
            ])
            ->add('brochure', FileType::class, [
                'mapped' => false, 
                'required' => false,
                'attr' => ['class' => 'form-control'],
                'label' => 'Image (JPG, PNG)',
                'label_attr' => ['class' => 'form-label mt-3'],
                'constraints' => [
                    new File([
                        'maxSize' => '2048k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png'
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide (JPEG ou PNG).',
                    ])
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Créer l\'article',
                'attr' => ['class' => 'btn btn-primary mt-4 w-100'],
            ])
        ;
    }
    

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
