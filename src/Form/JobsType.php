<?php

namespace App\Form;

use App\Entity\Jobs;
use App\Entity\Tags;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class JobsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('label')
            ->add('tags', EntityType::class, [
                'class' => Tags::class, // Utilisez `Tag` au lieu de `Tags`
                'choice_label' => 'name',
                'multiple' => true, // Permet de sélectionner plusieurs tags
                'expanded' => true, // Transforme le champ en cases à cocher
                'by_reference' => false, // Pour gérer la relation Many-to-Many
            ])
            ->add('description', CKEditorType::class, [

                'label' => 'Contenu',
                'purify_html' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de renseigner un contenu'
                    ]),
                    new Length([
                        'min' => 5,
                        'minMessage' => 'Le contenu doit contenir au moins {{ limit }} caractères',
                        'max' => 5000,
                        'maxMessage' => 'Le contenu doit contenir au maximum {{ limit }} caractères'
                    ]),
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Jobs::class,
        ]);
    }
}

