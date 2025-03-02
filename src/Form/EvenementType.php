<?php
// src/Form/EvenementType.php
namespace App\Form;

use App\Entity\Evenement;
use App\Entity\Categorie;
use App\Entity\TypeEvenement;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class EvenementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
         $builder
            ->add('mail', TextType::class, [
                'label' => 'enter your mail',
                'attr' => ['placeholder' => 'Enter a mail'],
            ])
             // Ajout du champ Type d'événement
             ->add('typeEvenement', EntityType::class, [
                 'class' => TypeEvenement::class,
                 'choice_label' => 'nom',
                 'label' => 'Type d\'événement',
                 'placeholder' => 'Choisissez un type d\'événement',
             ])
             ->add('titre', TextType::class, [
                 'label' => 'Title',
                 'attr' => ['placeholder' => 'Enter the event title'],
             ])
             ->add('description', TextType::class, [
                 'label' => 'Description',
                 'attr' => ['placeholder' => 'Enter a description'],
             ])
             ->add('lieu', TextType::class, [
                 'label' => 'Location',
                 'attr' => ['placeholder' => 'Enter the location'],
             ])
             ->add('nombre_de_places', IntegerType::class, [
                 'label' => 'Number of seats',
                 'attr' => ['placeholder' => 'Enter the number of seats'],
             ])
             ->add('date_debut', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Start date',
                'data' => new \DateTime(), // Définit la date actuelle
            ])
             ->add('date_fin', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'End date',
                'data' => (new \DateTime())->modify('+1 day'), // Définit la date de demain
            ])
             ->add('url_image', FileType::class, [
                'label' => 'Image',
                'mapped' => false,
                'required' => false,
            ])
            ->add('latitude', NumberType::class, [
                'label' => 'Latitude',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'step' => 'any',
                    'placeholder' => 'Enter latitude'
                ]
            ])
            ->add('longitude', NumberType::class, [
                'label' => 'Longitude',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'step' => 'any',
                    'placeholder' => 'Enter longitude'
                ]
            ])
             ->add('statut', ChoiceType::class, [
                'label' => 'Statut',
                'choices' => [
                    'En attente' => 'en attente',
                ],
                'disabled' => true, // Champ désactivé pour empêcher toute modification
            ])
         ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
         $resolver->setDefaults([
             'data_class' => Evenement::class,
         ]);
    }
}
