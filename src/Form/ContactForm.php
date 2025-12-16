<?php

namespace App\Form;

use App\DTO\ContactDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class ContactForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'required' => true,
                'attr' => ['placeholder' => 'ContactSection.page.form.nom.placeholder'],
                'label' => 'ContactSection.page.form.nom.label',
            ])
            ->add('prenom', TextType::class, [
                'required' => true,
                'attr' => ['placeholder' => 'ContactSection.page.form.prenom.placeholder'],
                'label' => 'ContactSection.page.form.prenom.label',
            ])
            ->add('email', EmailType::class, [
                'required' => true,
                'attr' => ['placeholder' => 'you@example'],
                'label' => 'Email',
            ])
      
            ->add('subject', TextType::class, [
                'required' => true,
                'attr' => ['placeholder' => 'ContactSection.page.form.subject.label'],
                'label' => 'Sujet',
            ])
            ->add('message', TextareaType::class, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'ContactSection.page.form.message.placeholder',
                    'rows' => 5
                ],
                'label' => 'ContactSection.page.form.message.label',
                // max length is not set here, but you can add it if needed
                'constraints' => [
                    new Length([
                        'max' => 1000,
                        'maxMessage' => 'contact.message.errors.maxLength',
                    ]),
                ],
            ])
           
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ContactDTO::class,
        ]);
    }
}
