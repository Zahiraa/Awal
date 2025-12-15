<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\CallbackTransformer;

class AdminUserForm extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Nom ne peut pas être vide.',
                    ]),
                ]
            ])
            ->add('prenom', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Prénom ne peut pas être vide.',
                    ]),
                ]
            ]);

        $isAdmin = $this->security->isGranted('ROLE_ADMIN');

        /** @var User $connectedUser */
        $connectedUser = $this->security->getUser();

        $isCurrentUser = $options['data']?->getId() === $connectedUser->getId();
        
        if ($isAdmin && !$isCurrentUser) {
            // Get the highest role for the user
            $userRoles = $options['data']->getRoles();

            $defaultRole = 'ROLE_USER';
            if (in_array('ROLE_ADMIN', $userRoles)) {
                $defaultRole = 'ROLE_ADMIN';
            } elseif (in_array('ROLE_MANAGER', $userRoles)) {
                $defaultRole = 'ROLE_MANAGER';
            }

            $builder->add('roles', ChoiceType::class, [
                'choices' => [
                    'Admin' => 'ROLE_ADMIN',
                    "Manager" => 'ROLE_MANAGER',
                    'Utilisateur' => 'ROLE_USER',
                ],
                'expanded' => false,
                'multiple' => false,
                'data' => $defaultRole,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez sélectionner un rôle.',
                    ]),
                ],
            ]);

            // Add data transformer for roles
            $builder->get('roles')
                ->addModelTransformer(new CallbackTransformer(
                    function ($rolesArray) {
                        return $rolesArray;
                    },
                    function ($roleString) {
                        return [$roleString];
                    }
                ));
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
