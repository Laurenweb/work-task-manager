<?php

namespace App\Form;

use App\Entity\Task;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskShortCreateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('wantedDuration')
            ->add('dueAt', DateType::class, [ 
                'widget' => 'single_text',
                          'html5' => false,
                          'attr' => ['class' => 'js-datepicker'],
                ])
            ->add('assignee')
            ->add('project')
            ->add('description', CKEditorType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}
