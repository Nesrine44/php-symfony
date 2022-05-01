<?php

namespace AppBundle\Service;
use AppBundle\Entity\User;


/**
 * Class PrEmployeeApi
 * @package AppBundle\Service
 */
class PrEmployeeApi
{
    private $api_key;
    private $api_base_url;



    public function __construct($api_key, $api_base_url)
    {
        $this->api_key = $api_key;
        $this->api_base_url = $api_base_url;
    }

    /**
     * Get user infos.
     *
     * @param User $user
     * @return mixed|null
     */
    public function getUserInfos($user)
    {
        $email = $user->getEmail();
        if(!$user->getIsPrEmploye()){
            return null;
        }
        try{
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'api_key: '.$this->api_key,
            ));
            curl_setopt($curl, CURLOPT_URL, $this->api_base_url.$email);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

            $result = curl_exec($curl);
            curl_close($curl);
            if($result){
                return json_decode($result, true);
            }
            return $result;
        }catch (\Exception $e){
            return null;
        }
    }

    /**
     * Get user title and country.
     *
     * @param User $user
     * @return string
     */
    public function getUserTitleAndCountry($user)
    {
        try{
            $title_and_coutry = '';
            $infos = $this->getUserInfos($user);
            if($infos && is_array($infos) && array_key_exists('Title', $infos)){
                $title_and_coutry .= $infos['Title'];
                if(array_key_exists('Title', $infos)) {
                    $title_and_coutry .= ' ('.$infos['Country'].')';
                }
            }
            return $title_and_coutry;
        }catch (\Exception $e){
            return '';
        }
    }

    /**
     * Get user used infos
     *
     * @param User $user
     * @return string
     */
    public function getUserUsedInfos($user)
    {
        try{
            $ret = array(
                'situation' => null,
                'country' => null,
                'pr_entity' => null,
            );
            $infos = $this->getUserInfos($user);
            if($infos && is_array($infos)){
                if(array_key_exists('Title', $infos)){
                    $ret['situation'] = $infos['Title'];
                }
                if(array_key_exists('Country', $infos)){
                    $ret['country'] = $infos['Country'];
                }
                if(array_key_exists('Company', $infos)){
                    $ret['pr_entity'] = $infos['Company'];
                }

            }
            return $ret;
        }catch (\Exception $e){
            return null;
        }
    }
    
}

