<?php

namespace App\Form;

use App\Entity\Task;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskStudentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $progressValue = 0;

        if ($options['task']) {
            $progressValue = $options['task']->getProgress();
        }

        $builder
            ->add('name', TextType::class, [
                'label' => 'Nazwa zadania',
                'mapped' => false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Opis',
                'mapped' => false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'Status zadania',
                'choices' => [
                    'Oczekuje na podjęcie' => 'new',
                    'W realizacji' => 'accepted',
                    'Anulowane' => 'canceled',
                    'Zakończone' => 'finished',
                    'Zablokowane' => 'locked',
                    'Przeterminowane' => 'overtimed'
                ],
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('progress', TextType::class, [
                'label' => 'Postęp (%)',
                'required' => true,
                'attr' => [
                    'id' => 'progress',
                    'class' => 'slider form-control',
                    'data-slider-id' => 'blue',
                    'data-slider-min' => '0',
                    'data-slider-max' => '100',
                    'data-slider-step' => '5',
                    'data-slider-value' => $progressValue
                ]
            ])
            ->add('finishedAt', TextType::class, [
                'label' => 'Data zakończenia',
                'required' => false,
                'mapped' => false,
                'attr' => [
                    'class' => 'form-control datepicker'
                ]
            ])
            ->addEventListener(FormEvents::POST_SET_DATA, function(FormEvent $event) use ($options) {
                $task = $event->getData();
                $finishedAt = $task->getFinishedAt();
                $finishedAtDate = '';

                if ($finishedAt) {
                    $finishedAtDate = $finishedAt->format('Y-m-d H:i:s');
                }

                $form = $event->getForm();
                $form->get('finishedAt')->setData($finishedAtDate);
            });
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'allow_extra_fields' => false,
            'data_class' => Task::class,
            'task' => null
        ));
    }
}