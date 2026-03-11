<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Create admin and Propriétaire users.
 *
 * @author Stephane H.
 * @created 2026-03-11
 *
 * @inputs  email, password, role (admin|proprietaire)
 * @outputs User created in database
 */
#[AsCommand(
    name: 'app:create-user',
    description: 'Create admin or Propriétaire user',
)]
class CreateUserCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'User email')
            ->addArgument('password', InputArgument::REQUIRED, 'Plain password')
            ->addArgument('role', InputArgument::REQUIRED, 'Role: admin or proprietaire');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');
        $role = strtolower($input->getArgument('role'));

        $roleClass = match ($role) {
            'admin' => 'ROLE_ADMIN',
            'proprietaire' => 'ROLE_PROPRIETAIRE',
            default => null,
        };

        if (!$roleClass) {
            $io->error('Role must be "admin" or "proprietaire"');
            return Command::FAILURE;
        }

        $existing = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($existing) {
            $io->error("User {$email} already exists.");
            return Command::FAILURE;
        }

        $user = new User();
        $user->setEmail($email);
        $user->setRoles([$roleClass]);
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));

        $this->em->persist($user);
        $this->em->flush();

        $io->success("User {$email} created with role {$roleClass}.");

        return Command::SUCCESS;
    }
}
