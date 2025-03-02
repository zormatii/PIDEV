<?php

namespace App\Form;

use App\Entity\Formation;
use App\Entity\Workshop;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class FormationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('datedebut', null, [
                'widget' => 'single_text',
            ])
            ->add('datefin', null, [
                'widget' => 'single_text',
            ])
            ->add('capacite')
            ->add('titre')
            ->add('statut')
            ->add('modeFormation', ChoiceType::class, [
                'choices' => [
                    'Présentiel' => 'Présentiel',
                    'Distanciel' => 'Distanciel',
                ],
                'required' => false,
                'placeholder' => 'Sélectionner un mode',
            ])
            ->add('workshop', EntityType::class, [
                'class' => Workshop::class,
                'choice_label' => 'titre',
            ])

            ->add('submit', SubmitType::class, [
                'label' => 'submit',
                'attr' => ['class' => 'btn btn-primary'], // Ajoute une classe Bootstrap
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Formation::class,
        ]);
    }
}
