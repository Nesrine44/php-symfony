<?php

namespace AppBundle\Service;
use AppBundle\Controller\DashboardController;
use AppBundle\Entity\Innovation;
use AppBundle\Entity\Settings;
use AppBundle\Entity\Stage;
use AppBundle\Entity\UserInnovationRight;
use AppBundle\Repository\SettingsRepository;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Entity\User;
use Predis\Client;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * This service is injected in twig
 * Class WebsiteGlobalDatas
 * @package AppBundle\Service
 */
class WebsiteGlobalDatas
{

    private $em;
    private $settings;
    private $container;
    private $redis;
    private $redis_prefix;

    public function __construct(EntityManagerInterface $em, ContainerInterface $container, Client $redis)
    {
        $this->em = $em;
        // I load setting in construct to do it only one time
        $this->settings = $this->em->getRepository('AppBundle:Settings')->getCurrentSettings();
        $this->container = $container;
        $this->redis = $redis;
        $this->redis_prefix = $this->container->getParameter('redis_prefix');
    }


    /**
     * Return global website settings
     * @return array
     */
    public function settings()
    {
        if(!$this->settings){
            return array();
        }
        return $this->settings->toArray();
    }

    /**
     * Get all citations.
     * @return array
     */
    public function getAllCitations(){
        return SettingsRepository::getAllCitations();
    }

    /**
     * Get random citation.
     * @return array
     */
    public function getRandomCitation(){
        return SettingsRepository::getRandomCitation();
    }

    /**
     * Return constants array
     * @return array
     */
    public function constants()
    {
        return array(
            'UserInnovationRight' => array(
                'RIGHT_READ' => UserInnovationRight::RIGHT_READ,
                'RIGHT_WRITE' => UserInnovationRight::RIGHT_WRITE,
                'ROLE_CONTACT_OWNER' => UserInnovationRight::ROLE_CONTACT_OWNER,
                'ROLE_FINANCE_CONTACT' => UserInnovationRight::ROLE_FINANCE_CONTACT,
                'ROLE_RESEARCH_AND_DEVELOPMENT' => UserInnovationRight::ROLE_RESEARCH_AND_DEVELOPMENT,
                'ROLE_MANAGEMENT' => UserInnovationRight::ROLE_MANAGEMENT,
                'ROLE_CONSUMER_INSIGHTS' => UserInnovationRight::ROLE_CONSUMER_INSIGHTS,
                'ROLE_LEGAL' => UserInnovationRight::ROLE_LEGAL,
                'ROLE_OPERATIONS' => UserInnovationRight::ROLE_OPERATIONS,
                'ROLE_OTHER' => UserInnovationRight::ROLE_OTHER,
            )
        );
    }

    /**
     * main_classes
     *
     * @param User $user
     * @param string $current_url
     * @return string
     */
    public function main_classes($user, $current_url = '')
    {
        $has_role_hq = $user->hasAdminRights();
        $has_role_management = $user->hasManagementRights();
        $has_role_entity_team_leader = $user->hasRoleEntityTeamLeader();
        $has_no_role = $user->hasNoRole();
        $class_main_open_data = ($this->settings && $this->settings->getIsEditionQuantiEnabled()) ? 'data-opened ' : 'data-closed ';
        $class_main_open_quali_data = ($this->settings && $this->settings->getIsEditionQualiEnabled()) ? 'data-quali-opened ' : 'data-quali-closed ';

        $class_is_maker = ($user->hasOnlyRoleMaker()) ? 'maker ' : '';

        $main_classes = ($has_no_role) ? 'no-role ' : '';
        $main_classes .= ' ';
        $main_classes .= ($this->settings && $this->settings->getIsProjectCreationEnabled()) ? 'project-creation-enabled ' : '';
        $main_classes .= ($current_url == '/') ? 'welcome_page ' : '';
        $main_classes .= ($has_role_hq) ? 'hq ' : '';
        $main_classes .= ' ';
        $main_classes .= ($user->hasDeveloperRights()) ? 'dev ' : '';
        $main_classes .= ($has_role_management) ? 'readonly management ' : '';
        $main_classes .= ' ';
        $main_classes .= ($has_role_entity_team_leader) ? 'entity_team_leader ' : '';
        $main_classes .= " " . $class_main_open_data;
        $main_classes .= " " . $class_main_open_quali_data;
        $main_classes .= ' ' . $class_is_maker;
        if($this->settings && $this->settings->getIsBetaEnabled()){
            $main_classes .= ' beta-header-activated';
        }
        return $main_classes;
    }

