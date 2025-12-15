<?php

namespace App\Manager;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserManager
{


    public function __construct(
        private readonly EntityManagerInterface      $entityManager,
        private readonly Mailler                     $mailler,
        private readonly UserPasswordHasherInterface $passwordHasher
    )
    {
    }


    public function completeRegistration(User $user, array  $userData): array
    {
        // Mettre à jour les informations de l'utilisateur
        $user->setPassword($this->passwordHasher->hashPassword($user, $userData['plainPassword']));
        $user->setNom($userData['nom']);
        $user->setPrenom($userData['prenom']);
        $user->setRegistrationToken(null);
        $user->setIsVerified(true);
        $user->setStatut(User::ACTIVE);

        try {
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return [
                'success' => true,
                'message' => 'Votre compte a été créé avec succès. Vous pouvez maintenant vous connecter.'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Une erreur est survenue lors de la création de votre compte.'
            ];
        }
    }

    public function inviteNewUsers(array $invitations): array
    {
        $invitationsFiltered = [];
        $messages = [];
        foreach ($invitations as $invitation) {
            $email = isset($invitation['email']) ? trim($invitation['email']) : '';
            $role = isset($invitation['role']) ? trim($invitation['role']) : '';

            // Ignorer si email vide, role vide ou email déjà vu
            if (empty($email) || empty($role) || isset($seenEmails[$email])) {
                continue;
            }

            // check if email is in database
            $existingUser = $this->entityManager->getRepository(User::class)->checkUser($email);
            if ($existingUser) {
                $messages[] = ['status' => 'warning', 'message' => sprintf('L\'email %s est déjà associé à un compte existant.', $email)];
                continue;
            }
            $userAlreadyInvited = $this->entityManager->getRepository(User::class)->invitedUser($email);

            if ($userAlreadyInvited) {
                $messages[] = ['status' => 'warning', 'message' => sprintf('L\'email %s a déjà été invité.', $email)];
                continue;
            }

            $seenEmails[$email] = true;
            $invitationsFiltered[$email] = $role;
        }
        if ($invitationsFiltered) {
            foreach ($invitationsFiltered as $email => $role) {
                $user = $this->createUser($email, $role);

                $this->mailler->sendTemplateEmail(
                    $email,
                    'Invitation à rejoindre la plateforme',
                    'emails/security/invitation_email.html.twig',
                    [
                        'userEmail' => $email,
                        'role' => str_replace('ROLE_', '', $role),
                        'token' => $user->getRegistrationToken()
                    ]
                );
            }
            $messages[] = ["status" => "success", "message" => sprintf('%d invitation(s) envoyée(s) avec succès.', count($invitationsFiltered))];
        }
        return $messages;
    }

    public function createUser($email, $role)
    {
        $user = new User();
        $user->setEmail($email);
        $user->setRoles([$role]);
        $user->setStatut(User::INACTIVE);
        $user->setIsVerified(0);
        $user->setInvitedAt(new \DateTime());

        $user->setRegistrationToken(bin2hex(random_bytes(32)));
        $password = bin2hex(random_bytes(8));

        $user->setPassword(password_hash($password, PASSWORD_BCRYPT));
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        return $user;
    }
}
