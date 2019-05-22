<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Site;
use App\Entity\Sortie;
use Doctrine\DBAL\Types\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('site',EntityType::class,[
                'class' => Site::class,
                'choice_label' => 'nom',
            ])
            ->add('nomdeLaSortie',TextType::class, [
            ])
            ->add('dateDepart',DateType::class, [
                'widget' => 'choice',
            ])
            ->add('dateFin',DateType::class, [
                'widget' => 'choice',
            ]);



    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([

        ]);
    }
}


