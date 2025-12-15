<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Translation\TranslatableMessage;

class RegistrationForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'register.lastName.label',
                'attr' => ['placeholder' => 'register.lastName.placeholder'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'register.lastName.required',
                    ]),
                ],
            ])
            ->add('prenom', TextType::class, [
                'attr' => ['placeholder' => 'register.firstName.placeholder'],
                'label' => 'register.firstName.label',
                'constraints' => [
                    new NotBlank([
                        'message' => 'register.firstName.required',
                    ]),
                ],
            ])
            ->add('email',EmailType::class,
                [
                     'label' => 'register.email.label',
                    'attr' => ['placeholder' => 'register.email.placeholder'],
                ]
            )
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                "label" => 'register.termes',
                'constraints' => [
                    new IsTrue([
                        'message' => 'register.termesRequired',
                    ]),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                 'invalid_message' => 'register.confirmPassword.error',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => [
                    'label' => 'register.password.label',
                    'attr' => [ 'placeholder' => 'register.password.placeholder'],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'register.password.required',
                        ]),
                        new Assert\Length([
                            'min' => 8,
                            'minMessage' => 'register.password.min',
                        ]),
                    ],

                ]
                ,
                'second_options' => [
                    'label' => 'register.confirmPassword.label',
                    'attr' => ['placeholder' => 'register.confirmPassword.placeholder'],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'register.confirmPassword.required',
                        ]),
                        new Assert\Length([
                            'min' => 8,
                            'minMessage' => 'register.confirmPassword.min',
                        ]),
                    ],
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'translation_domain' => 'messages',
        ]);
    }
}