    /**
     * Get last_ns_group.
     *
     * @param User $user
     * @return mixed
     */
    public function last_ns_group($user)
    {
        return $this->settings->getLastNsGroupForUser($user);
    }

    /**
     * Get user_full_data. full json to frontend website.
     *
     * @param User $user
     * @param array $additional_read_innovation_ids
     * @return array
     */
    public function user_full_data($user, $additional_read_innovation_ids = array())
    {
        if(!$user){
            return array();
        }
        $all_innovations = $this->getAllInnovationsArray();
        $all_consolidation = json_decode($this->redis->get($this->redis_prefix.'pri_allconsolidation'), true);
        $other_datas = json_decode($this->redis->get($this->redis_prefix.'pri_otherdatas'), true);
        $all_innovations_titles_for_user_manage_filters = $this->em->getRepository('AppBundle:Innovation')->getAllInnovationTitlesForUserManageFilters($user);
        $all_entities_for_user = $this->em->getRepository('AppBundle:Entity')->getAllEntitiesForUser($user);
        $main_skills = $this->em->getRepository('AppBundle:Skill')->getMainSkills(true);
        $full_data = array(
            'all_innovations' => $all_innovations,
            'all_consolidation' => $all_consolidation,
            'monitor_filters' => $user->getFilters($other_datas, $all_innovations_titles_for_user_manage_filters, true),
            'user_rights' => $user->getUserRightsArray(),
            'ping' => $this->settings->getPingTimestamp(),
            'user_roles' => $user->getUserRolesArray(),
            'update' => date('Y-m-d H:i:s'),
            'other_datas' => $other_datas,
            'popup-new' => array(
                'entities' => $all_entities_for_user,
                'main_skills' => $main_skills
            ),
            'main_classes' => $this->main_classes($user),
            'current_user' => $user->toArray(),
            'search_history' => $user->getLastSearchHistories(),
        );
        return self::secureFullDataForUser($user, $full_data, $additional_read_innovation_ids);
    }


    /**
     * Get user_all_secured_innovations.
     *
     * @param $user
     * @param array $additional_read_innovation_ids
     * @return array
     */
    public function user_all_secured_innovations($user, $additional_read_innovation_ids = array())
    {
        if(!$user){
            return array();
        }
        $all_innovations = $this->getAllInnovationsArray();
        $full_data = array(
            'all_innovations' => $all_innovations
        );
        $secured = self::secureFullDataForUser($user, $full_data, $additional_read_innovation_ids);
        return $secured['all_innovations'];
    }


    /**
     * User has empty manage list.
     *
     * @param User $user
     * @return bool
     */
    public function userHasEmptyManageList(User $user){
        if($user->hasAdminRights() || $user->hasManagementRights()){
            return (count($this->getAllInnovationsArray()) == 0);
        }
        return (count($user->getUserInnovationRights()) == 0);

    }

    /**
     * Get all innovations array
     *
     * @return array
     */
    public function getAllInnovationsArray()
    {
        return json_decode($this->redis->get($this->redis_prefix.'pri_allinnovations'), true);
    }


