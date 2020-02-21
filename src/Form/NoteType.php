<?php

namespace App\Form;

use App\Entity\Note;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\EntityType;

class NoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
            
            
        
        $builder
            ->add('note', TextType::class, [ "help" => "Tapez la note",
                                               "data" => "",
                                               "attr" => ["min" => 0,
                                                          "max" => 20]
                                            ])
            ->add('avis', TextareaType::class, [ "label" => "Avis"])
            ->add('membre_note_id', EntityType::class, [ "class" => Membre::class,
                                                 "choice_label" =>"email",
                                                 "label" => "Adresse mail du membre que vous notez"])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Note::class,
        ]);
    }
}
