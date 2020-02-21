<?php

namespace App\Form;

use App\Entity\Categorie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints as Contrainte;

class CategorieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre', TextType::class, [ "help" => "Tapez le titre de la catégorie",
                                               "data" => "Catégorie",
                                               "constraints" => [ 
                                                                new Contrainte\NotBlank(["message" => "Vous avez oublié de remplir ce champ"]),
                                                                new Contrainte\Length(["min" => 2, "max" => 20,
                                                                            "minMessage" => "Le titre doit contenir au moins 2 caractères",
                                                                            "maxMessage" => "Le titre ne doit pas dépasser 20 caractères"])
                                                                ]])
            ->add('motscles', TextareaType::class, [ "label" => "Mots clés", "help" => "Séparez les mots clés par des virgules"])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Categorie::class,
        ]);
    }
}
