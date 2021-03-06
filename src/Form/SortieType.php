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
            ->add('dateheureDebut', DateTimeType::class, ['widget'=>'choice', 'label'=>'Date de la sortie'])
            ->add('dateLimite', DateType::class, ['widget' => 'choice', 'data' => new \DateTime("now"), 'label'=> 'Clôture d\'inscription' ])
            ->add('nbInscriptionMax', IntegerType::class, ['label'=>'Nombre de participants'])
            ->add('duree', IntegerType::class, ['label' => 'Durée (en minutes):'])
            ->add('infoSortie', TextareaType::class, ['label' => 'Description'])
            ->add('ville', EntityType::class, ['class' => Ville::class, 'choice_label' => 'nom','mapped'=>false])
            ->add('lieu', EntityType::class, ['class' => Lieu::class, 'choice_label' => 'nom'])
            ->add('enregistrer', SubmitType::class, array(
                'attr' => array('class' => 'btn btn-outline-light')),  ['label' => 'Enregistrer'])

            ->add('annuler', SubmitType::class, array(
                'attr' => array('class' => 'btn btn-outline-light')), ['label' => 'Annuler']);
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => Sortie::class]);
    }
}