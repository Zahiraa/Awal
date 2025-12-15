<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Categorie;
use App\Entity\Cgu;
use App\Entity\File;
use App\Entity\User;
use App\Form\DataTransformer\CategoriesToArrayTransformer;
use App\Repository\CategorieRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CguForm extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('title', TextType::class, [
                'label' => 'Titre',
                'required' => true,
                'label_attr' => [
                    'class' => 'text-lg text-gray-700'
                ],
                'attr' => [
                    'class' => 'form-input block w-full mt-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 h-auto px-2 py-2'
                ]
            ])
            ->add('short_description', TextType::class, [
                'required' => false,
                'label' => 'Description courte',
                'label_attr' => [
                    'class' => 'text-lg text-gray-700'
                ],
                'attr' => [
                    'class' => 'form-input block w-full mt-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 h-auto px-2 py-2'
                ]
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Contenu',
                'required' => true,
                'label_attr' => [
                    'class' => 'text-lg text-gray-700'
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cgu::class,
        ]);
    }
}
