<?php

namespace App\Form;

use App\Entity\Contenu;
use App\Entity\ContenuNumero;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContenuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
   ->add('contenuNumeros', EntityType::class, [
    'class' => ContenuNumero::class,
    'choice_label' => function($cn) {
        return $cn->getTitle(); // titre unique
    },
    'multiple' => true,
    'expanded' => false,
    'by_reference' => false,
    'required' => false,
    'attr' => [
        'class' => 'js-select2 w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500',
    ],
    'query_builder' => function (EntityRepository $er) {
        return $er->createQueryBuilder('c')
                  ->orderBy('c.title', 'ASC');
    },
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
            'data_class' => Contenu::class,
        ]);
    }
}
