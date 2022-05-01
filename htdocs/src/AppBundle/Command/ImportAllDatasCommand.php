<?php

namespace AppBundle\Command;

use AppBundle\Entity\Activity;
use AppBundle\Entity\AdditionalPicture;
use AppBundle\Entity\Brand;
use AppBundle\Entity\BusinessDriver;
use AppBundle\Entity\Classification;
use AppBundle\Entity\ConsumerOpportunity;
use AppBundle\Entity\Entity;
use AppBundle\Entity\FinancialData;
use AppBundle\Entity\Innovation;
use AppBundle\Entity\MomentOfConsumption;
use AppBundle\Entity\PerformanceReview;
use AppBundle\Entity\Picture;
use AppBundle\Entity\PortfolioProfile;
use AppBundle\Entity\Settings;
use AppBundle\Entity\Stage;
use AppBundle\Entity\Type;
use AppBundle\Entity\User;
use AppBundle\Entity\UserInnovationRight;
use AppBundle\Worker\PernodWorker;
use League\Csv\Reader;
use Predis\Client;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Process\Process;
use AppBundle\Event\InnovationEvent;
use AppBundle\Event\SettingsEvent;

class ImportAllDatasCommand  extends ContainerAwareCommand
{

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
        $this->setName('pri:import-all-datas')
            ->setDescription('Import all Innovation Pipeline data.')
            ->addOption('with-pictures', null, InputOption::VALUE_NONE, 'Would you import picture with rsync?')
            ->addOption('only-redis', null, InputOption::VALUE_NONE, 'Would you only update redis datas?')
            ->addOption('only-created_at', null, InputOption::VALUE_NONE, 'Would you only update innovation created_at?');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title("Attempting to import all Innovation Pipeline...");
        ini_set('memory_limit', '-1');

