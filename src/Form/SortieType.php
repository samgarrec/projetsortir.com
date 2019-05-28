<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class)
            ->add('dateheureDebut', DateTimeType::class, ['widget'=>'choice',
                'format'=>'y-M-d hh:mm ', 'label' => 'Date et heure de début'])

            ->add('dateLimite', DateType::class, ['widget' => 'choice', 'data' => new \DateTime("now"), 'label'=> 'Date limite d\'inscription'])
            ->add('nbInscriptionMax', IntegerType::class, ['label'=>'Nombre de participants maximum'])
            ->add('duree', IntegerType::class, ['label' => 'Durée (en minutes):'])
            ->add('infoSortie', TextareaType::class, ['label'=>'Description'])
            ->add('lieu', EntityType::class, ['class' => Lieu::class, 'choice_label' => 'nom'])
            ->add('enregistrer', SubmitType::class, ['label' => 'Enregistrer', 'class' => 'btn btn-outline-success'])
            ->add('annuler', SubmitType::class, ['label' => 'Annuler', 'class' => 'btn btn-outline-danger']);
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);

    }
}