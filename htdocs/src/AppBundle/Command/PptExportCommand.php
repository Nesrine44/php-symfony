<?php
/**
 * Created by PhpStorm.
 * User: FlorianNicolas
 * Date: 13/07/2018
 * Time: 16:28
 */

namespace AppBundle\Command;

use AppBundle\Entity\Settings;
use AppBundle\UtilsPpt;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Predis\Client;

class PptExportCommand extends ContainerAwareCommand
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
        $this
            ->setName('pri:generate_ppt')
            ->setDescription('Export from database to ppt file')
            ->addArgument(
                'export_type',
                InputArgument::OPTIONAL,
                'What do you want to export?'
            )->addOption(
                'path',
                null,
                InputOption::VALUE_OPTIONAL,
                'File path')
            ->addOption(
                'export_id',
                null,
                InputOption::VALUE_OPTIONAL,
                'Export id')
            ->addOption(
                'user_id',
                null,
                InputOption::VALUE_OPTIONAL,
                'user_id')
            ->addOption(
                'innovation_id',
                null,
                InputOption::VALUE_OPTIONAL,
                'innovation_id')
            ->addOption(
                'params',
                null,
                InputOption::VALUE_OPTIONAL,
                'params')
            ;
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $root_dir =  $this->getContainer()->get('kernel')->getRootDir();
        $awsS3Uploader  = $this->getContainer()->get('app.s3_uploader');
        $wsGlobalDatas = $this->getContainer()->get('app.website_global_datas');
        $secure_path = $root_dir.'/../private/secure/';
        $export_type = $input->getArgument('export_type');
        $settings = $this->em->getRepository('AppBundle:Settings')->getCurrentSettings();
        $path = $input->getOption('path');
        $export_id = $input->getOption('export_id');
        $user_id = $input->getOption('user_id');
        $innovation_id = $input->getOption('innovation_id');

        $params = UtilsPpt::getProperParamsForOverviewExportPpt(json_decode($input->getOption('params'), true));
        
        if ($export_type == 'overview_quali') {
            if(!$path) {
                $path = $secure_path.'export - Overview - Quali - ' . date('Y-m-d').'.pptx';
            }
            try{
                if(!$user_id){ // Default user
                    $user = $this->em->getRepository('AppBundle:User')->findOneBy(array('email' => 'florian.nicolas@corellis.eu'));
                }else{
                    $user = $this->em->getRepository('AppBundle:User')->find($user_id);
                }
                $innovation_ids = $this->em->getRepository('AppBundle:Innovation')->getAllInnovationsIdsForUser($user, $params['innovations_ids']);
                $innovations_array = $wsGlobalDatas->getInnovationsArrayByArrayIds($innovation_ids);
                UtilsPpt::ppt_overview_full_quali($this->em, $this->redis, $innovations_array, $settings, $user, $params,  $path, $export_id, $awsS3Uploader);
            }catch (\Exception $e){
                $this->getContainer()->get('logger')->error('[PPT Export error on '.$export_type.'] : '.$e->getMessage());
                $this->getContainer()->get('logger')->error('[Stack-Trace='.$e->getTraceAsString().']');
                $this->redis->set($export_id, Settings::EXPORT_PROGRESS_ERROR);
            }
        }
        elseif ($export_type == 'quali') {
            $innovation = $wsGlobalDatas->getInnovationArrayById($innovation_id);
            if(!$path) {
                $path = $secure_path.$innovation['title'].' ' . date('Y-m-d').'.pptx';
            }
            try{
                UtilsPpt::ppt_quali($this->redis, $innovation, $settings, null, $path, $export_id, $awsS3Uploader);
            }catch (\Exception $e){
                $this->getContainer()->get('logger')->error('[PPT Export error on '.$export_type.'] : '.$e->getMessage());
                $this->getContainer()->get('logger')->error('[Stack-Trace='.$e->getTraceAsString().']');
                $this->redis->set($export_id, Settings::EXPORT_PROGRESS_ERROR);
            }
        }
        elseif ($export_type == 'quali_full') {
            $innovation = $wsGlobalDatas->getInnovationArrayById($innovation_id);
            if(!$path) {
                $path = $secure_path.$innovation['title'].' ' . date('Y-m-d').'.pptx';
            }
            try{
                UtilsPpt::ppt_quali($this->redis, $innovation, $settings, 'full', $path, $export_id, $awsS3Uploader);
            }catch (\Exception $e){
                $this->getContainer()->get('logger')->error('[PPT Export error on '.$export_type.'] : '.$e->getMessage());
                $this->getContainer()->get('logger')->error('[Stack-Trace='.$e->getTraceAsString().']');
                $this->redis->set($export_id, Settings::EXPORT_PROGRESS_ERROR);
            }
        }
        elseif ($export_type == 'contributor') {
            if(!$path) {
                $path = $secure_path.'Entity performance review - ' . date('Y-m-d').'.pptx';
            }
            try{
                if(!$user_id){ // Default user
                    $user = $this->em->getRepository('AppBundle:User')->findOneBy(array('email' => 'florian.nicolas@corellis.eu'));
                }else{
                    $user = $this->em->getRepository('AppBundle:User')->find($user_id);
                }
                $innovation_ids = $this->em->getRepository('AppBundle:Innovation')->getAllInnovationsIdsForUser($user);
                $innovations_array = $wsGlobalDatas->getInnovationsArrayByArrayIds($innovation_ids);
                UtilsPpt::ppt_top_contributor($this->redis, $innovations_array, $settings, $user, $path, $export_id, $awsS3Uploader);
            }catch (\Exception $e){
                $this->getContainer()->get('logger')->error('[PPT Export error on '.$export_type.'] : '.$e->getMessage());
                $this->getContainer()->get('logger')->error('[Stack-Trace='.$e->getTraceAsString().']');
                $this->redis->set($export_id, Settings::EXPORT_PROGRESS_ERROR);
            }
        }
    }

}