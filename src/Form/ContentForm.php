<?php

namespace App\Form;

use App\Entity\Content;
use App\Entity\Terme;
use App\Entity\Proposition;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class ContentForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titreFr', TextType::class, [
                'label' => 'Nom',
                'label_attr' => ['class' => 'block w-full'],
                'required'=> false,
                'attr' => ['class' => 'w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500']
            ])
            ->add('titreAr', TextType::class, [
                'label' => 'اسم',
                'required'=> false,
                'label_attr' => ['class' => 'block w-full text-right', 'dir' => 'rtl'],
                'attr' => ['class' => 'w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500', 'dir' => 'rtl', 'lang' => 'ar']
            ])
            ->add('titreDr', TextType::class, [
                'label' => 'اسم',
                'required'=> false,
                'label_attr' => ['class' => 'block w-full text-right', 'dir' => 'rtl'],
                'attr' => ['class' => 'w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500', 'dir' => 'rtl', 'lang' => 'ar']
            ])


            ->add('descriptionFr', TextareaType::class, [
                'label' => 'Description',
                'label_attr' => ['class' => 'block w-full'],
                'required'=> false,
                'attr' => ['class' => 'w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 h-40'],
                // add constraint for max length
                'constraints' => [
                    new Length([
                        'max' => 300,
                        'maxMessage' => 'La description ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ]
            ])
            ->add('descriptionAr', TextareaType::class, [
                'label' => 'الوصف',
                'required'=> false,
                'label_attr' => ['class' => 'block w-full text-right', 'dir' => 'rtl'],
                'attr' => ['class' => 'w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 h-40', 'dir' => 'rtl', 'lang' => 'ar'],
                'constraints' => [
                    new Length([
                        'max' => 300,
                        'maxMessage' => 'La description ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ]
            ])
            ->add('descriptionDr', TextareaType::class, [
                'label' => 'الوصف',
                'required'=> false,
                'label_attr' => ['class' => 'block w-full text-right', 'dir' => 'rtl'],
                'attr' => ['class' => 'w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 h-40', 'dir' => 'rtl', 'lang' => 'ar'],
                'constraints' => [
                    new Length([
                        'max' => 300,
                        'maxMessage' => 'La description ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ]
            ])

            ->add('synonymeFr', TextType::class, [
                'label' => 'Synonyme',
                'label_attr' => ['class' => 'block w-full'],
                'required'=> false,
                'attr' => ['class' => 'w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500']
            ])

            ->add('synonymeAr', TextType::class, [
                'label' => 'المرادف',
                'label_attr' => ['class' => 'block w-full text-right', 'dir' => 'rtl'],
                'required'=> false,
                'attr' => ['class' => 'w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500']
            ])

            ->add('synonymeDr', TextType::class, [
                'label' => 'المرادف',
                'label_attr' => ['class' => 'block w-full text-right', 'dir' => 'rtl'],
                'required'=> false,
                'attr' => ['class' => 'w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500']
            ])

            ->add('domaine_applicationsFr', TextType::class, [
                'label' => 'Domaine d\'application',
                'label_attr' => ['class' => 'block w-full'],
                'required'=> false,
                'attr' => ['class' => 'w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500']
            ])

            ->add('domaine_applicationsAr', TextType::class, [
                'label' => 'مجال التطبيق',
                'label_attr' => ['class' => 'block w-full text-right', 'dir' => 'rtl'],
                'required'=> false,
                'attr' => ['class' => 'w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500']
            ])

            ->add('domaine_applicationsDr', TextType::class, [
                'label' => 'مجال التطبيق',
                'label_attr' => ['class' => 'block w-full text-right', 'dir' => 'rtl'],
                'required'=> false,
                'attr' => ['class' => 'w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500']
            ])

            ->add('categorieFr', TextType::class, [
                'label' => 'Catégorie',
                'label_attr' => ['class' => 'block w-full'],
                'required'=> false,
                'attr' => ['class' => 'w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500']
            ])

            ->add('categorieAr', TextType::class, [
                'label' => 'الصنف',
                'label_attr' => ['class' => 'block w-full text-right', 'dir' => 'rtl'],
                'required'=> false,
                'attr' => ['class' => 'w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500']
            ])

            ->add('categorieDr', TextType::class, [
                'label' => 'الصنف',
                'label_attr' => ['class' => 'block w-full text-right', 'dir' => 'rtl'],
                'required'=> false,
                'attr' => ['class' => 'w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500']
            ])

            ->add('sourceFr', TextType::class, [
                'label' => 'Source',
                'label_attr' => ['class' => 'block w-full'],
                'required'=> false,
                'attr' => ['class' => 'w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500']
            ])

            ->add('sourceAr', TextType::class, [
                'label' => 'المصدر',
                'label_attr' => ['class' => 'block w-full text-right', 'dir' => 'rtl'],
                'required'=> false,
                'attr' => ['class' => 'w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500']
            ])

            ->add('sourceDr', TextType::class, [
                'label' => 'المصدر',
                'label_attr' => ['class' => 'block w-full text-right', 'dir' => 'rtl'],
                'required'=> false,
                'attr' => ['class' => 'w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500']
            ])

            ->add('categorie_grammaticaleFr', TextType::class, [
                'label' => 'Catégorie grammaticale',
                'label_attr' => ['class' => 'block w-full'],
                'required'=> false,
                'attr' => ['class' => 'w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500']
            ])

            ->add('categorie_grammaticaleAr', TextType::class, [
                'label' => 'الفئة النحوية',
                'label_attr' => ['class' => 'block w-full text-right', 'dir' => 'rtl'],
                'required'=> false,
                'attr' => ['class' => 'w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500']
            ])

            ->add('categorie_grammaticaleDr', TextType::class, [
                'label' => 'الفئة النحوية',
                'label_attr' => ['class' => 'block w-full text-right', 'dir' => 'rtl'],
                'required'=> false,
                'attr' => ['class' => 'w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500']
            ])

            ->add('relation_terminologiqueFr', TextType::class, [
                'label' => 'Relation terminologique',
                'label_attr' => ['class' => 'block w-full'],
                'required'=> false,
                'attr' => ['class' => 'w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500']
            ])

            ->add('relation_terminologiqueAr', TextType::class, [
                'label' => 'العلاقة المصطلحية',
                'label_attr' => ['class' => 'block w-full text-right', 'dir' => 'rtl'],
                'required'=> false,
                'attr' => ['class' => 'w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500']
            ])

            ->add('relation_terminologiqueDr', TextType::class, [
                'label' => 'العلاقة المصطلحية',
                'label_attr' => ['class' => 'block w-full text-right', 'dir' => 'rtl'],
                'required'=> false,
                'attr' => ['class' => 'w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500']
            ])

            ->add('equivalent_anglaisFr', TextType::class, [
                'label' => 'Équivalent anglais',
                'label_attr' => ['class' => 'block w-full'],
                'required'=> false,
                'attr' => ['class' => 'w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500']
            ])

            ->add('equivalent_anglaisAr', TextType::class, [
                'label' => 'المرادف بالإنجليزية',
                'label_attr' => ['class' => 'block w-full text-right', 'dir' => 'rtl'],
                'required'=> false,
                'attr' => ['class' => 'w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500']
            ])

            ->add('equivalent_anglaisDr', TextType::class, [
                'label' => 'المرادف بالإنجليزية',
                'label_attr' => ['class' => 'block w-full text-right', 'dir' => 'rtl'],
                'required'=> false,
                'attr' => ['class' => 'w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500']
            ])

            ->add('equivalent_espagnolFr', TextType::class, [
                'label' => 'Équivalent espagnol',
                'label_attr' => ['class' => 'block w-full'],
                'required'=> false,
                'attr' => ['class' => 'w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500']
            ])

            ->add('equivalent_espagnolAr', TextType::class, [
                'label' => 'المرادف بالإسبانية',
                'label_attr' => ['class' => 'block w-full text-right', 'dir' => 'rtl'],
                'required'=> false,
                'attr' => ['class' => 'w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500']
            ])

            ->add('equivalent_espagnolDr', TextType::class, [
                'label' => 'المرادف بالإسبانية',
                'label_attr' => ['class' => 'block w-full text-right', 'dir' => 'rtl'],
                'required'=> false,
                'attr' => ['class' => 'w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500']
            ])
            ->add('idiomeFr', TextType::class, [
                'label' => 'Idiome',
                'label_attr' => ['class' => 'block w-full'],
                'required'=> false,
                'attr' => ['class' => 'w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500']
            ])
            ->add('idiomeAr', TextType::class, [
                'label' => 'اللغة',
                'label_attr' => ['class' => 'block w-full text-right', 'dir' => 'rtl'],
                'required'=> false,
                'attr' => ['class' => 'w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500']
            ])
            ->add('idiomeDr', TextType::class, [
                'label' => 'اللغة',
                'label_attr' => ['class' => 'block w-full text-right', 'dir' => 'rtl'],
                'required'=> false,
                'attr' => ['class' => 'w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500']
            ])
            ->add('usage_metaphoriqueFr', TextType::class, [
                'label' => 'Usage metaphorique',
                'label_attr' => ['class' => 'block w-full'],
                'required'=> false,
                'attr' => ['class' => 'w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500']
            ])
            ->add('usage_metaphoriqueAr', TextType::class, [
                'label' => 'تعبير مجازي',
                'label_attr' => ['class' => 'block w-full text-right', 'dir' => 'rtl'],
                'required'=> false,
                'attr' => ['class' => 'w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500']
            ])
            ->add('usage_metaphoriqueDr', TextType::class, [
                'label' => 'تعبير مجازي',
                'label_attr' => ['class' => 'block w-full text-right', 'dir' => 'rtl'],
                'required'=> false,
                'attr' => ['class' => 'w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500']
            ])
            ->add('recit_vieFr', TextareaType::class, [
                'label' => 'Récit de vie',
                'required'=> false,
                'label_attr' => ['class' => 'block w-full'],
                'attr' => ['class' => 'w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 h-40'],
                'constraints' => [
                    new Length([
                        'max' => 500,
                        'maxMessage' => 'La description ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ]
            ])
            ->add('recit_vieAr', TextareaType::class, [
                'label' => 'قصة حياة',
                'required'=> false,
                'label_attr' => ['class' => 'block w-full text-right', 'dir' => 'rtl'],
                'attr' => ['class' => 'w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 h-40', 'dir' => 'rtl', 'lang' => 'ar'],
                'constraints' => [
                    new Length([
                        'max' => 500,
                        'maxMessage' => 'La description ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ]
            ])
            ->add('recit_vieDr', TextareaType::class, [
                'label' => 'قصة حياة',
                'required'=> false,
                'label_attr' => ['class' => 'block w-full text-right', 'dir' => 'rtl'],
                'attr' => ['class' => 'w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 h-40', 'dir' => 'rtl', 'lang' => 'ar'],
                'constraints' => [
                    new Length([
                        'max' => 500,
                        'maxMessage' => 'La description ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ]
            ])
            ->add('liens_hypertexteFr', TextareaType::class, [
                'label' => 'Liens hypertextes',
                'required'=> false,
                'label_attr' => ['class' => 'block w-full'],
                'attr' => ['class' => 'w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 h-40'],
                'constraints' => [
                    new Length([
                        'max' => 500,
                        'maxMessage' => 'La description ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ]
            ])
            ->add('liens_hypertexteAr', TextareaType::class, [
                'label' => 'روابط الويب',
                'required'=> false,
                'label_attr' => ['class' => 'block w-full text-right', 'dir' => 'rtl'],
                'attr' => ['class' => 'w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 h-40', 'dir' => 'rtl', 'lang' => 'ar'],
                'constraints' => [
                    new Length([
                        'max' => 500,
                        'maxMessage' => 'La description ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ]
            ])
            ->add('liens_hypertexteDr', TextareaType::class, [
                'label' => 'روابط الويب',
                'required'=> false,
                'label_attr' => ['class' => 'block w-full text-right', 'dir' => 'rtl'],
                'attr' => ['class' => 'w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 h-40', 'dir' => 'rtl', 'lang' => 'ar'],
                'constraints' => [
                    new Length([
                        'max' => 500,
                        'maxMessage' => 'La description ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ]
            ])



        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Content::class,
        ]);
    }
}
