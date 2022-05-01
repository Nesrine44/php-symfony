<?php
/**
 * Created by PhpStorm.
 * User: jonathan.garcia
 * Date: 11/07/2018
 * Time: 11:02
 */


namespace AppBundle\Command;

use AppBundle\Entity\Settings;
use AppBundle\UtilsExcel;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Predis\Client;


class ExcelExportCommand extends ContainerAwareCommand
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
            ->setName('pri:generate_excel')
            ->setDescription('Export from database to excel file')
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
                'user_id',
                true)
            ->addOption(
                'innovations_ids',
                null,
                InputOption::VALUE_OPTIONAL,
                'innovations_ids',
                '[]')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $root_dir =  $this->getContainer()->get('kernel')->getRootDir();
        $awsS3Uploader  = $this->getContainer()->get('app.s3_uploader');
        $export_type = $input->getArgument('export_type');
        $path = $input->getOption('path');
        $export_id = $input->getOption('export_id');
        $user_id = $input->getOption('user_id');
        $innovations_ids = json_decode($input->getOption('innovations_ids'), true);
        $template_path = $root_dir.'/../private/template/';
        $secure_path = $root_dir.'/../private/secure/';
        $excelObj= $this->getContainer()->get('phpexcel');
        if (!$export_type) {
            $output->writeln('Wich kind of export do you want?');
            exit(0);
        }
        elseif ($export_type == 'active_user') {
            if(!$path) {
                $path = $secure_path . 'Active users from ' . date("Y-m-d", strtotime("-3 Months")) . ' to ' . date('Y-m-d') . '.xlsx';
            }
            try{
                UtilsExcel::active_users($this->em, $this->redis, $excelObj, $path, $template_path, $export_id, $awsS3Uploader);
            }catch (\Exception $e){
                $this->getContainer()->get('logger')->error('[Excel Export error on '.$export_type.'] : '.$e->getMessage());
                $this->getContainer()->get('logger')->error('[Stack-Trace='.$e->getTraceAsString().']');
                $this->redis->set($export_id, Settings::EXPORT_PROGRESS_ERROR);
            }
        }
        elseif ($export_type == 'newsletter_user') {
            if(!$path) {
                $path = $secure_path.'Newsletter users - ' . date('Y-m-d') . '.xlsx';
            }
            try{
                UtilsExcel::newsletter_users($this->em, $this->redis, $excelObj, $path, $template_path, $export_id, $awsS3Uploader);
            }catch (\Exception $e){
                $this->getContainer()->get('logger')->error('[Excel Export error on '.$export_type.'] : '.$e->getMessage());
                $this->getContainer()->get('logger')->error('[Stack-Trace='.$e->getTraceAsString().']');
                $this->redis->set($export_id, Settings::EXPORT_PROGRESS_ERROR);
            }
        }
        elseif ($export_type == 'matrix') {
            if(!$path) {
                $path = $secure_path.'Team Matrix update - ' . date('Y-m-d') . '.xlsx';
            }
            try{
                UtilsExcel::team_matrix_update($this->em, $this->redis, $excelObj, $path, $template_path, $export_id, $awsS3Uploader);
            }catch (\Exception $e){
                $this->getContainer()->get('logger')->error('[Excel Export error on '.$export_type.'] : '.$e->getMessage());
                $this->getContainer()->get('logger')->error('[Stack-Trace='.$e->getTraceAsString().']');
                $this->redis->set($export_id, Settings::EXPORT_PROGRESS_ERROR);
            }
        }
        elseif ($export_type == 'matrix_without_duplicate') {
            if(!$path) {
                $path = $secure_path.'Team Matrix update without duplicate - ' . date('Y-m-d') . '.xlsx';
            }
            try{
                UtilsExcel::team_matrix_update_no_duplicate($this->em, $this->redis, $excelObj, $path, $template_path, $export_id, $awsS3Uploader);
            }catch (\Exception $e){
                $this->getContainer()->get('logger')->error('[Excel Export error on '.$export_type.'] : '.$e->getMessage());
                $this->getContainer()->get('logger')->error('[Stack-Trace='.$e->getTraceAsString().']');
                $this->redis->set($export_id, Settings::EXPORT_PROGRESS_ERROR);
            }
        }
        elseif ($export_type == 'innovations') {
            if(!$path) {
                $path = $secure_path.'Innovations - ' . date('Y-m-d') . '.xlsx';
            }
            try{
                $wsGlobalDatas = $this->getContainer()->get('app.website_global_datas');
                if(!$user_id){ // Default user
                    $user = $this->em->getRepository('AppBundle:User')->findOneBy(array('email' => 'florian.nicolas@corellis.eu'));
                }else{
                    $user = $this->em->getRepository('AppBundle:User')->find($user_id);
                }
                $innovations_ids = $this->em->getRepository('AppBundle:Innovation')->getAllInnovationsIdsForUser($user, $innovations_ids);
                $innovations_array = $wsGlobalDatas->getInnovationsExcelByArrayIds($innovations_ids);
                UtilsExcel::innovations($this->em, $this->redis, $excelObj, $path, $template_path, $innovations_array, $user, $export_id, $awsS3Uploader);
            }catch (\Exception $e){
                $this->getContainer()->get('logger')->error('[Excel Export error on '.$export_type.'] : '.$e->getMessage());
                $this->getContainer()->get('logger')->error('[Stack-Trace='.$e->getTraceAsString().']');
                $this->redis->set($export_id, Settings::EXPORT_PROGRESS_ERROR);
            }
        }elseif ($export_type == 'complete') {
            if(!$path) {
                $path = $secure_path.'Complete innovations - ' . date('Y-m-d') . '.xlsx';
            }
            try{
                $wsGlobalDatas = $this->getContainer()->get('app.website_global_datas');
                if(!$user_id){ // Default user
                    $user = $this->em->getRepository('AppBundle:User')->findOneBy(array('email' => 'florian.nicolas@corellis.eu'));
                }else{
                    $user = $this->em->getRepository('AppBundle:User')->find($user_id);
                }
                $innovations_ids = array();
                $innovations =  $wsGlobalDatas->getInnovationsArrayByArrayIds($innovations_ids);
                $innovations_excel = $wsGlobalDatas->getInnovationsExcelByArrayIds($innovations_ids);
                UtilsExcel::complete($this->em, $this->redis, $excelObj, $path, $template_path, $innovations, $innovations_excel, $user, $export_id, $awsS3Uploader);
            }catch (\Exception $e){
                $this->getContainer()->get('logger')->error('[Excel Export error on '.$export_type.'] : '.$e->getMessage());
                $this->getContainer()->get('logger')->error('[Stack-Trace='.$e->getTraceAsString().']');
                $this->redis->set($export_id, Settings::EXPORT_PROGRESS_ERROR);
            }
        }
    }

}