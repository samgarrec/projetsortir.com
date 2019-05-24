<?php

namespace App\Form;

 use Doctrine\ORM\EntityRepository;
 use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
 use Symfony\Component\Form\Extension\Core\Type\SubmitType;
 use Symfony\Component\Form\FormTypeInterface;
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
            ->add('Site', EntityType::class,['class'=>Site::class,'query_builder'
            => function(EntityRepository $er){
                    return $er->createQueryBuilder('s')
                        ->orderBy('s.nom','ASC');

                },'choice_label'=>'nom',


            ])


            ->add('nomDeLaSortie',\Symfony\Component\Form\Extension\Core\Type\TextType::class, [
            ])
            ->add('dateDepart',DateType::class, [
                'widget' => 'choice',
            ])
            ->add('dateFin',DateType::class, [
                'widget' => 'choice',
            ])

            ->add('isOrganisateur', CheckboxType::class, [
                'label'    => 'Sorties dont je suis l \'orgarnisateur',
                'required' => false,
            ])
            ->add('isRegistred', CheckboxType::class, [
                'label'    => 'Sorties auquelles je suis inscrit',
                'required' => false,
            ])
            ->add('isNotRegistred', CheckboxType::class, [
                'label'    => 'Sorties auquelles je ne suis pas inscrit',
                'required' => false,
            ])
            ->add('pastSorties', CheckboxType::class, [
                'label'    => 'Sorties passées ',
                'required' => false,
            ])
        ->add('rechercher',SubmitType::class);



    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([


        ]);
    }
}