    /**
     * Dashboard get innovations incomplete financial data array.
     *
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public function dashboardGetInnovationsIncompleteFinancialData($offset = 0, $limit = 6)
    {
        $all_innovations = $this->getAllInnovationsArray();
        $innovations = array();
        $sortArray = array();
        foreach ($all_innovations as $proper_product) {
            if($proper_product['empty_fields']['financial'] > 0){
                $innovations[] = $proper_product;
                $sortArray['empty_fields']['financial'][] = $proper_product['empty_fields']['financial'];
            }
        }
        array_multisort($sortArray['empty_fields']['financial'], SORT_DESC, $innovations);
        return array_slice($innovations, $offset, $limit);
    }

    /**
     * Check redis datas.
     *
     * @return bool
     */
    public function checkRedisDatas(){
        return (json_decode($this->redis->get($this->redis_prefix.'pri_otherdatas'), true) &&
            json_decode($this->redis->get($this->redis_prefix.'pri_allinnovations'), true) &&
            json_decode($this->redis->get($this->redis_prefix.'pri_allinnovations_excel'), true) &&
            json_decode($this->redis->get($this->redis_prefix.'pri_allconsolidation'), true)
        );
    }

    /**
     * Get innnovation array by id.
     *
     * @param int $id
     * @return null|array
     */
    public function getInnovationArrayById($id)
    {
        $innovations = $this->getAllInnovationsArray();
        foreach ($innovations as $innovation){
            if($innovation['id'] == $id){
                return $innovation;
            }
        }
        return null;
    }

    /**
     * Get innnovations array by array ids.
     *
     * @param array $array_ids
     * @return array
     */
    public function getInnovationsArrayByArrayIds($array_ids)
    {
        $ret = array();
        $innovations = $this->getAllInnovationsArray();
        if(count($array_ids) == 0){
            return $innovations;
        }
        foreach ($innovations as $innovation){
            if(in_array($innovation['id'], $array_ids)){
                $ret[] = $innovation;
            }
        }
        return $ret;
    }

    /**
     * Get innnovations excel by array ids.
     *
     * @param array $ids
     * @return array
     */
    public function getInnovationsExcelByArrayIds($ids)
    {
        $ret = array();
        $innovations = json_decode($this->redis->get($this->redis_prefix.'pri_allinnovations_excel'), true);
        if(count($ids) == 0){
            return $innovations;
        }
        if(!$innovations){
            return $this->em->getRepository('AppBundle:Innovation')->getAllInnovationsForExcelExport($this->settings, $ids);
        }
        foreach ($innovations as $innovation){
            if(in_array($innovation['id'], $ids)){
                $ret[] = $innovation;
            }
        }
        return $ret;
    }

    /**
     * Action is possible.
     *
     * @param User $user
     * @return bool
     */
    public function actionIsPossible($user){
        if(!$this->checkRedisDatas()){
            return false;
        }
        if($this->settings->getIsMaintenanceEnabled() && !$user->hasAdminRights()){
            return false;
        }
        return true;
    }

    /**
     * Get redis cache progress.
     *
     * @return string
     */
    public function getRedisCacheProgress(){
        $progress = $this->redis->get($this->redis_prefix.'redis_cache_progress');
        return ($progress) ? $progress : 0;
    }

    /**
     * Get mouseflow path.
     *
     * @param User $user
     * @param $current_path
     * @return string
     */
    public function getMouseflowPath($user, $current_path){
        $current_path = Settings::getXssCleanString($current_path);
        if(!$user){
            return $current_path."?type=unconnected";
        }
        $path_exploder = explode("?", $current_path);
        $current_path = $path_exploder[0];
        $user_type = $user->getMouseflowType();
        $post_url = '?type='.$user_type;


        if (strpos($current_path, '/explore/') !== false) {
            $exploder = explode("/", $current_path);
            $innovation_id = intval($exploder[2]);
            $innovation = ($innovation_id) ? $this->em->getRepository('AppBundle:Innovation')->findActiveInnovation($innovation_id) : null;
            if ($innovation) {
                $ret_url = "/explore/<detail>";
                if($user_type == 'maker') {
                    $post_url = ($user->canEditThisInnovation($innovation)) ? "'?type=maker" : "'?type=maker_explorer";
                }
                $post_url .= $this->getProperTabForUrl($current_path);
                $post_url .= $innovation->getProperStageForUrl();
                $post_url .= $innovation->getProperClassificationForUrl();

                return $ret_url.$post_url;
            }
        }
        $simple_strpos_urls = [
            '/user',
            '/content/search',
        ];
        foreach($simple_strpos_urls as $simple_strpos_url){
            if(strpos($current_path, $simple_strpos_url) !== false){
                return $simple_strpos_url.$post_url;
            }
        }
        return $current_path.$post_url;
    }

