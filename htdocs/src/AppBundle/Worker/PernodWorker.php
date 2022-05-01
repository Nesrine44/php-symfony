<?php

namespace AppBundle\Worker;

use AppBundle\Entity\Settings;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpKernel\KernelInterface;


class PernodWorker extends \Dtc\QueueBundle\Model\Worker
{
    private $redis;
    private $redis_prefix;
    private $em;
    private $kernel;
    private $liip;

    public function __construct()
    {
    }

    /**
     * Set kernel
     * @param KernelInterface $kernel
     */
    public function setKernel(KernelInterface $kernel) {
        $this->kernel = $kernel;
    }

    /**
     * Set Redis service
     * @param $redis
     */
    public function setRedis($redis) {
        $this->redis = $redis;
    }

    /**
     * Set Liip service.
     *
     * @param $liip
     */
    public function setLiip($liip) {
        $this->liip = $liip;
    }

    /**
     * Set redis prefix.
     *
     * @param $redis_prefix
     */
    public function setRedisPrefix($redis_prefix) {
        $this->redis_prefix = $redis_prefix;
    }
    
    public function getRedisPrefix() {
        return $this->redis_prefix;
    }

    /**
     * Set Entity Manager
     * @param $em
     */
    public function setEntityManager($em) {
        $this->em = $em;
    }

    /**
     * How to user workers
     *
     * Create a job:
     * $worker = new PernodWorker()
     * $job = $worker->batchLater()->generateAllInnovationsAndConsolidation(); //Batch Example
     *
     * or
     *
     * $job = $worker->later()->generateAllInnovationsAndConsolidation();
     * $job = $workerManager->runJob($job);
     *
     */

    /**
     * Get redis cache progress.
     * 
     * @return mixed
     */
    public function getRedisCacheProgress(){
        return $this->redis->get($this->redis_prefix.'redis_cache_progress');
    }

    /**
     * Init redis cache progress.
     *
     * @return mixed
     */
    public function initRedisCacheProgress(){
        $this->redis->set($this->redis_prefix.'redis_cache_progress', 1);
        return $this->getRedisCacheProgress();
    }

    /**
     * Generate all_innovations array and all_consolidation array.
     *
     * @param bool $with_other_data
     */
    public function generateAllInnovationsAndConsolidation($with_other_data = false)
    {
        ini_set('memory_limit', '-1');
        $redis_key = $this->redis_prefix.'redis_cache_progress';
        $this->redis->set($redis_key, 2);
        if($with_other_data){
            $this->generateOtherDatas();
        }
        $liip = $this->liip;
        $settings = $this->em->getRepository('AppBundle:Settings')->getCurrentSettings();
        $this->redis->set($redis_key, 3);
        $all_actives_innovations = $this->em->getRepository('AppBundle:Innovation')->getAllActiveInnovations();
        $this->redis->set($redis_key, 6);
        $all_innovations = array();
        $all_innovations_excel = array();
        $arg = 1;
        $coeff = count($all_actives_innovations) / 93;
        foreach ($all_actives_innovations as $item){
            $all_innovations[] = $item->toArray($settings, $liip);
            $all_innovations_excel[] = $item->toExcelArray($settings);
            $progress = round($arg / $coeff) + 6;
            $this->redis->set($redis_key, $progress);
            $arg++;
        }
        $all_consolidation = $this->em->getRepository('AppBundle:Innovation')->getInfosForConsolidation($settings);
        $this->redis->set($redis_key, 99);
        //Save result to redis
        $this->redis->set($this->redis_prefix.'pri_allinnovations',json_encode($all_innovations));
        $this->redis->set($this->redis_prefix.'pri_allinnovations_excel',json_encode($all_innovations_excel));
        $this->redis->set($this->redis_prefix.'pri_allconsolidation',json_encode($all_consolidation));
        $this->em->getRepository('AppBundle:Settings')->updateCurrentSettingsPing();
        $this->redis->del($redis_key);
    }

    /**
     * How to user workers
     *
     * Create a job:
     * $worker = new PernodWorker()
     * $job = $worker->batchLater()->generateAllInnovationsAndConsolidation(); //Batch Example
     *
     * or
     *
     * $job = $worker->later()->generateAllInnovationsAndConsolidation();
     * $job = $workerManager->runJob($job);
     *
     */

    /**
     * Update all_innovations array and all_consolidation array by innovation.
     *
     * @param array $innovation_array
     */
    public function updateAllInnovationsAndConsolidationByInnovation($innovation_array)
    {
        //Execute query and generate Array
        $settings = $this->em->getRepository('AppBundle:Settings')->getCurrentSettings();
        $all_innovations = json_decode($this->redis->get($this->redis_prefix.'pri_allinnovations'), true);
        for ($i = 0; $i < count($all_innovations); $i++){
            if($all_innovations[$i]['id'] === $innovation_array['id']){
                $all_innovations[$i] = $innovation_array;
                break;
            }
        }
        $this->updateAllInnovationsExcel($settings, $innovation_array['id']);
        $all_consolidation = $this->em->getRepository('AppBundle:Innovation')->getInfosForConsolidation($settings);
        //Save result to redis
        $this->redis->set($this->redis_prefix.'pri_allinnovations',json_encode($all_innovations));
        $this->redis->set($this->redis_prefix.'pri_allconsolidation',json_encode($all_consolidation));
        $this->em->getRepository('AppBundle:Settings')->updateCurrentSettingsPing();
    }