        if($input->getOption('only-created_at')){
            $this->postUpdateAllInnovationsCreatedAt($io);
        }elseif($input->getOption('only-redis')){
            $this->generateRedisDatas($io);
        }else {
            if ($input->getOption('with-pictures')) {
                $this->rsyncPictures($io);
            }
            $this->generateSettings($io);
            $this->importStages($io);
            $this->importBusinessDrivers($io);
            $this->importClassifications($io);
            $this->importConsumerOpportunities($io);
            $this->importMomentOfConsumptions($io);
            $this->importPortfolioProfiles($io);
            $this->importTypes($io);
            $this->importBrands($io);
            $this->importEntities($io);
            $this->importUsers($io);
            $this->importInnovations($io);
            $this->importFinancialDatas($io);
            $this->importPerformanceReviews($io);
            $this->importActivities($io);
            $this->importUserInnovationRights($io);
            $this->postUpdateAllInnovations($io);
            $this->postUpdateAllInnovationsCreatedAt($io);
            $this->updateUsersLastLogin($io);
            $this->generateRedisDatas($io);
        }
        $io->success("Import 100% completed!");
    }


    /**
     * Generate global website settings
     */
    protected function generateSettings($io)
    {
        $io->text("Attempting to generate website settings...");
        $object = $this->em->getRepository('AppBundle:Settings')
            ->findOneById(1);
        if (!$object) {
            $object = new Settings();
        }
        $object->setId(1);
        $object->setContactEmail("emily.lararosales@pernod-ricard.com");
        $object->setCurrentFinancialDate("2018-04-15");
        $object->setCurrentTrimester(4);
        $object->setOpenDate("2018-05-28");
        $object->setCloseDate("2018-06-06");
        $object->setOpenDateLibelle("May 28th");
        $object->setCloseDateLibelle("June 6th 2018");
        $object->setIsEditionQualiEnabled(true);
        $object->setIsProjectCreationEnabled(true);
        $object->setPing(new \DateTime());
        $object->updateCacheVersion();
        $this->em->persist($object);
        $this->em->flush();
        $this->em->clear();
    }

    /**
     * Import all pictures
     */
    protected function rsyncPictures($io)
    {
        $username = 'live.e0c68ae3-2164-4da3-b4ce-34fa9c8e8500';
        $root_dir = $this->getApplication()->getKernel()->getContainer()->get('kernel')->getRootDir();
        $upload_path = $root_dir . "/../web/uploads/";
        $io->text("Attempting to import all pictures in " . $upload_path);
        $exclude_path = $root_dir . "/../src/AppBundle/Data/rsync_pictures_exclude.txt";
        // rsync -rvlz --copy-unsafe-links --size-only --ipv4 --progress -e 'ssh -p 2222' live.e0c68ae3-2164-4da3-b4ce-34fa9c8e8500@appserver.live.e0c68ae3-2164-4da3-b4ce-34fa9c8e8500.drush.in:code/sites/default/files/ /var/www/symfony/app/../web/uploads/
        $process = new Process('rsync -rvlz --exclude-from=' . $exclude_path . ' --copy-unsafe-links --size-only --ipv4 --progress -e \'ssh -p 2222\' ' . $username . '@appserver.' . $username . '.drush.in:code/sites/default/files/ /var/www/symfony/app/../web/uploads/');
        $process->setTimeout(7200);
        $process->run(function ($type, $buffer) {
            if (Process::ERR === $type) {
                echo 'ERR > ' . $buffer;
            } else {
                echo 'OUT > ' . $buffer;
            }
        });
    }


    /**
     * Import all stages
     */
    protected function importStages($io)
    {
        $io->text("Attempting to import all stages...");
        $csv = Reader::createFromPath('%kernel.root_dir%/../src/AppBundle/Data/stage.csv');
        $csv->setHeaderOffset(0); //set the CSV header offset
        $io->progressStart(iterator_count($csv));
        foreach ($csv as $row) {
            $object = $this->em->getRepository('AppBundle:Stage')
                ->findOneById($row['id']);
            if (!$object) {
                $object = new Stage();
            }
            $object->setId(intval($row['id']));
            $object->setTitle($row['title']);
            $object->setCssClass($row['css_class']);
            $this->em->persist($object);
            $io->progressAdvance();
        }
        $this->em->flush();
        $this->em->clear();
        $io->progressFinish();
    }

    /**
     * Import all business drivers
     */
    protected function importBusinessDrivers($io)
    {
        $io->text("Attempting to import all business drivers...");
        $csv = Reader::createFromPath('%kernel.root_dir%/../src/AppBundle/Data/business_driver.csv');
        $csv->setHeaderOffset(0); //set the CSV header offset
        $io->progressStart(iterator_count($csv));
        foreach ($csv as $row) {
            $object = $this->em->getRepository('AppBundle:BusinessDriver')
                ->findOneById($row['id']);
            if (!$object) {
                $object = new BusinessDriver();
            }
            $object->setId(intval($row['id']));
            $object->setTitle($row['title']);
            $object->setCssClass($row['css_class']);
            $this->em->persist($object);
            $io->progressAdvance();
        }
        $this->em->flush();
        $this->em->clear();
        $io->progressFinish();
    }

    /**
     * Import all classifications
     */
    protected function importClassifications($io)
    {
        $io->text("Attempting to import all classifications...");
        $csv = Reader::createFromPath('%kernel.root_dir%/../src/AppBundle/Data/classification.csv');
        $csv->setHeaderOffset(0); //set the CSV header offset
        $io->progressStart(iterator_count($csv));
        foreach ($csv as $row) {
            $object = $this->em->getRepository('AppBundle:Classification')
                ->findOneById($row['id']);
            if (!$object) {
                $object = new Classification();
            }
            $object->setId(intval($row['id']));
            $object->setTitle($row['title']);
            $object->setCssClass($row['css_class']);
            $this->em->persist($object);
            $io->progressAdvance();
        }
        $this->em->flush();
        $this->em->clear();
        $io->progressFinish();
    }

    /**
     * Import all consumer opportunities
     */
    protected function importConsumerOpportunities($io)
    {
        $io->text("Attempting to import all consumer opportunities...");
        $csv = Reader::createFromPath('%kernel.root_dir%/../src/AppBundle/Data/consumer_opportunity.csv');
        $csv->setHeaderOffset(0); //set the CSV header offset
        $io->progressStart(iterator_count($csv));
        foreach ($csv as $row) {
            $object = $this->em->getRepository('AppBundle:ConsumerOpportunity')
                ->findOneById($row['id']);
            if (!$object) {
                $object = new ConsumerOpportunity();
            }
            $object->setId(intval($row['id']));
            $object->setTitle($row['title']);
            $object->setCssClass($row['css_class']);
            $this->em->persist($object);
            $io->progressAdvance();
        }
        $this->em->flush();
        $this->em->clear();
        $io->progressFinish();
    }

    /**
     * Import all moment of consumptions
     */
    protected function importMomentOfConsumptions($io)
    {
        $io->text("Attempting to import all moment of convivialité...");
        $csv = Reader::createFromPath('%kernel.root_dir%/../src/AppBundle/Data/moment_of_consumption.csv');
        $csv->setHeaderOffset(0); //set the CSV header offset
        $io->progressStart(iterator_count($csv));
        foreach ($csv as $row) {
            $object = $this->em->getRepository('AppBundle:MomentOfConsumption')
                ->findOneById($row['id']);
            if (!$object) {
                $object = new MomentOfConsumption();
            }
            $object->setId(intval($row['id']));
            $object->setTitle($row['title']);
            $object->setCssClass($row['css_class']);
            $this->em->persist($object);
            $io->progressAdvance();
        }
        $this->em->flush();
        $this->em->clear();
        $io->progressFinish();
    }

    /**
     * Import all portfolio profiles
     */
    protected function importPortfolioProfiles($io)
    {
        $io->text("Attempting to import all consumer portfolio profiles...");
        $csv = Reader::createFromPath('%kernel.root_dir%/../src/AppBundle/Data/portfolio_profile.csv');
        $csv->setHeaderOffset(0); //set the CSV header offset
        $io->progressStart(iterator_count($csv));
        foreach ($csv as $row) {
            $object = $this->em->getRepository('AppBundle:PortfolioProfile')
                ->findOneById($row['id']);
            if (!$object) {
                $object = new PortfolioProfile();
            }
            $object->setId(intval($row['id']));
            $object->setTitle($row['title']);
            $object->setCssClass($row['css_class']);
            $this->em->persist($object);
            $io->progressAdvance();
        }
        $this->em->flush();
        $this->em->clear();
        $io->progressFinish();
    }

    /**
     * Import all Types
     */
    protected function importTypes($io)
    {
        $io->text("Attempting to import all types...");
        $csv = Reader::createFromPath('%kernel.root_dir%/../src/AppBundle/Data/type.csv');
        $csv->setHeaderOffset(0); //set the CSV header offset
        $io->progressStart(iterator_count($csv));
        foreach ($csv as $row) {
            $object = $this->em->getRepository('AppBundle:Type')
                ->findOneById($row['id']);
            if (!$object) {
                $object = new Type();
            }
            $object->setId(intval($row['id']));
            $object->setTitle($row['title']);
            $object->setCssClass($row['css_class']);
            $this->em->persist($object);
            $io->progressAdvance();
        }
        $this->em->flush();
        $this->em->clear();
        $io->progressFinish();
    }

    /**
     * Import all users
     */
    protected function importUsers($io)
    {
        $io->text("Attempting to import all users...");
        $csv = Reader::createFromPath('%kernel.root_dir%/../src/AppBundle/Data/user.csv');
        $csv->setHeaderOffset(0); //set the CSV header offset
        $io->progressStart(iterator_count($csv));
        $userManager = $this->getApplication()->getKernel()->getContainer()->get('fos_user.user_manager');
        foreach ($csv as $row) {
            $email_exist = $userManager->findUserByEmail($row['email']);
            if ($email_exist) {
                continue;
            }
            $secret_key = $this->getApplication()->getKernel()->getContainer()->getParameter('pr_auth')['password_key'];
            /* @var User $user */
            $user = $userManager->createUser();
            $user->setOldId($row['old_id']);
            $user->setUsername($row['email']);
            $user->setEmail($row['email']);
            $user->setEmailCanonical($row['email']);
            $user->setEnabled(1); // enable the user or enable it later with a confirmation token in the email
            $user->addRole($row['role']);
            $user->setIsPrEmploye($row['is_pr_employe']);
            $user->setAcceptNewsletter($row['accept_newsletter']);
            $user->setIsVideoEnabled($row['is_video_enabled']);
            $user->setPlainPassword($user->getGeneratedLocalPassword($secret_key));
            $user->setLastLogin(new \DateTime($row['access']));
            $user->importCreatedAt(new \DateTime($row['created']));
            $user->generateFirstnameFromEmail();
            $userManager->updateUser($user);

            $io->progressAdvance();
        }
        $this->em->flush();
        $this->em->clear();
        $io->progressFinish();
    }

    /**
     * Update all users last_login
     */
    protected function updateUsersLastLogin($io)
    {
        $io->text("Attempting to update all users last_login...");
        $csv = Reader::createFromPath('%kernel.root_dir%/../src/AppBundle/Data/user.csv');
        $csv->setHeaderOffset(0); //set the CSV header offset
        $io->progressStart(iterator_count($csv));
        foreach ($csv as $row) {
            $user = $this->em->getRepository('AppBundle:User')->findOneBy(array('old_id' => $row['old_id']));
            if($user){
                $user->setLastLogin(new \DateTime($row['access']));
            }
            $io->progressAdvance();
        }
        $this->em->flush();
        $this->em->clear();
        $io->progressFinish();
    }

    /**
     * Import all Brands
     */
    protected function importBrands($io)
    {
        $io->text("Attempting to import all brands...");
        $csv = Reader::createFromPath('%kernel.root_dir%/../src/AppBundle/Data/brand.csv');
        $csv->setHeaderOffset(0); //set the CSV header offset
        $io->progressStart(iterator_count($csv));
        foreach ($csv as $row) {
            $object = $this->em->getRepository('AppBundle:Brand')
                ->findOneBy(['old_id' => $row['old_id']]);
            if (!$object) {
                $object = new Brand();
            }
            $object->setOldId(intval($row['old_id']));
            $object->setTitle($row['title']);
            $object->setGroupId($row['group_id']);
            $this->em->persist($object);
            $io->progressAdvance();
        }
        $this->em->flush();
        $this->em->clear();
        $io->progressFinish();
    }

    /**
     * Import all Entities
     */
    protected function importEntities($io)
    {
        $io->text("Attempting to import all entities...");
        $csv = Reader::createFromPath('%kernel.root_dir%/../src/AppBundle/Data/entity.csv');
        $csv->setHeaderOffset(0); //set the CSV header offset
        $io->progressStart(iterator_count($csv));
        foreach ($csv as $row) {
            $object = $this->em->getRepository('AppBundle:Entity')
                ->findOneBy(['old_id' => $row['old_id']]);
            if (!$object) {
                $object = new Entity();
            }
            $object->setOldId(intval($row['old_id']));
            $object->setTitle($row['title']);
            $this->em->persist($object);
            $io->progressAdvance();
        }
        $this->em->flush();
        $this->em->clear();
        $io->progressFinish();
    }


    /**
     * Import all Innovations
     */
    protected function importInnovations($io)
    {
        $io->text("Attempting to import all innovations...");
        $csv = Reader::createFromPath('%kernel.root_dir%/../src/AppBundle/Data/innovation.csv');
        $csv->setHeaderOffset(0); //set the CSV header offset
        $io->progressStart(iterator_count($csv));
        foreach ($csv as $row) {
            $object = $this->em->getRepository('AppBundle:Innovation')
                ->findOneBy(['old_id' => $row['old_id']]);
            if (!$object) {
                $object = new Innovation();
            }
            $object->setOldId(intval($row['old_id']));
            $object->setTitle($row['title']);
            if($row['in_market_date']) {
                $object->setInMarketDate(new \DateTime($row['in_market_date']));
            }else{
                $object->setInMarketDate(null);
            }
            if($row['start_date']) {
                $object->setStartDate(new \DateTime($row['start_date']));
            }else{
                $object->setStartDate(null);
            }

            // Contact
            $contact = $this->em->getRepository('AppBundle:User')
                ->findOneBy(['old_id' => $row['contact_old_id']]);
            if ($contact) {
                $object->setContact($contact);
            }
            // Stage
            $stage = $this->em->getRepository('AppBundle:Stage')
                ->findOneById($row['stage_id']);
            if ($stage) {
                $object->setStage($stage);
            }
            // Type
            $type = $this->em->getRepository('AppBundle:Type')
                ->findOneById($row['type_id']);
            if ($type) {
                $object->setType($type);
            }
            // Classification
            $classification = $this->em->getRepository('AppBundle:Classification')
                ->findOneById($row['classification_id']);
            if ($classification) {
                $object->setClassification($classification);
            }
            // new_to_the_world
            $object->setIsNewToTheWorld($row['is_new_to_the_world']);
            // is_frozen
            $object->setIsFrozen($row['is_frozen']);
            // consumer_opportunity
            $consumer_opportunity = $this->em->getRepository('AppBundle:ConsumerOpportunity')
                ->findOneById($row['consumer_opportunity_id']);
            if ($consumer_opportunity) {
                $object->setConsumerOpportunity($consumer_opportunity);
            }
            // moment_of_consumption_id
            $moment_of_consumption = $this->em->getRepository('AppBundle:MomentOfConsumption')
                ->findOneById($row['moment_of_consumption_id']);
            if ($moment_of_consumption) {
                $object->setMomentOfConsumption($moment_of_consumption);
            }
            // business_driver_id
            $business_driver = $this->em->getRepository('AppBundle:BusinessDriver')
                ->findOneById($row['business_driver_id']);
            if ($business_driver) {
                $object->setBusinessDriver($business_driver);
            }
            // portfolio_profile_id
            $portfolio_profile = $this->em->getRepository('AppBundle:PortfolioProfile')
                ->findOneById($row['portfolio_profile_id']);
            if ($portfolio_profile) {
                $object->setPortfolioProfile($portfolio_profile);
            }
            // entity_old_id
            $entity = $this->em->getRepository('AppBundle:Entity')
                ->findOneBy(['old_id' => $row['entity_old_id']]);
            if ($entity) {
                $object->setEntity($entity);
            }
            $object->setCategory($row['category']);
            $object->setIsMultiBrand($row['is_multi_brand']);
            $object->setIsReplacingExistingProduct($row['is_replacing_existing_product']);
            $object->setReplacingProduct($row['replacing_product']);
            $object->setIsInPrisma($row['is_in_prisma']);
            $object->setWhyInvestInThisInnovation($row['why_invest_in_this_innovation']);
            $object->setUniqueExperience($row['unique_experience']);
            $object->setStory($row['story']);
            $object->setUniqueness($row['uniqueness']);
            $object->setConsumerInsight($row['consumer_insight']);
            $object->setEarlyAdopterPersona($row['early_adopter_persona']);
            $object->setSourceOfBusiness($row['source_of_business']);
            $object->setUniversalKeyInformation1($row['universal_key_information_1']);
            $object->setUniversalKeyInformation2($row['universal_key_information_2']);
            $object->setUniversalKeyInformation3($row['universal_key_information_3']);
            $object->setUniversalKeyInformation4($row['universal_key_information_4']);
            $object->setUniversalKeyInformation5($row['universal_key_information_5']);
            // pot_picture_1
            if (!$object->getPotPicture1() && $row['pot_picture_1']) {
                $picture = $this->generatePictureFromFilename($row['pot_picture_1']);
                if ($picture) {
                    $object->setPotPicture1($picture);
                }
            }
            // pot_picture_2
            if (!$object->getPotPicture2() && $row['pot_picture_2']) {
                $picture = $this->generatePictureFromFilename($row['pot_picture_2']);
                if ($picture) {
                    $object->setPotPicture2($picture);
                }
            }
            $object->setPotLegend1($row['pot_legend_1']);
            $object->setPotLegend2($row['pot_legend_2']);
            $object->setKeyLearningSoFar($row['key_learning_so_far']);
            $object->setNextSteps($row['next_steps']);
            $object->setGrowthModel($row['growth_model']);
            $object->setIsEarningAnyMoneyYet($row['is_earning_any_money_yet']);
            $object->setPlanToMakeMoney($row['plan_to_make_money']);
            $object->setMarkets($row['markets']);
            // beautyshot_picture
            if (!$object->getBeautyshotPicture() && $row['beautyshot_picture']) {
                $picture = $this->generatePictureFromFilename($row['beautyshot_picture']);
                if ($picture) {
                    $object->setBeautyshotPicture($picture);
                }
            }
            // packshot_picture
            //$io->text($row['title']." packshot_picture : ".$row['packshot_picture']);
            if (!$object->getPackshotPicture() && $row['packshot_picture']) {
                $picture = $this->generatePictureFromFilename($row['packshot_picture']);
                if ($picture) {
                    $object->setPackshotPicture($picture);
                }
            }
            // financial_graph_picture
            if (!$object->getFinancialGraphPicture() && $row['financial_graph_picture']) {
                $picture = $this->generatePictureFromFilename($row['financial_graph_picture']);
                if ($picture) {
                    $object->setFinancialGraphPicture($picture);
                }
            }
            // additional_pictures
            if ((count($object->getAdditionalPictures()) == 0) && $row['additional_pictures']) {
                $additional_pictures = json_decode($row['additional_pictures'], true);
                for($i = 0; $i < count($additional_pictures); $i++){
                    $picture = $this->generatePictureFromFilename($additional_pictures[$i]);
                    if ($picture) {
                        $additional_picture = $this->em->getRepository('AppBundle:AdditionalPicture')
                            ->findOneBy(['picture' => $picture, 'innovation' => $object]);
                        if(!$additional_picture) {
                            $additional_picture = new AdditionalPicture();
                            $additional_picture->setInnovation($object);
                            $additional_picture->setPicture($picture);
                            $additional_picture->setOrder($i);
                            $this->em->persist($additional_picture);
                        }
                    }
                }
            }
            $object->setVideoUrl($row['video_url']);
            $object->setVideoPassword($row['video_password']);
            $object->setIbpUrl($row['ibp_url']);
            $object->setMybrandsUrl($row['mybrands_url']);
            $object->setPressUrl($row['press_url']);
            $object->setIsNeedingFinancialUpdate($row['is_needing_financial_update']);
            $object->setIsActive($row['is_active']);
            // brand_old_id
            $brand = $this->em->getRepository('AppBundle:Brand')
                ->findOneBy(['old_id' => $row['brand_old_id']]);
            if ($brand) {
                $object->setBrand($brand);
            }
            $this->em->persist($object);
            $io->progressAdvance();
        }
        $this->em->flush();
        $this->em->clear();
        $io->progressFinish();
    }

    /**
     * generatePictureFromFilename
     *
     * @param string $filename
     *
     * @return Picture|null
     */
    protected function generatePictureFromFilename($filename)
    {
        $root_dir = $this->getApplication()->getKernel()->getContainer()->get('kernel')->getRootDir();
        $upload_path = $root_dir . "/../web/uploads/";
        $filepathname = $upload_path . $filename;
        if (file_exists($filepathname)) {
            $object = $this->em->getRepository('AppBundle:Picture')
                ->findOneBy(['filename' => $filename]);
            if (!$object) {
                $object = new Picture();
                $object->setFilename($filename);
                $this->em->persist($object);
                return $object;
            }
        }
        return null;
    }

    /**
     * Import all FinancialDatas
     */
    protected function importFinancialDatas($io)
    {
        $io->text("Attempting to import all financial datas...");
        $csv = Reader::createFromPath('%kernel.root_dir%/../src/AppBundle/Data/financial_data.csv');
        $csv->setHeaderOffset(0); //set the CSV header offset
        $io->progressStart(iterator_count($csv));
        $i = 0;
        foreach ($csv as $row) {
            $object = $this->em->getRepository('AppBundle:FinancialData')
                ->findOneBy(['old_id' => $row['old_id']]);
            if (!$object) {
                $object = new FinancialData();
            }
            $object->setOldId(intval($row['old_id']));
            // innovation_old_id
            $innovation = $this->em->getRepository('AppBundle:Innovation')
                ->findOneBy(['old_id' => $row['innovation_old_id']]);
            if ($innovation) {
                $object->setInnovation($innovation);
            }
            $object->setKey($row['key']);
            $object->setValue($row['value']);
            $this->em->persist($object);
            unset($object);
            unset($innovation);
            $io->progressAdvance();
            // I flush every 1500 rows to prevent "Out of memory"
            if ($i % 1500 == 0) {
                $this->em->flush();
                $this->em->clear();
            }
            $i++;
        }
        $this->em->flush();
        $this->em->clear();
        $io->progressFinish();
    }

    /**
     * Import all PerformanceReviews
     */
    protected function importPerformanceReviews($io)
    {
        $io->text("Attempting to import all performance reviews...");
        $csv = Reader::createFromPath('%kernel.root_dir%/../src/AppBundle/Data/performance_review.csv');
        $csv->setHeaderOffset(0); //set the CSV header offset
        $io->progressStart(iterator_count($csv));
        foreach ($csv as $row) {
            $object = $this->em->getRepository('AppBundle:PerformanceReview')
                ->findOneBy(['old_id' => $row['old_id']]);
            if (!$object) {
                $object = new PerformanceReview();
            }
            $object->setOldId(intval($row['old_id']));
            // innovation_old_id
            $innovation = $this->em->getRepository('AppBundle:Innovation')
                ->findOneBy(['old_id' => $row['innovation_old_id']]);
            if ($innovation) {
                $object->setInnovation($innovation);
            }
            $object->setKey($row['key']);
            $object->setValue($row['value']);
            $this->em->persist($object);
            $io->progressAdvance();
        }
        $this->em->flush();
        $this->em->clear();
        $io->progressFinish();
    }

    /**
     * Import all Activities
     */
    protected function importActivities($io)
    {
        $io->text("Attempting to import all activities...");
        $csv = Reader::createFromPath('%kernel.root_dir%/../src/AppBundle/Data/activity.csv');
        $csv->setHeaderOffset(0); //set the CSV header offset
        $io->progressStart(iterator_count($csv));
        $i = 0;
        foreach ($csv as $row) {
            $object = $this->em->getRepository('AppBundle:Activity')
                ->findOneBy(['old_id' => $row['old_id']]);
            if (!$object) {
                $object = new Activity();
            }
            $object->setOldId(intval($row['old_id']));
            $object->setTitle($row['title']);
            // innovation_old_id
            $innovation = $this->em->getRepository('AppBundle:Innovation')
                ->findOneBy(['old_id' => $row['innovation_old_id']]);
            if ($innovation) {
                $object->setInnovation($innovation);
            }
            $object->setActionId($row['action_id']);
            $object->setSourceId($row['source_id']);
            // user_old_id
            $user = $this->em->getRepository('AppBundle:User')
                ->findOneBy(['old_id' => $row['user_old_id']]);
            if ($user) {
                $object->setUser($user);
            }
            $is_child = ($row['is_child']) ? 1 : 0;
            $object->setIsChild($is_child);
            $object->setData($row['data']);

            // financial_data_old_id
            $financial_data = $this->em->getRepository('AppBundle:FinancialData')
                ->findOneBy(['old_id' => $row['financial_data_old_id']]);
            if ($financial_data) {
                $object->setFinancialData($financial_data);
            }
            $object->importCreatedAt(new \DateTime($row['created_at']));
            $this->em->persist($object);
            unset($object);
            unset($innovation);
            unset($user);
            $io->progressAdvance();
            // I flush every 1500 rows to prevent "Out of memory"
            if ($i % 1500 == 0) {
                $this->em->flush();
                $this->em->clear();
            }
            $i++;
        }
        $this->em->flush();
        $this->em->clear();
        $io->progressFinish();
    }

    /**
     * Import all UserInnovationRights
     */
    protected function importUserInnovationRights($io)
    {
        $io->text("Attempting to import all user innovation rights...");
        $csv = Reader::createFromPath('%kernel.root_dir%/../src/AppBundle/Data/user_innovation_rights.csv');
        $csv->setHeaderOffset(0); //set the CSV header offset
        $io->progressStart(iterator_count($csv));
        foreach ($csv as $row) {
            $object = $this->em->getRepository('AppBundle:UserInnovationRight')
                ->findOneBy(['old_id' => $row['old_id']]);
            if (!$object) {
                $object = new UserInnovationRight();
            }
            $object->setOldId(intval($row['old_id']));
            // innovation_old_id
            $innovation = $this->em->getRepository('AppBundle:Innovation')
                ->findOneBy(['old_id' => $row['innovation_old_id']]);
            if ($innovation) {
                $object->setInnovation($innovation);
            }
            // user_old_id
            $user = $this->em->getRepository('AppBundle:User')
                ->findOneBy(['old_id' => $row['user_old_id']]);
            if ($user) {
                $object->setUser($user);
            }
            $object->setUserRole($row['role']);
            $object->setUserRight($row['right']);
            $this->em->persist($object);
            $io->progressAdvance();
        }
        $this->em->flush();
        $this->em->clear();
        $io->progressFinish();
    }


    /**
     * Post update all Innovations
     */
    protected function postUpdateAllInnovations($io)
    {
        $io->text("Attempting to post updates all innovations...");
        $csv = Reader::createFromPath('%kernel.root_dir%/../src/AppBundle/Data/innovation.csv');
        $csv->setHeaderOffset(0); //set the CSV header offset
        $io->progressStart(iterator_count($csv));
        foreach ($csv as $row) {
            $object = $this->em->getRepository('AppBundle:Innovation')
                ->findOneBy(['old_id' => $row['old_id']]);
            if (!$object) {
                continue;
            }
            $old_stage_activity = $object->getPreviousStageActivity();
            if ($old_stage_activity) {
                // Stage
                $stage = $this->em->getRepository('AppBundle:Stage')
                    ->findOneById($old_stage_activity->getDataArray()['old_value']);
                if ($stage) {
                    $object->setOldStage($stage);
                    $object->setOldStageDate($old_stage_activity->getCreatedAt());
                }
            }
            $this->em->persist($object);
            $io->progressAdvance();
        }
        $this->em->flush();
        $this->em->clear();
        $io->progressFinish();
    }

    /**
     * Post update all Innovations created_at
     */
    protected function postUpdateAllInnovationsCreatedAt($io)
    {
        $io->text("Attempting to post updates all innovations created_at...");
        $csv = Reader::createFromPath('%kernel.root_dir%/../src/AppBundle/Data/innovation.csv');
        $csv->setHeaderOffset(0); //set the CSV header offset
        $io->progressStart(iterator_count($csv));
        foreach ($csv as $row) {
            $object = $this->em->getRepository('AppBundle:Innovation')
                ->findOneBy(['old_id' => $row['old_id']]);
            if (!$object) {
                continue;
            }
            $object->importCreatedAt(new \DateTime($row['created_at']));
            $this->em->persist($object);
            $io->progressAdvance();
        }
        $this->em->flush();
        $this->em->clear();
        $io->progressFinish();
    }

    /**
     * Generate redis datas
     */
    protected function generateRedisDatas($io)
    {
        $io->text("Attempting to generate redis datas...");

        $redis_prefix = $this->getContainer()->getParameter('redis_prefix');
        $this->redis->del($redis_prefix . 'redis_cache_progress');
        $this->redis->del($redis_prefix.'pri_otherdatas');
        $this->redis->del($redis_prefix.'pri_allinnovations_excel');
        $this->redis->del($redis_prefix.'pri_allinnovations');
        $this->redis->del($redis_prefix.'pri_allconsolidation');

        $pernodWorker = $this->getContainer()->get('AppBundle\Worker\PernodWorker');
        // Worker methods are launched synchronously because we need it
        $pernodWorker->initRedisCacheProgress();
        $pernodWorker->generateAllInnovationsAndConsolidation(true);
    }


}