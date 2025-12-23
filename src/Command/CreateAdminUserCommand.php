<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-admin-user',
    description: 'Создаёт пользователя с ролью ROLE_ADMIN для доступа в админку',
)]
class CreateAdminUserCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('phone', InputArgument::REQUIRED, 'Номер телефона пользователя')
            ->addArgument('username', InputArgument::REQUIRED, 'Имя пользователя')
            ->addOption('password', null, InputOption::VALUE_REQUIRED, 'Пароль (если не указан — будет запрошен)')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $phoneNumber = $input->getArgument('phone');
        $username = $input->getArgument("username"); 
        $plainPassword = $input->getOption('password');

        if (!$plainPassword) {
            $plainPassword = $io->askHidden('Введите пароль');
        }

        if (empty($plainPassword)) {
            $io->error('Пароль не может быть пустым.');
            return Command::FAILURE;
        }

        $existingUser = $this->entityManager->getRepository(User::class)
            ->findOneBy(['phoneNumber' => $phoneNumber]);

        if ($existingUser) {
            $io->error(sprintf('Пользователь с номером %s уже существует.', $phoneNumber));
            return Command::FAILURE;
        }

        $user = new User();
        $user->setPhoneNumber($phoneNumber);
        $user->setUsername($username); 
        $user->setRole('ROLE_ADMIN'); 

        $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($hashedPassword);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return Command::SUCCESS;
    }
}