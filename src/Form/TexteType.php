<?php

namespace App\Form;

use App\Entity\File;
use App\Entity\Texte;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TexteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('subTitle')
               ->add('content', TextareaType::class, [
                'label' => 'Contenu',
                'label_attr' => [
                    'class' => 'text-lg text-gray-700'
                ],
            ])
     
               ->add('image', FileType::class, [
                'label' => false,
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'hidden',
                    'accept' => '.jpg,.png,.jpeg,.gif,.svg',
                ],

            ])
               ->add('media', FileType::class, [
                'label' => false,
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'hidden',
                    'accept' => 'mp3, wav, ogg, aac, flac, mp4, avi, mov, wmv, flv, webm'
                ],

            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Texte::class,
        ]);
    }
}
