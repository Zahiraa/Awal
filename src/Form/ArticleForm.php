<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Categorie;
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

class ArticleForm extends AbstractType
{
    private CategorieRepository $categorieRepository;

    public function __construct(CategorieRepository $categorieRepository)
    {
        $this->categorieRepository = $categorieRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $article = $options['data'] ?? null;
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
            ->add('titre', TextType::class, [
                'label' => 'Titre',
                'label_attr' => [
                    'class' => 'text-lg text-gray-700'
                ],
                'attr' => [
                    'class' => 'form-input block w-full mt-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 h-auto px-2 py-2'
                ]
            ])
            ->add('categories', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'nom',
                'choice_value' => 'id',
                'choice_attr' => function(Categorie $categorie) {
                    return [
                        'data-bg-class' => $categorie->getBgClass(),
                        'data-text-class' => $categorie->getTextClass(),
                        'data-color' => $categorie->getColor() ?: 'gray' // Conserver data-color pour compatibilité
                    ];
                },
                'multiple' => true,
                'expanded' => false,
                'required' => false,
                'label_attr' => [
                    'class' => 'text-lg text-gray-700'
                ],
                'attr' => [
                    'class' => 'js-select2 form-multiselect block w-full mt-1 rounded-md border-gray-300 shadow-sm h-auto py-2',
                    'data-placeholder' => 'Sélectionnez des catégories'
                ],
                'by_reference' => false
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Contenu',
                'label_attr' => [
                    'class' => 'text-lg text-gray-700'
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
            'data_class' => Article::class,
        ]);
    }
}
