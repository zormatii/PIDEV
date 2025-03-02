<?php

namespace App\Form;

use App\Entity\Partenaire;
use App\Entity\Collaboration;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class PartenaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nomP', TextType::class, [
                'label' => 'Name',
                'required' => true, // Optional: Set if required
            ])
            ->add('emailP', EmailType::class, [
                'label' => 'Email',
                'required' => true, // Optional: Set if required
            ])
            ->add('telephone', TelType::class, [
                'label' => 'Telephone',
                'required' => true, // Optional: Set if required
            ])
            ->add('typeP', ChoiceType::class, [
                'label' => 'Type',
                'choices' => [
                    'Cross-Promotion' => 'cross_promotion',
                    'Event Integration' => 'event_integration',
                    'Co-Branding' => 'co_branding',
                    'Ticketing or Registration' => 'ticketing_registration',
                    'Exclusive Content' => 'exclusive_content',
                    'Data Sharing/Analytics' => 'data_sharing',
                    'Influencer or Celebrity Partnerships' => 'influencer_partnerships',
                ],
                'placeholder' => 'Select a type',
                'required' => true, // Optional: Set if required
            ])
            ->add('collaboration', EntityType::class, [
                'class' => Collaboration::class,
                'choice_label' => 'nom_c',  
                'label' => 'Collaboration',
                'required' => true, // Optional: Set if required
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Save',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Partenaire::class,
        ]);
    }
}