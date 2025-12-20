<?php

namespace App\Form;

use App\Entity\ContenuNumero;
use App\Entity\File;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContenuNumeroType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('subTitle')
            ->add('content')
                 ->add('image', FileType::class, [
                'label' => false,
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'hidden',
                    'accept' => '.jpg,.png,.jpeg,.gif,.svg',
                ],
            ])
                     ->add('draft_button', SubmitType::class, [
                'label' => 'Brouillon',
                'attr' => [
                    'class' => 'bg-[#AAEFEF] text-[#069490] px-4 py-2 rounded-lg font-semibold flex items-center gap-2 text-lg hover:bg-[#8EDFDF] transition cursor-pointer'
                ]
            ])
            ->add('publish_button', SubmitType::class, [
                'label' => 'Publier',
                'attr' => [
                    'class' => 'bg-teal-600 text-white px-4 py-2 rounded-lg flex items-center hover:bg-teal-700 transition duration-200 font-semibold cursor-pointer flex gap-2'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ContenuNumero::class,
        ]);
    }
}
