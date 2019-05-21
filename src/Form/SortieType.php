<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Site;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('dateheureDebut',DateType::class, [
                'widget' => 'choice',
            ])
            ->add('duree')
            ->add('dateLimite',DateType::class, [
        'widget' => 'choice',
    ])
            ->add('nbInscriptionmax',IntegerType::class)
            ->add('infoSortie',TextareaType::class)

            ->add('site',EntityType::class,[
                'class' => Site::class,
                'choice_label' => 'nom',
            ])

            ->add('Lieu',EntityType::class,[
                'class' => Lieu::class,
                'choice_label' => 'nom',
            ]
        )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
