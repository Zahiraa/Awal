<?php

namespace App\Form;

use App\Entity\Terme;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TermeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('image', FileType::class, [
                'label' => false,
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'hidden',
                    'accept' => '.jpg,.png,.jpeg,.gif,.svg',
                ],

            ])
            ->add('contents', CollectionType::class, [
                'label' => false,
                'entry_type' => ContentForm::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference' => false,
                'attr' => [
                    'class' => 'contents-collection space-y-4'
                ],
                'label_attr' => [
                    'class' => 'text-lg font-semibold text-gray-700'
                ],
            ])
            ->add('draft_button', SubmitType::class, [
                'label' => 'Brouillon',
                'attr' => [
                    'class' => 'bg-[#AAEFEF] text-[#069490] px-4 py-2 rounded-2xl font-semibold flex items-center gap-2 text-lg hover:bg-[#8EDFDF] transition cursor-pointer'
                ]
            ])
            ->add('publish_button', SubmitType::class, [
                'label' => 'Publier',
                'attr' => [
                    'class' => 'bg-teal-600 text-white px-4 py-2 rounded-lg flex items-center hover:bg-teal-700 transition duration-200 font-semibold cursor-pointer flex gap-2'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Terme::class,
        ]);
    }
}
