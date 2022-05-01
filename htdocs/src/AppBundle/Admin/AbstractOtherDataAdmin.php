<?php
/**
 * Created by PhpStorm.
 * User: florian
 * Date: 16/10/2018
 * Time: 11:49
 */

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;


class AbstractOtherDataAdmin extends AbstractAdmin
{

    public function postUpdate($object){
        $container = $this->getConfigurationPool()->getContainer();
        $pernodWorker = $container->get('AppBundle\Worker\PernodWorker');
        $pernodWorker->generateOtherDatas();
        parent::postUpdate($object);
    }

    public function postPersist($object){
        $container = $this->getConfigurationPool()->getContainer();
        $pernodWorker = $container->get('AppBundle\Worker\PernodWorker');
        $pernodWorker->generateOtherDatas();
        parent::postPersist($object);
    }

    public function postRemove($object){
        $container = $this->getConfigurationPool()->getContainer();
        $pernodWorker = $container->get('AppBundle\Worker\PernodWorker');
        $pernodWorker->generateOtherDatas();
        parent::postRemove($object);
    }

}