<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'Nazwa użytkownika',
                'required' => true,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => true,
                'mapped' => false,
                'first_options' => [
                    'label' => 'Hasło',
                    'attr' => [
                        'class' => 'form-control'
                    ]
                ],
                'second_options' => [
                    'label' => 'Powtórz hasło',
                    'attr' => [
                        'class' => 'form-control'
                    ]
                ]
            ])
            ->add('firstName', TextType::class, [
                'label' => 'Imię',
                'required' => true,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nazwisko',
                'required' => true,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Typ użytkownika',
                'choices' => [
                    'Student' => 'student',
                    'Administrator' => 'administrator'
                ],
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('isActive', CheckboxType::class, [
                'label' => 'Konto aktywne',
                'required' => false
            ])
            ->add('userImage', FileType::class, [
                'label' => 'Awatar użytkownika',
                'required' => false
            ])
        ;

        if ($options['user_exists']) {
            $builder->remove('plainPassword');
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'allow_extra_fields' => true,
            'data_class' => User::class,
            'user_exists' => false
        ));
    }
}