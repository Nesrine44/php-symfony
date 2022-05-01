<?php

namespace PrAuthBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class PrAuthUpdateUsersCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('pr-auth:update-users')
            ->setDescription('Update all users allowing them to use MyPortal authentication if it\'s possible.');
    }



    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title("Attempting to update all users...");
        ini_set('memory_limit', '-1');
        $this->updateAllUsers($io);
        $io->success("Update 100% completed!");
    }


    /**
     * Update all users
     */
    protected function updateAllUsers($io)
    {
        $userManager = $this->getApplication()->getKernel()->getContainer()->get('fos_user.user_manager');
        $users = $userManager->findUsers();
        $secret_key = $this->getApplication()->getKernel()->getContainer()->getParameter('pr_auth')['password_key'];
        $roles = $this->getApplication()->getKernel()->getContainer()->getParameter('pr_auth')['default_roles'];
        $removable_roles = $this->getApplication()->getKernel()->getContainer()->getParameter('pr_auth')['removable_roles'];
        $io->progressStart(count($users));
        foreach ($users as $user) {
            if ($user->hasPernodRicardEmail()){
                $user->setIsPrEmploye(true);
                $user->setPlainPassword($user->getGeneratedLocalPassword($secret_key));
                $user->setEnabled(1); // enable the user or enable it later with a confirmation token in the email
                if(!$user->getFirsname() && !$user->getLastname()){
                    $user->generateFirstnameFromEmail();
                }
                foreach ($removable_roles as $removable_role){
                    $user->removeRole($removable_role);
                }
                foreach ($roles as $role){
                    $user->addRole($role);
                }
                $userManager->updateUser($user);
                $io->text($user->getProperUsername()." updated");
            }
            $io->progressAdvance();
        }
        $io->progressFinish();
    }
    


}
