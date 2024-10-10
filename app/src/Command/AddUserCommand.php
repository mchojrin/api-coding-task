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

#[AsCommand(
    name: 'app:add-user',
    description: 'Adds a user to the database',
)]
class AddUserCommand extends Command
{
    public function __construct(private readonly EntityManagerInterface $entityManager, string $name = null)
    {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::OPTIONAL, 'The new user\'s email')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $email = $input->getArgument('email');

        if (empty($email)) {
            $email = $io->ask("What is the new user's email?");
        }

        $user = new User();
        $user->setEmail($email);
        $user->setPassword("Not important");
        $user->setToken(bin2hex(random_bytes(16)));
        $io->info('The token is '.$user->getToken());
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success('You have a new user. The token is "'.$user->getToken().'"');

        return Command::SUCCESS;
    }
}
