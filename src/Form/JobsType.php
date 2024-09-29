<?php

namespace App\Form;

use App\Entity\Jobs;
use App\Entity\Tags;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
            ->add('description');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Jobs::class,
        ]);
    }
}

