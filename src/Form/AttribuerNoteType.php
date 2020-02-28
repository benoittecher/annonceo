<?php

namespace App\Form;

use App\Entity\Note;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type as Input;
use Symfony\Component\Validator\Constraints;

class AttribuerNoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('note', Input\NumberType::class, ["attr" => ["min" => 1,"max" => 5],
                                              "constraints" => [
                                                  new Constraints\NotBlank(["message" => "Vous devez attribuer une note"]),
                                                  new Constraints\LessThanOrEqual(["value" => 5, "message" => "La note doit être inférieure ou égale à 5"]),
                                                  new Constraints\GreaterThan(["value" => 1, "message" => "La note doit être supérieure à 0"])
                                              ]
                                            ])
            ->add('avis', Input\TextareaType::class, [ "label" => "Avis"])
            ->add("ajouter", Input\SubmitType::class, ["label" => "Enregistrer"])
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Note::class,
        ]);
    }
}
