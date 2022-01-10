<?php

namespace App\Form;

use App\Entity\Task;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class TaskEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('description', CKEditorType::class, [
                'required' => false
            ])
            ->add('priority')
            ->add('dueAt', DateType::class, [ 
                'widget' => 'single_text',
                          'html5' => false,
                          'attr' => ['class' => 'js-datepicker'],
                ])
            ->add('expectedDuration')
            ->add('actualDuration')
            ->add('project')
            ->add('assignee')
            ->add('category')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}