    /**
     * Get proper tab for url.
     * @param $current_path
     * @return string
     */
    private function getProperTabForUrl($current_path)
    {
        if (strpos($current_path, '/explore/') !== false) {
            if (strpos($current_path, '/tab/') !== false) {
                $exploder = explode("/tab/", $current_path);
                return '&tab='.$exploder[1];
            }
            return '&tab=overview';
        }
        return '';
    }
    
    /**
     * Get global wording (for website multiversion).
     *
     * @return array
     */
    public function get_global_wording()
    {
        $global_wording = array();
        $global_wording['version'] = $this->container->getParameter('app.version');
        $global_wording['title'] = $this->container->getParameter('app.website_title');
        $global_wording['company'] = $this->container->getParameter('app.company');
        $global_wording['volume_unit'] = $this->container->getParameter('app.volume_unit');
        $global_wording['image_folder_all'] = $this->container->getParameter('app.image_folder_all');
        return $global_wording;
    }


    /**
     * Dashboard keys.
     *
     * @return array
     */
    public function dashboard_keys()
    {
        return array(
            'DASHBOARD_TYPE_NEW_BACK' => DashboardController::DASHBOARD_TYPE_NEW_BACK,
            'DASHBOARD_TYPE_NEW' => DashboardController::DASHBOARD_TYPE_NEW,
            'DASHBOARD_TYPE_OUT' => DashboardController::DASHBOARD_TYPE_OUT,
            'DASHBOARD_TYPE_IN_MARKET_SOON' => DashboardController::DASHBOARD_TYPE_IN_MARKET_SOON,
            'DASHBOARD_TYPE_IN_MARKET_UPDATE' => DashboardController::DASHBOARD_TYPE_IN_MARKET_UPDATE,
        );
    }

    /**
     * Get all innovations array
     *
     * @param string $type
     * @return string
     */
    public function getDashboardTitleForType($type)
    {
        return DashboardController::getDashboardTitleForType($type);
    }

    /**
     * Get dashboard html table tr.
     * 
     * @param $item
     * @param bool $max_size
     * @return string
     */
    public function get_dashboard_html_table_tr($item, $max_size = false)
    {
        $ret = '<tr>';
        $ret .= '<td style="width: 44px;"><span class="icon-stage icon tooltip-right-tr force-tooltip '.$item['stage_icon_class'].'" title="'.$item['stage_name'].'"></span></td>'; // Stage icon
        $ret .= '<td class="td-micro-loading-title text-align-left text-transform-uppercase">';
        $ret .= '<a class="link_explore_detail inner-td-ellipsis right-10 tooltip-right-tr color-000 font-size-13 font-montserrat-700" title="'.$item['innovation_name'].'" href="' . $item['innovation_url'] . '">';
        $ret .= $item['innovation_name'];
        $ret .= '</a>';
        $ret .= '</td>';
        $width = ($max_size) ? 300 : 150;
        $ret .= '<td class="font-work-sans-400 font-size-13 color-9b9b9b tooltip-right-tr " style="width: '.$width.'px;">';
        $ret .= '<span class="inner-td-ellipsis text-align-left tooltip-right-tr" title="'.$item['entity_brand'].'">';
        $ret .= $item['entity_brand'];
        $ret .= '</span>';
        $ret .= '</td>';
        $width = ($max_size) ? 160 : 80;
        $ret .= '<td class="text-align-right font-work-sans-400 font-size-13 color-000 " style="width: '.$width.'px;">';
        $ret .= '<span class="tooltip-right-tr force-tooltip" title="' . $item['placeholder_date'] . '">';
        $ret .= $item['relative_created'];
        $ret .= '</span>';
        $ret .= '</td>';
        $ret .= '</tr>';
        return $ret;
    }