    /**
     * Update all innovations excel.
     *
     * @param Settings $settings
     * @param int $id
     */
    public function updateAllInnovationsExcel(Settings $settings, $id){
        $innovation = $this->em->getRepository('AppBundle:Innovation')->findActiveInnovation($id);
        if($innovation){
            $all_innovations_excel = json_decode($this->redis->get($this->redis_prefix.'pri_allinnovations_excel'), true);
            for ($i = 0; $i < count($all_innovations_excel); $i++){
                if($all_innovations_excel[$i]['id'] === $id){
                    $all_innovations_excel[$i] = $innovation->toExcelArray($settings);
                    $this->redis->set($this->redis_prefix.'pri_allinnovations_excel',json_encode($all_innovations_excel));
                    break;
                }
            }
        }
    }

    /**
     * Update all innovations excel by adding innovation.
     *
     * @param Settings $settings
     * @param int $id
     */
    public function updateAllInnovationsExcelByAddingInnovation(Settings $settings, $id){
        $innovation = $this->em->getRepository('AppBundle:Innovation')->findActiveInnovation($id);
        if($innovation){
            $all_innovations_excel = json_decode($this->redis->get($this->redis_prefix.'pri_allinnovations_excel'), true);
            $all_innovations_excel[] = $innovation->toExcelArray($settings);
            $this->redis->set($this->redis_prefix.'pri_allinnovations_excel',json_encode($all_innovations_excel));
        }
    }

    /**
     * Update all innovations excel by removing innovation.
     *
     * @param int $id
     */
    public function updateAllInnovationsExcelByRemovingInnovation($id){
        $all_innovations_excel = json_decode($this->redis->get($this->redis_prefix.'pri_allinnovations_excel'), true);
        for ($i = 0; $i < count($all_innovations_excel); $i++){
            if($all_innovations_excel[$i]['id'] === $id){
                unset($all_innovations_excel[$i]);
                break;
            }
        }
        $all_innovations_excel = array_values($all_innovations_excel);
        $this->redis->set($this->redis_prefix.'pri_allinnovations_excel',json_encode($all_innovations_excel));
    }

    /**
     * Update all_innovations array and all_consolidation array by adding innovation.
     *
     * @param array $innovation_array
     */
    public function updateAllInnovationsAndConsolidationByAddingInnovation($innovation_array)
    {
        //Execute query and generate Array
        $settings = $this->em->getRepository('AppBundle:Settings')->getCurrentSettings();
        $all_innovations = json_decode($this->redis->get($this->redis_prefix.'pri_allinnovations'), true);
        $all_innovations[] = $innovation_array;
        $this->updateAllInnovationsExcelByAddingInnovation($settings, $innovation_array['id']);
        $all_consolidation = $this->em->getRepository('AppBundle:Innovation')->getInfosForConsolidation($settings);
        //Save result to redis
        $this->redis->set($this->redis_prefix.'pri_allinnovations',json_encode($all_innovations));
        $this->redis->set($this->redis_prefix.'pri_allconsolidation',json_encode($all_consolidation));
        $this->em->getRepository('AppBundle:Settings')->updateCurrentSettingsPing();
    }

    /**
     * Update all_innovations array and all_consolidation array by innovation.
     *
     * @param int $innovation_id
     */
    public function updateAllInnovationsAndConsolidationByRemovingInnovation($innovation_id)
    {
        //Execute query and generate Array
        $settings = $this->em->getRepository('AppBundle:Settings')->getCurrentSettings();
        $all_innovations = json_decode($this->redis->get($this->redis_prefix.'pri_allinnovations'), true);
        for ($i = 0; $i < count($all_innovations); $i++){
            if($all_innovations[$i]['id'] === $innovation_id){
                unset($all_innovations[$i]);
                break;
            }
        }
        $all_innovations = array_values($all_innovations);
        $this->updateAllInnovationsExcelByRemovingInnovation($innovation_id);
        $all_consolidation = $this->em->getRepository('AppBundle:Innovation')->getInfosForConsolidation($settings);
        //Save result to redis
        $this->redis->set($this->redis_prefix.'pri_allinnovations',json_encode($all_innovations));
        $this->redis->set($this->redis_prefix.'pri_allconsolidation',json_encode($all_consolidation));
        $this->em->getRepository('AppBundle:Settings')->updateCurrentSettingsPing();
    }

    /**
     * Generate other_datas array.
     */
    public function generateOtherDatas()
    {
        //Execute query and generate Array
        $other_datas = $this->em->getRepository('AppBundle:Settings')->getWebsiteOtherDatas();
        //Save result to redis
        $this->redis->set($this->redis_prefix.'pri_otherdatas',json_encode($other_datas));
        $this->em->getRepository('AppBundle:Settings')->updateCurrentSettingsPing();
    }


    /**
     * Run command.
     *
     * @param $data_array
     *
     * $data_array example:
     *
     *  $data_array = array(
     *      'command' => 'pri:generate_excel',
     *      'export_type' => 'active_user', // argument 1
     *      '--path' => "", // option path
     *      '--export_id' => "", // option export_id
     *  );
     *
     */
    public function runCommand($data_array){
        ini_set('memory_limit', '-1');
        $application = new Application($this->kernel);
        $application->setAutoExit(false);
        $input = new ArrayInput($data_array);
        // You can use NullOutput() if you don't need the output
        $output = new BufferedOutput();
        $application->run($input, $output);
    }

    /**
     * Return Job's name
     * @return string
     */
    public function getName()
    {
        return 'pernodWorker';
    }




}