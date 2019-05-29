<?php

namespace App\Form;

use App\Entity\Participant;
use App\Entity\Site;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParticipantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('nom')
            ->add('prenom')
            ->add('telephone')
            ->add('email')
            ->add('picture', FileType::class, array('label' => 'Photo (png, jpeg)','data_class'=>null,'required'=>false))
            ->add('Site', EntityType::class,['class'=>Site::class,'query_builder'
            => function(EntityRepository $er){
                return $er->createQueryBuilder('s')
                    ->orderBy('s.nom','ASC');

                },'choice_label'=>'nom'])
            ->add('picture', FileType::class, array('label' => 'Picture (png, jpeg)', 'data_class'=> null, 'required' => false));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
