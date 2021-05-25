<?php

namespace App\Form;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nazwa zadania',
                'required' => true,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Opis',
                'required' => true,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('assignedTo', EntityType::class, [
                'label' => 'Przypisane do',
                'class' => User::class,
                'choice_label' => 'choiceData',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('deadlineAt', TextType::class, [
                'label' => 'Termin wykonania',
                'required' => false,
                'mapped' => false,
                'attr' => [
                    'class' => 'form-control datepicker'
                ]
            ])
            ->add('files', FileType::class, [
                'label' => 'Załączniki',
                'required' => false,
                'mapped' => false,
                'multiple' => true,
                'attr'     => [
                    'accept' => 'application/msword, application/vnd.ms-excel, application/vnd.ms-powerpoint, text/plain, application/pdf, image/*',
                    'multiple' => 'multiple'
                ]
            ])
            ->addEventListener(FormEvents::POST_SET_DATA, function(FormEvent $event) use ($options) {
                $task = $event->getData();
                $deadlineAt = $task->getDeadlineAt();
                $deadlineAtDate = '';

                if ($deadlineAt) {
                    $deadlineAtDate = $deadlineAt->format('Y-m-d H:i:s');
                }

                $form = $event->getForm();
                $form->get('deadlineAt')->setData($deadlineAtDate);
            });
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'allow_extra_fields' => true,
            'data_class' => Task::class,
            'task' => null
        ));
    }
}