<?php
// src/Form/CategorieType.php
namespace App\Form;

use App\Entity\Categorie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategorieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
         $builder
             ->add('nom', TextType::class, [
                 'label' => 'Type',
                 'attr' => ['placeholder' => 'Enter the type of category'],
             ])
             ->add('description', TextareaType::class, [
                 'label' => 'Description',
                 'attr' => ['placeholder' => 'Enter the description'],
             ])
             ->add('url_image', FileType::class, [
                'label' => 'Image',
                'mapped' => false,
                'required' => false,
            ])
            
             ->add('statut', CheckboxType::class, [
                 'label'    => 'Status',
                 'required' => false,
             ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
         $resolver->setDefaults([
             'data_class' => Categorie::class,
         ]);
    }
}

