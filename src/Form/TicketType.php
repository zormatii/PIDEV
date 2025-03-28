<?php

namespace App\Form;

use App\Entity\Reservation;
use App\Entity\Ticket;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class TicketType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Nom')
            ->add('Prenom')
            ->add('Numero_Telephone')
            ->add('Email')
            ->add('date', null, [
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd', // Ensures compatibility with HTML5 date picker
                'required' => false,
            ])
            ->add('Prix')
            ->add('reservation', EntityType::class, [
                'class' => Reservation::class,
                'choice_label' => 'Nom Evenement',
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
            'data_class' => Ticket::class,
        ]);
    }
}
