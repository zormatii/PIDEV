<?php

namespace App\Form;

use App\Entity\Collaboration;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class CollaborationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom_c', TextType::class, [
                'label' => 'Collaboration Name',
                'attr' => ['placeholder' => 'Enter name'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'The name cannot be empty.']),
                    new Assert\Length([
                        'min' => 3,
                        'max' => 255,
                        'minMessage' => 'The name must have at least {{ limit }} characters.',
                        'maxMessage' => 'The name cannot exceed {{ limit }} characters.',
                    ]),
                ],
            ])
            ->add('type', TextType::class, [
                'label' => 'Type',
                'attr' => ['placeholder' => 'Enter type'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'The type cannot be empty.']),
                    new Assert\Length([
                        'min' => 3,
                        'max' => 255,
                        'minMessage' => 'The type must have at least {{ limit }} characters.',
                        'maxMessage' => 'The type cannot exceed {{ limit }} characters.',
                    ]),
                ],
            ])
            ->add('date_sig', null, [
                'widget' => 'single_text',
                
            ])
            
            ->add('date_ex', null, [
                'widget' => 'single_text',
            ])
            
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Active' => 'active',
                    'Expired' => 'expired',
                ],
                'label' => 'Status',
                'expanded' => true, // Display as radio buttons
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Please select a status.']),
                    new Assert\Choice([
                        'choices' => ['active', 'expired'],
                        'message' => 'Invalid status selected.',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Collaboration::class,
        ]);
    }
}