    /**
     * Get dashboard html table tr.
     *
     * @param $item
     * @param bool $max_size
     * @return string
     */
    public function get_dashboard_html_table_tr_incomplete_financial_data($item, $max_size = false)
    {
        $stage_icon_class = 'light '.$item['current_stage'];
        $stage_title = ($item['current_stage_id']) ? Stage::returnCurrentStageTitle($item['current_stage_id']) : '';
        if ($item['is_frozen']) {
            $stage_title .= ' (frozen)';
            $stage_icon_class .= ' frozen';
        }
        $entity_brand =  ($item['entity']) ? $item['entity']['title'] : '';
        if ($item['brand']) {
            $entity_brand .= ($entity_brand != '') ? ', ' . $item['brand']['title'] : $item['brand']['title'];
        }

        $ret = '<tr>';
        $ret .= '<td style="width: 44px;"><span class="icon-stage icon tooltip-right-tr force-tooltip '.$stage_icon_class.'" title="'.$stage_title.'"></span></td>'; // Stage icon
        $ret .= '<td class="td-micro-loading-title text-align-left text-transform-uppercase">';
        $ret .= '<a class="link_explore_detail inner-td-ellipsis right-10 tooltip-right-tr color-000 font-size-13 font-montserrat-700" title="'.$item['title'].'" href="/explore/' . $item['id'] . '">';
        $ret .= $item['title'];
        $ret .= '</a>';
        $ret .= '</td>';
        $width = ($max_size) ? 300 : 150;
        $ret .= '<td class="font-work-sans-400 font-size-13 color-9b9b9b tooltip-right-tr " style="width: '.$width.'px;">';
        $ret .= '<span class="inner-td-ellipsis text-align-left tooltip-right-tr" title="'.$entity_brand.'">';
        $ret .= $entity_brand;
        $ret .= '</span>';
        $ret .= '</td>';
        $width = ($max_size) ? 160 : 80;
        $ret .= '<td class="text-align-right font-work-sans-400 font-size-13 color-000 " style="width: '.$width.'px;">';
        $ret .= '<span class="round-nb-emtpy-field-dashboard tooltip-right-tr force-tooltip" title="' . $item['empty_fields']['financial'] . ' incomplete financial datas">'.$item['empty_fields']['financial'].'</span>';
        $ret .= '</td>';
        $ret .= '</tr>';
        return $ret;
    }

    /**
     * secureFullDataForUser
     *
     * @param User $user
     * @param array $full_data
     * @param array $additional_read_innovation_ids
     *
     * @return array
     */
    public static function secureFullDataForUser($user, $full_data, $additional_read_innovation_ids = array()){
        if(!$user->hasMonitorAccess()){
            unset($full_data['other_datas']['perimeter_json']);
        }
        foreach ($full_data['all_innovations'] as $index => $data) {
            $is_limited = $user->isLimitedOnInnovation($data);
            $is_on_explore = Innovation::arrayIsEnabledOnExplore($data);
            $is_on_additional_innovations_ids = in_array($data['id'], $additional_read_innovation_ids);
            // if not limited, or innovation in explore or additional_read_right (like ask feedback)
            if(!$is_limited || ($is_limited && $is_on_explore) || $is_on_additional_innovations_ids){
                $full_data['all_innovations'][$index] = Innovation::arrayToFrontend($full_data['all_innovations'][$index], $is_limited);
            }else{
                unset($full_data['all_innovations'][$index]);
            }
        }
        $full_data['all_innovations'] = array_values($full_data['all_innovations']);
        return $full_data;
    }

    /**
     * Is current mode dev
     * @return bool
     */
    public static function is_current_mode_dev(){
        return (array_key_exists('CURRENT_MODE', $_ENV) && $_ENV['CURRENT_MODE'] == 'dev');
    }
}

