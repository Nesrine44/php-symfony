<?php

namespace AppBundle\Command;

use AppBundle\Entity\UserEntity;
use AppBundle\Entity\UserInnovationRight;
use Predis\Client;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Process\Process;

class DevelopCommand  extends ContainerAwareCommand
{

    //  docker-compose -f docker-compose.dev.yml exec php7 bin/console pri:develop
    const CMD_NAME = 'pri:develop';
    const ACTION_UPDATE_SORT_SCORE = 'update-sort-score';
    const ACTION_UPDATE_USERS_WITH_EMPLOYEE_API = 'update-users-employee-api';
    const ACTION_UPDATE_ROLE_FINANCE_CONTACT = 'update-role-finance-contact';
    const ACTION_UPDATE_UPDATE_CITY_PICTURES = 'update-city-pictures';

    private $em;
    private $redis;

    public function __construct(EntityManagerInterface $em, Client $redis)
    {
        parent::__construct();
        $this->em = $em;
        $this->redis = $redis;
    }

    protected function configure()
    {
        $this->setName(self::CMD_NAME)
            ->setDescription('Execute multiple updates on command line.')
            ->addArgument(
                'action',
                InputArgument::OPTIONAL,
                'What do you want to do?'
            )
            ->addOption(
                'force-update',
                null,
                InputOption::VALUE_OPTIONAL,
                'Would you force update datas?')
            ->addOption(
                'update-full-data',
                null,
                InputOption::VALUE_OPTIONAL,
                'Would you update full data after your update?');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $action = $input->getArgument('action');
        $force_update = $input->getArgument('force-update');
        $io = new SymfonyStyle($input, $output);
        $io->title('Action "'.$action.'" launched...');
        ini_set('memory_limit', '-1');

        switch ($action){
            case self::ACTION_UPDATE_SORT_SCORE:
                $this->updateInnovationSortScore($io);
                break;
            case self::ACTION_UPDATE_USERS_WITH_EMPLOYEE_API:
                $this->updateUsersWithEmployeeApi($io);
                break;
            case self::ACTION_UPDATE_ROLE_FINANCE_CONTACT:
                $this->updateRoleFinanceContact($io);
                break;
            case self::ACTION_UPDATE_UPDATE_CITY_PICTURES:
                $this->updateCityPictures($io, $force_update);
                break;
            default:
                $io->text("Nothing done.");
                break;
        }
        if ($input->getOption('update-full-data')) {
            $this->updateFullData($io);
        }
        $io->success("Action 100% completed!");
    }

    /**
     * Update innovation sort score
     */
    protected function updateInnovationSortScore($io)
    {
        $io->text("Attempting to update innovation sort_score...");
        $innovations = $this->em->getRepository('AppBundle:Innovation')->getAllActiveInnovations();
        $io->progressStart(count($innovations));
        foreach ($innovations as $innovation){
            $innovation->updateSortScore();
            $io->progressAdvance();
        }
        $this->em->flush();
        $this->em->clear();

        $io->progressFinish();
    }


    /**
     * Update city pictures
     *
     * @param $io
     * @param bool $force_update
     */
    protected function updateCityPictures($io, $force_update = false)
    {
        $io->text("Attempting to update city pictures...");
        $cities = $this->em->getRepository('AppBundle:City')->findAll();
        $nb = 0;
        $save = false;
        $io->progressStart(count($cities));
        foreach ($cities as $city){
            if($city->updatePictureUrl($force_update)){
                $save = true;
            }
            $io->progressAdvance();
            if(($nb % 100) === 0 && $save){
                $this->em->flush();
                $save = false;
            }
            $nb++;
        }
        $this->em->flush();
        $this->em->clear();

        $io->progressFinish();
    }

    /**
     * Update users with Employee API.
     */
    protected function updateUsersWithEmployeeApi($io)
    {
        $io->text("Attempting to update users with Employee API...");
        $all_users = $this->em->getRepository('AppBundle:User')->findAll();
        $prEmployeeApi = $this->getContainer()->get('app.pr_employee_api');
        $io->progressStart(count($all_users));
        foreach ($all_users as $a_user){
            if($a_user->getIsPrEmploye()){
                $infos = $prEmployeeApi->getUserUsedInfos($a_user);
                if($infos){
                    $a_user->setCountry($infos['country']);
                    $a_user->setSituation($infos['situation']);
                    if($infos['pr_entity']){
                        $userEntity = $this->em->getRepository('AppBundle:UserEntity')->findOneBy(array('pr_title' => $infos['pr_entity']));
                        if(!$userEntity){
                            $userEntity = new UserEntity();
                            $userEntity->setPrTitle($infos['pr_entity']);
                            $this->em->persist($userEntity);
                            $this->em->flush();
                            $a_user->setUserEntity($userEntity);
                        }else{
                            $a_user->setUserEntity($userEntity);
                        }
                    }
                }
            }
            $io->progressAdvance();
        }
        $this->em->flush();
        $this->em->clear();
        $pernodWorker = $this->getContainer()->get('AppBundle\Worker\PernodWorker');
        $pernodWorker->generateOtherDatas();
        $io->progressFinish();
    }

    /**
     * Update full data
     */
    protected function updateFullData($io)
    {
        $io->text("Attempting to update full data...");
        $pernodWorker = $this->getContainer()->get('AppBundle\Worker\PernodWorker');
        $redis_prefix = $this->getContainer()->getParameter('redis_prefix');
        $this->redis->del($redis_prefix . 'redis_cache_progress');
        $this->redis->del($redis_prefix . 'pri_otherdatas');
        $this->redis->del($redis_prefix . 'pri_allinnovations_excel');
        $this->redis->del($redis_prefix . 'pri_allinnovations');
        $this->redis->del($redis_prefix . 'pri_allconsolidation');
        $pernodWorker->initRedisCacheProgress();
        $pernodWorker->later()->generateAllInnovationsAndConsolidation(true);
    }

    /**
     * Update role finance contact
     */
    protected function updateRoleFinanceContact($io)
    {
        $io->text("Attempting to update role finance contact...");
        $innovations = $this->em->getRepository('AppBundle:Innovation')->getAllActiveInnovations();
        $io->progressStart(count($innovations));
        foreach ($innovations as $innovation){
            $userInnovationRights = $innovation->getUserInnovationRights();
            foreach($userInnovationRights as $userInnovationRight) {
                if($userInnovationRight->getRole() !== UserInnovationRight::ROLE_CONTACT_OWNER){
                    $user = $userInnovationRight->getUser();
                    $nbFinancialActivities = $innovation->getActivities()->filter(function ($activity) use ($user) {
                        if($activity->getUser() && $activity->getUser()->getId() === $user->getId()){
                            $data = $activity->getDataArray();
                            if(!array_key_exists('key', $data)){
                                return false;
                            }
                            $activityKey = $data['key'];
                            $financialKeys = array(
                                'volume_',
                                'net_sales_',
                                'contributing_margin_',
                                'central_investment_',
                                'advertising_promotion_',
                                'caap_'
                            );

                            // si $activityKey contient une des $financialKeys => return true;
                            foreach ($financialKeys as $financialKey){
                                if( strpos($activityKey, $financialKey) === 0 ){
                                    return true;
                                }
                            }
                        }
                        return false;
                    })->count();
                    if($nbFinancialActivities>0){
                        $userInnovationRight->setRole(UserInnovationRight::ROLE_FINANCE_CONTACT);
                        $this->em->persist($userInnovationRight);
                        $this->em->flush();
                    }
                }
            }
            $io->progressAdvance();
        }
        $this->em->flush();
        $this->em->clear();

        $io->progressFinish();
    }




}