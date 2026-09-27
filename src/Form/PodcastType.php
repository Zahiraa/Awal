<?php

namespace App\Form;

use App\Entity\File;
use App\Entity\Podcast;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PodcastType extends AbstractType
{
       public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('subTitle')
            ->add('statut', \Symfony\Component\Form\Extension\Core\Type\ChoiceType::class, [
                'choices' => [
                    'مسودة' => Podcast::STATUT_DRAFT,
                    'منشور' => Podcast::STATUT_PUBLISHED,
                ],
                'placeholder' => 'اختر الحالة', // Optionnel
            ])
            ->add('content')
            ->add('media', \Symfony\Component\Form\Extension\Core\Type\FileType::class, [
                'label' => false,
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'hidden', // Ou on peut laisser vide et gérer la classe absolue dans Twig
                    'accept' => '.mp3,.wav,.mp4,.webm',
                ],
            ])
            ->add('image', \Symfony\Component\Form\Extension\Core\Type\FileType::class, [
                'label' => false,
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'hidden',
                    'accept' => '.jpg,.png,.jpeg,.gif,.svg',
                ],
            ])
        ;
    }



    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Podcast::class,
        ]);
    }
}
