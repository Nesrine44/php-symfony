<?php
/**
 * Created by PhpStorm.
 * User: jonathan.garcia
 * Date: 11/07/2018
 * Time: 14:13
 */


namespace AppBundle;

use AppBundle;
use AppBundle\Entity\User;
use AppBundle\Entity\Innovation;
use AppBundle\Entity\Settings;
use AppBundle\Entity\FinancialData;

require_once __DIR__ . '/../../vendor/autoload.php';

use Dtc\QueueBundle\Util\Util;
use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\IOFactory;
use PhpOffice\PhpPresentation\Shape\Drawing\Base64;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Style\Alignment;
use PhpOffice\PhpPresentation\DocumentLayout;
use PhpOffice\PhpPresentation\Style\Border;

const STATUS_INNOVATION_DRAFT = 1;
const STATUS_INNOVATION_SUBMITTED = 2;
const STATUS_INNOVATION_VALIDATED = 3;
const STATUS_INNOVATION_NEW = 6;
const STATUS_INNOVATION_MODIFIED = 7;

class UtilsPpt
{
    const FONT_MONTSERRAT = 'Montserrat';
    const FONT_WORK_SANS = 'Work Sans';

    const COLOR_BLACK = 'FF000000';
    const COLOR_WHITE = 'FFFFFFFF';
    const COLOR_RED_MISSING_DATA = 'FFff94a1';
    const COLOR_b5b5b5 = 'FFb5b5b5';
    const COLOR_d8d8d8 = 'FFd8d8d8';
    const COLOR_005095 = 'FF005095';
    const COLOR_9b9b9b = 'FF9b9b9b';
    const COLOR_fab700 = 'FFfab700';
    const COLOR_80a7d0 = 'FF80a7d0';
    const COLOR_e3eaf1 = 'FFe3eaf1';
    const COLOR_CA1A3C = 'FFCA1A3C';
    const COLOR_C9193C = 'FFC9193C';
    const COLOR_45ab34 = 'FF45ab34';
    const COLOR_03AFF0 = 'FF03AFF0';
    const ICON_PERCENT_POSITIVE = "icons/percent/up.png";
    const ICON_PERCENT_NEGATIVE = "icons/percent/down.png";

    /**
     *
     * Generation du ppt top contributor performance
     *
     * @param $redis
     * @param $data_array
     * @param $settings
     * @param $user
     * @param $path
     * @param int|null $export_id
     * @param $awsS3Uploader
     * @return string
     * @throws \Exception
     *
     */
    public static function ppt_top_contributor($redis, $data_array, $settings, $user, $path, $export_id, $awsS3Uploader)
    {
        if ($export_id) {
            $redis->set($export_id, Settings::EXPORT_PROGRESS_BEFORE_DATA_LOADED);
        }

        $elements = array(
            'ppt' => new PhpPresentation(),
            'current_slide' => null,
            'nb_slide' => null,
        );

        $elements['ppt']->getProperties()->setCreator('Pernod Ricard')
            ->setLastModifiedBy('Pernod Ricard Team')
            ->setTitle('Pernod Ricard Entity performance review')
            ->setSubject('Top Priority Innovations')
            ->setDescription('')
            ->setKeywords('office 2007 openxml libreoffice odt php')
            ->setCategory('Innovation');

        $cx = 1918 * 9525;
        $cy = 1078 * 9525;
        $layout = array('cx' => $cx, 'cy' => $cy);
        $elements['ppt']->getLayout()->setDocumentLayout($layout, true);
        $elements['ppt']->getLayout()->setCX(1918, DocumentLayout::UNIT_PIXEL);
        $elements['ppt']->getLayout()->setCY(1078, DocumentLayout::UNIT_PIXEL);
        $elements['ppt']->removeSlideByIndex(0);
        $elements['nb_slide'] = 0;

        $el_date = $settings->getCurrentFinancialDate();

        $is_admin = ($user->hasAdminRights() || $user->hasManagementRights());

        $data = self::getInnovationsForExportPerformancePpt($el_date, $data_array, $is_admin);

        if ($export_id) {
            $redis->set($export_id, Settings::EXPORT_PROGRESS_DATA_LAUNCH);
        }

        $elements = self::addGuardPage($elements, $data, $el_date, $is_admin);
        $elements = self::addTopPriorityInnovationsPage($elements, $data, $el_date, $is_admin);

        if ($export_id) {
            $redis->set($export_id, 20);
        }

        #BIG BET
        $big_bets_innovations = $data['big_bet']['innovations'];
        if (count($big_bets_innovations) > 10) {
            $max = 5;
            $nb = floor(count($big_bets_innovations) / $max);
            for ($i = 0; $i < $nb; $i++) {
                $offset = $i * $max;
                $limit = ($i + 1) * $max;
                $part_big_bets_innovations = array_slice($big_bets_innovations, $offset, $limit);
                $elements = self::addPerformancePage($elements, $data, 'big_bet', $part_big_bets_innovations, $el_date, $is_admin, $settings);
            }
        } else {
            $elements = self::addPerformancePage($elements, $data, 'big_bet', $big_bets_innovations, $el_date, $is_admin, $settings);
        }

        if ($export_id) {
            $redis->set($export_id, 40);
        }

        // Top contributors
        $top_innovations = $data['top']['innovations'];
        $elements = self::addPerformancePage($elements, $data, 'top', $top_innovations, $el_date, $is_admin, $settings);

        if ($export_id) {
            $redis->set($export_id, 65);
        }

        // Negative CAAP
        $worst_innovations = $data['worst']['innovations'];
        $elements = self::addPerformancePage($elements, $data, 'worst', $worst_innovations, $el_date, $is_admin, $settings);

        if ($export_id) {
            $redis->set($export_id, 75);
        }

        // High Investment
        $high_innovations = $data['high']['innovations'];
        $elements = self::addPerformancePageHighInvestment($elements, $data, 'high', $high_innovations, $el_date, $is_admin, $settings);

        if ($export_id) {
            $redis->set($export_id, 90);
        }

        self::savePPT($elements, $path, $awsS3Uploader, $redis, $export_id);

        if ($export_id) {
            $redis->set($export_id, Settings::EXPORT_PROGRESS_MAX);
        }

        return 'Success';

    }

    /**
     *
     * Generation du ppt quali et quali full
     *
     * @param $redis
     * @param $data
     * @param $settings
     * @param $type
     * @param $path
     * @param int|null $export_id
     * @param $awsS3Uploader
     * @return string
     * @throws \Exception
     *
     */
    public static function ppt_quali($redis, $data, $settings, $type, $path, $export_id, $awsS3Uploader)
    {
        if ($export_id) {
            $redis->set($export_id, Settings::EXPORT_PROGRESS_BEFORE_DATA_LOADED);
        }

        $elements = array(
            'ppt' => new PhpPresentation(),
            'current_slide' => null,
            'nb_slide' => null,
        );


        $elements['ppt']->getProperties()->setCreator("Pernod Ricard")
            ->setLastModifiedBy("Pernod Ricard" . ' Team')
            ->setTitle('Export Quali')
            ->setSubject('Qualitative export')
            ->setDescription('')
            ->setKeywords('office 2007 openxml libreoffice odt php')
            ->setCategory('Innovation');

        $cx = 1918 * 9525;
        $cy = 1078 * 9525;
        $layout = array('cx' => $cx, 'cy' => $cy);
        $elements['ppt']->getLayout()->setDocumentLayout($layout, true);
        $elements['ppt']->getLayout()->setCX(1918, DocumentLayout::UNIT_PIXEL);
        $elements['ppt']->getLayout()->setCY(1078, DocumentLayout::UNIT_PIXEL);

        $elements['ppt']->removeSlideByIndex(0);
        $elements['nb_slide'] = 0;


        $inno = self::getProperInnovationForQualiQuanti($data, $settings);

        $elements = self::addInnovationQualiPage1($elements, $settings->getCurrentFinancialDate(), $inno);

        $innovation_is_a_service = Innovation::innovationArrayIsAService($inno);
        $el_date = $settings->getCurrentFinancialDate();

        if ($export_id) {
            $redis->set($export_id, Settings::EXPORT_PROGRESS_DATA_LAUNCH);
        }

        if (!$innovation_is_a_service &&  in_array($inno['current_stage'], array('discover', 'ideate'))) {
            $elements = self::addInnovationVeryEarlyQualiPage($elements, $el_date, $inno);
        }else if (!in_array($inno['current_stage'], array('discover', 'ideate')) && !$innovation_is_a_service) {
            $elements = self::addInnovationQualiPage($elements, $el_date, $inno);
            $redis->set($export_id, 30);
            if ((!in_array($inno['current_stage'], array('discover', 'ideate', 'experiment'))) && $type == "full") {
                $elements = self::addInnovationQuantiPage($elements, $el_date, $inno);
                $redis->set($export_id, 60);
            }
        }
        $redis->set($export_id, 91);

        self::savePPT($elements, $path, $awsS3Uploader, $redis, $export_id);
        if ($export_id) {
            $redis->set($export_id, Settings::EXPORT_PROGRESS_MAX);
        }

        return 'Success';
    }


    /**
     *
     * Genration du ppt full overview quali
     *
     * @param $em
     * @param $redis
     * @param $data_array
     * @param $settings
     * @param $user
     * @param $params
     * @param $path
     * @param init|null $export_id
     * @param $awsS3Uploader
     * @throws \Exception
     *
     */
    public static function ppt_overview_full_quali($em, $redis, $data_array, $settings, $user, $params, $path, $export_id, $awsS3Uploader)
    {
        ini_set('memory_limit', '-1');
        if ($export_id) {
            $redis->set($export_id, Settings::EXPORT_PROGRESS_BEFORE_DATA_LOADED);
        }
        $el_date = $settings->getCurrentFinancialDate();

        $data = self::getInfosForInnovationFormPPT($data_array, $el_date, false, false, false, true, $settings, $em);

        if ($export_id) {
            $redis->set($export_id, Settings::EXPORT_PROGRESS_DATA_LAUNCH);
        }

        $elements = array(
            'ppt' => new PhpPresentation(),
            'current_slide' => null,
            'nb_slide' => null,
        );

        $elements['ppt']->getProperties()->setCreator("Pernod Ricard")
            ->setLastModifiedBy("Pernod Ricard" . ' Team')
            ->setTitle("Pernod Ricard" . ' Innovations Project Overview Quali')
            ->setSubject('Latest Innovations')
            ->setDescription('')
            ->setKeywords('office 2007 openxml libreoffice odt php')
            ->setCategory('Innovation');

        $cx = 1918 * 9525;
        $cy = 1078 * 9525;
        $layout = array('cx' => $cx, 'cy' => $cy);
        $elements['ppt']->getLayout()->setDocumentLayout($layout, true);
        $elements['ppt']->getLayout()->setCX(1918, DocumentLayout::UNIT_PIXEL);
        $elements['ppt']->getLayout()->setCY(1078, DocumentLayout::UNIT_PIXEL);

        $elements['ppt']->removeSlideByIndex(0);
        $elements['nb_slide'] = 0;

        $paramexport['overview_subtitle'] = self::getOverviewSubtitleForExport($em, $params, $settings, $user);

        if ($export_id) {
            $redis->set($export_id, 13);
        }
        $elements = self::addEntityQualiFirstPage($elements, null, $el_date);
        if ($export_id) {
            $redis->set($export_id, 14);
        }
        $elements = self::addOverviewPage($elements, $paramexport, $data['total']['overview']);
        if ($export_id) {
            $redis->set($export_id, 15);
        }

        $elements = self::addAllProductsQualiFull($redis, $elements, $data, $el_date, $export_id);

        self::savePPT($elements, $path, $awsS3Uploader, $redis, $export_id);

        if ($export_id) {
            $redis->set($export_id, Settings::EXPORT_PROGRESS_MAX);
        }

    }

    /**
     * Get proper params for overview export ppt.
     * @param $params
     * @return array
     */
    public static function getProperParamsForOverviewExportPpt($params)
    {
        if (!$params || ($params && !is_array($params))) {
            return array(
                'filters' => array(),
                'innovations_ids' => array()
            );
        }
        if (!array_key_exists('filters', $params)) {
            $params['filters'] = array();
        }
        if (!array_key_exists('innovations_ids', $params)) {
            $params['innovations_ids'] = array();
        }
        return $params;
    }

    /**
     * Get overview subtitle for export.
     * @param $em
     * @param $params
     * @param $settings
     * @param $user
     * @return string
     */
    public static function getOverviewSubtitleForExport($em, $params, $settings, $user)
    {
        /**
         * - Si HQ pas de filtre : All innovations
         * - Si filtre sur 1 Brand uniquement =  On utilise le nom de la Brand
         * - Si filtre sur 1 Entity uniquement = On utilise le nom de l'entity
         * - Sinon on met uniquement "LE1 17"
         */
        $libelle_financial = $settings->getFinancialDateLibelle();
        $subtitle = "";
        if (array_key_exists('current_stage', $params['filters']) && $params['filters']['current_stage'] == array('0', '1', '2', '3', '4')) {
            unset($params['filters']['current_stage']);
        }
        if (($user->hasAdminRights() || $user->hasManagementRights()) && count($params['filters']) == 0) {
            // Si HQ pas de filtre
            $subtitle = "All innovations • ";
        } elseif (count($params['filters']) == 1 && array_key_exists('brand', $params['filters']) && count($params['filters']['brand']) == 1) {
            // si filtre uniquement sur une brand
            $the_brand_id = $params['filters']['brand'][0];
            $the_brand = $em->getRepository('AppBundle:Brand')->find(intval($the_brand_id));
            if ($the_brand) {
                $subtitle = $the_brand->getTitle() . " • ";
            }
        } elseif (count($params['filters']) == 1 && array_key_exists('entity', $params['filters']) && count($params['filters']['entity']) == 1) {
            // si filtre uniquement sur une entity
            $the_entity_gid = $params['filters']['entity'][0];
            $the_entity = $em->getRepository('AppBundle:Entity')->find(intval($the_entity_gid));
            if ($the_entity) {
                $subtitle = $the_entity->getTitle() . " • ";
            }
        }
        $subtitle .= $libelle_financial;
        return $subtitle;
    }


    /**
     * Methode reprise sur drupal
     *
     *
     * @param $elements
     * @param $path
     * @throws \Exception
     */
    public static function savePPT($elements, $path, $awsS3Uploader, $redis, $export_id)
    {
        $directory_path = str_replace(basename($path), '', $path);
        if (!file_exists($directory_path)) {
            mkdir($directory_path, 0777, true);
        }
        if (file_exists($path)) unlink($path);
        // SAVE IN LOCAL CONTAINER
        $oWriterPPTX = IOFactory::createWriter($elements['ppt']);
        $oWriterPPTX->save($path);

        if ($export_id) {
            $redis->set($export_id, 96);
        }

        // UPLOAD TO AWS
        $aws_path = 'exports/' . basename($directory_path) . '/' . basename($path);
        $awsS3Uploader->uploadFile($aws_path, $path);

        if ($export_id) {
            $redis->set($export_id, 98);
        }

        // THEN DELETE FROM LOCAL CONTAINER
        if (file_exists($path)) unlink($path);

        if ($export_id) {
            $redis->set($export_id, 99);
        }
    }

    /**
     * Methode reprise sur drupal
     *
     * @param $settings
     * @param $elements
     * @param null $entity_id
     * @param null $el_date
     * @param null $title_entity
     * @return mixed
     */
    public static function addFirstPage($settings, $elements, $entity_id = null, $el_date = null, $title_entity = null)
    {
        $elements['nb_slide']++;
        $elements['current_slide'] = $elements['ppt']->createSlide();
        // Ajout du background
        $shape = $elements['current_slide']->createDrawingShape();
        $shape->setName('')
            ->setDescription('')
            ->setPath(__DIR__ . '/../../web/ppt/couv.jpg')
            ->setHeight(1080)
            ->setOffsetX(0)
            ->setOffsetY(0);
        $shape->getShadow()->setVisible(false)
            ->setDirection(0)
            ->setDistance(0);


        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(100)
            ->setWidth(1000)
            ->setOffsetX(460)
            ->setOffsetY(580);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $text = $settings->getFinancialDateLibelle();
        $textRun = $shape->createTextRun($text);
        $textRun->getFont()
            ->setSize(40)
            ->setName('Century Gothic')
            ->setColor(new Color(UtilsPpt::COLOR_WHITE));


        if ($entity_id) {
            #  $affiliate = pri_get_node_by_type_and_data('affiliate', array('id' => $entity_id));
            $affiliate = null;
            if ($affiliate) {
                $shape = $elements['current_slide']->createRichTextShape()
                    ->setHeight(100)
                    ->setWidth(1000)
                    ->setOffsetX(460)
                    ->setOffsetY(645);
                $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $text = $affiliate->title;
                $textRun = $shape->createTextRun($text);
                $textRun->getFont()
                    ->setSize(30)
                    ->setName('Century Gothic')
                    ->setColor(new Color(UtilsPpt::COLOR_WHITE));


            }
        } elseif ($title_entity) {
            $shape = $elements['current_slide']->createRichTextShape()
                ->setHeight(100)
                ->setWidth(1000)
                ->setOffsetX(460)
                ->setOffsetY(645);
            $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $text = $title_entity;
            $textRun = $shape->createTextRun($text);
            $textRun->getFont()
                ->setSize(30)
                ->setName('Century Gothic')
                ->setColor(new Color(UtilsPpt::COLOR_WHITE));
        }

        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(100)
            ->setWidth(1000)
            ->setOffsetX(460)
            ->setOffsetY(700);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $text = date('F j Y');
        $textRun = $shape->createTextRun($text);
        $textRun->getFont()
            ->setSize(20)
            ->setName('Century Gothic')
            ->setColor(new Color(UtilsPpt::COLOR_WHITE));

        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(50)
            ->setWidth(1920)
            ->setOffsetX(0)
            ->setOffsetY(1030);
        $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        //$shape->getFill()->setFillType(Fill::FILL_SOLID)->setRotation(90)->setStartColor(new Color(UtilsPpt::COLOR_CA1A3C))->setEndColor(new Color(UtilsPpt::COLOR_C9193C));
        $textRun = $shape->createTextRun("   IMPORTANT NOTE: This document is highly confidential. This copy should not be further communicated to any whom.");
        $textRun->getFont()
            ->setSize(20)
            ->setName('Century Gothic')
            ->setColor(new Color(UtilsPpt::COLOR_WHITE));

        return $elements;
    }

    /**
     * Methode reprise sur drupal
     *
     * @param $date
     * @param bool $edition
     * @param bool $add_NA_fields
     * @param bool $add_previous_helper_field
     * @return array|null
     */
    public static function getFinancialDataPostFields($date, $edition = true, $add_NA_fields = false, $add_previous_helper_field = false)
    {
        #if (!$date) {
        #    $the_date = new DateTime(get_financial_date());
        #} else {
        $the_date = new \DateTime($date);
        #}
        $ret = null;
        $year = intval($the_date->format('y'));
        $month = intval($the_date->format('n'));
        $previous_year = $year - 1;
        $previous_of_previous_year = $year - 2;
        $futur_year = $year + 1;

        $year_libelle = substr($year, -2);
        $previous_year_libelle = substr($previous_year, -2);
        $previous_of_previous_year_libelle = substr($previous_of_previous_year, -2);
        $futur_year_libelle = substr($futur_year, -2);

        $disabled_mode = ($edition == false);
        if ($month < 4) {
            $nb_trimestre = 3;
        } elseif ($month < 7) {
            $nb_trimestre = 4;
        } elseif ($month < 10) {
            $nb_trimestre = 1;
        } else {
            $nb_trimestre = 2;
        }
        switch ($nb_trimestre) {
            case 1:
                $ret = array();
                $ret['A' . $previous_year_libelle] = array('disabled' => true, 'mandatory' => false, 'position' => '0');
                $ret['A' . $year_libelle] = array('disabled' => $disabled_mode, 'mandatory' => false, 'position' => '1');
                if ($add_NA_fields) {
                    $ret['N/A'] = array('disabled' => true, 'mandatory' => false, 'position' => '2');
                    $ret['B' . $futur_year_libelle . ' (final)'] = array('disabled' => $disabled_mode, 'mandatory' => true, 'position' => '3');
                } else {
                    $ret['B' . $futur_year_libelle . ' (final)'] = array('disabled' => $disabled_mode, 'mandatory' => true, 'position' => '2');
                }
                break;
            case 2:
                $ret = array(
                    'A' . $previous_year_libelle => array('disabled' => true, 'mandatory' => false, 'position' => '0'),
                    'A' . $year_libelle => array('disabled' => true, 'mandatory' => false, 'position' => '1'),
                    'B' . $futur_year_libelle . ' (final)' => array('disabled' => true, 'mandatory' => false, 'position' => '2', 'libelle' => 'B' . $futur_year_libelle),
                    'LE1 ' . $futur_year_libelle => array('disabled' => $disabled_mode, 'mandatory' => true, 'position' => '3'),
                );
                break;
            case 3:
                $ret = array();
                $ret['A' . $previous_of_previous_year_libelle] = array('disabled' => true, 'mandatory' => false, 'position' => '0');
                $ret['A' . $previous_year_libelle] = array('disabled' => true, 'mandatory' => false, 'position' => '1');
                $ret['B' . $year_libelle . ' (final)'] = array('disabled' => true, 'mandatory' => false, 'position' => '2');
                if ($add_previous_helper_field) {
                    $ret['LE1 ' . $year_libelle] = array('disabled' => true, 'mandatory' => false, 'position' => '3');
                    $ret['LE2 ' . $year_libelle] = array('disabled' => $disabled_mode, 'mandatory' => true, 'position' => '4');
                } else {
                    $ret['LE2 ' . $year_libelle] = array('disabled' => $disabled_mode, 'mandatory' => true, 'position' => '3');
                }
                break;
            case 4:
                $ret = array();
                $ret['A' . $previous_year_libelle] = array('disabled' => true, 'mandatory' => false, 'mandatory' => false, 'position' => '0');
                $ret['B' . $year_libelle . ' (final)'] = array('disabled' => true, 'mandatory' => false, 'position' => '1');
                if ($add_previous_helper_field) {
                    $ret['LE2 ' . $year_libelle] = array('disabled' => true, 'mandatory' => false, 'position' => '2');
                    $ret['LE3 ' . $year_libelle] = array('disabled' => $disabled_mode, 'mandatory' => true, 'position' => '3');
                    $ret['B' . $futur_year_libelle . ' (initial)'] = array('disabled' => $disabled_mode, 'mandatory' => false, 'position' => '4');
                } else {
                    $ret['LE3 ' . $year_libelle] = array('disabled' => $disabled_mode, 'mandatory' => true, 'position' => '2');
                    $ret['B' . $futur_year_libelle . ' (initial)'] = array('disabled' => $disabled_mode, 'mandatory' => false, 'position' => '3');
                }
                break;
        }

        return $ret;
    }

    /**
     * Methode reprise sur drupal
     *
     * @param $current_stage
     * @param null $date
     * @param bool $edition
     * @param bool $add_NA_fields
     * @param bool $add_previous_helper_field
     * @param bool $is_a_service
     * @return array
     */
    public static function getFinancialDataFieldsForStage($current_stage, $date = null, $edition = true, $add_NA_fields = false, $add_previous_helper_field = false, $is_a_service = false)
    {
        $ret = array(
            'to_display_list' => array(),
            'list' => array(),
            'details' => array(),
        );
        $post_fields = self::getFinancialDataPostFields($date, $edition, $add_NA_fields, $add_previous_helper_field);
        if ($is_a_service) {
            $pre_fields = array('central_investment', 'advertising_promotion');
        } elseif ($current_stage == 'ideate' || $current_stage == 'discover') {
            $pre_fields = array('central_investment');
        } elseif ($current_stage == 'experiment') {
            $pre_fields = array('volume', 'central_investment', 'advertising_promotion');
        } elseif ($current_stage == 'incubation') {
            $pre_fields = array('volume', 'net_sales', 'contributing_margin', 'central_investment', 'advertising_promotion');
        } elseif ($current_stage == 'Scale up') {
            $pre_fields = array('volume', 'net_sales', 'contributing_margin', 'central_investment', 'advertising_promotion');
        } elseif ($current_stage == 'empty') {
            return $ret;
        } elseif ($current_stage == 'Empty') {
            return $ret;
        } elseif ($current_stage == 'Permanent range') {
            $pre_fields = array('volume', 'net_sales', 'contributing_margin', 'central_investment', 'advertising_promotion');
        } elseif ($current_stage == 'discontinued') {
            $pre_fields = array('volume', 'net_sales', 'contributing_margin', 'central_investment', 'advertising_promotion');
        } else {
            return $ret;
        }

        foreach ($post_fields as $post_libelle => $field) {
            $tr_libelle = (array_key_exists('libelle', $field)) ? $field['libelle'] : $post_libelle;
            $libelle = $post_libelle;
            $id = str_replace(' ', '_', $libelle);
            $id = str_replace('(', '', $id);
            $id = str_replace(')', '', $id);
            $ret['to_display_list'][] = array('libelle' => $tr_libelle, 'type' => 'tr', 'id' => $id);
            foreach ($pre_fields as $pre_field) {
                $libelle = $pre_field . '_' . $post_libelle;
                $id = str_replace(' ', '_', $libelle);
                $id = str_replace('(', '', $id);
                $id = str_replace(')', '', $id);
                $ret['list'][] = $id;
                $ret['to_display_list'][] = array('libelle' => $libelle, 'id' => $id, 'type' => 'td', 'infos' => $field, 'placeholder' => self::getFinancialDataPlaceholder($pre_field, $post_libelle));
                $ret['details'][$libelle] = $field;
            }
        }
        return $ret;
    }

    /**
     * Methode reprise sur drupal
     *
     * @param $pre_libelle
     * @param $post_libelle
     * @return string
     */
    public static function getFinancialDataPlaceholder($pre_libelle, $post_libelle)
    {
        $post_libelle = str_replace(' (final)', '', $post_libelle);
        $post_libelle = str_replace(' (initial)', '', $post_libelle);
        if ($pre_libelle == 'volume') {
            return 'V ' . $post_libelle;
        } elseif ($pre_libelle == 'net_sales') {
            return 'NS ' . $post_libelle;
        } elseif ($pre_libelle == 'contributing_margin') {
            return 'CM ' . $post_libelle;
        } elseif ($pre_libelle == 'central_investment') {
            return 'CI ' . $post_libelle;
        } elseif ($pre_libelle == 'advertising_promotion') {
            return 'AP ' . $post_libelle;
        } elseif ($pre_libelle == 'caap') {
            return 'CAAP ' . $post_libelle;
        } else {
            return $pre_libelle . ' ' . $post_libelle;
        }
    }

    /**
     * Methode reprise sur drupal
     *
     * @param $texte
     * @param int $nbreCar
     * @return bool|string
     */
    public static function textResume($texte, $nbreCar = 200)
    {
        $texte = trim(strip_tags($texte));
        if (is_numeric($nbreCar)) {
            $PointSuspension = '...';
            $texte .= ' ';
            $LongueurAvant = strlen($texte);
            if ($LongueurAvant > $nbreCar) {
                $texte = substr($texte, 0, strpos($texte, ' ', $nbreCar));
                if ($PointSuspension != '') {
                    $texte .= $PointSuspension;
                }
            }
            // ---------------------
        }
        return $texte;
    }


    /**
     * Get base64 picture data from url.
     *
     * @param $url
     * @return null|string
     */
    public static function getBase64PictureDataFromUrl($url)
    {
        try {
            return "data:image/jpeg;base64," . base64_encode(file_get_contents($url));
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Methode reprise sur drupal
     *
     * @param $elements
     * @param null $el_date
     * @param null $innovation
     * @return mixed
     */
    public static function addInnovationQualiPage1($elements, $el_date = null, $innovation = null)
    {
        $elements['nb_slide']++;
        $elements['current_slide'] = $elements['ppt']->createSlide();
        // Ajout du background
        $shape = $elements['current_slide']->createDrawingShape();
        $shape->setName('')
            ->setDescription('')
            ->setPath(__DIR__ . '/../../web/ppt/couvs/couv-quali-1.png')
            ->setHeight(1079)
            ->setOffsetX(0)
            ->setOffsetY(0);
        $shape->getShadow()->setVisible(false)
            ->setDirection(0)
            ->setDistance(0);
        
        /* LEFT COLUMN
          ---------------------------------------------------------------------------------------- */
        $elements = self::generateLeftColumnQualiPage1($elements, $el_date, $innovation);


        /* RIGHT COLUMN
          ---------------------------------------------------------------------------------------- */

        // Innovation picture
        if ($innovation['quali']['left_column']['beautyshot_quali_bg'] && $innovation['quali']['left_column']['beautyshot_quali_bg'] !== "") {
            $imageData = self::getBase64PictureDataFromUrl($innovation['quali']['left_column']['beautyshot_quali_bg']);
            if ($imageData) {
                $shape = new Base64();
                $shape->setName('')
                    ->setDescription('')
                    ->setResizeProportional(false)
                    ->setData($imageData)
                    ->setWidth(1395)
                    ->setHeight(1050)
                    ->setOffsetX(523)
                    ->setOffsetY(0);
                $elements['current_slide']->addShape($shape);
            }
        }


        // Pernod Ricard Logo
        $shape = $elements['current_slide']->createDrawingShape();
        $shape->setName('')
            ->setDescription('')
            ->setPath(__DIR__ . '/../../web/ppt/icons/logo-small.png')
            ->setHeight(97)
            ->setOffsetX(1684)
            ->setOffsetY(30);
        $shape->setResizeProportional(true);


        $why_invest_offset_y = 840;
        $offset_x = 914;
        $offset_padding_x = 885;
        if (count($innovation['quali']['left_column']['links']) > 0) {
            $why_invest_offset_y = 780;
        }

        $shape = $elements['current_slide']->createDrawingShape();
        $shape->setName('')
            ->setDescription('')
            ->setPath(__DIR__ . '/../../web/ppt/couvs/block/grey_why.png')
            ->setWidthAndHeight(1031, 190)
            ->setOffsetX($offset_padding_x)
            ->setOffsetY($why_invest_offset_y);
        $shape->setResizeProportional(false);

        // title Why did we design this innovation?
        $offset_y = $why_invest_offset_y + 15;
        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(40)
            ->setWidth(980)
            ->setOffsetX($offset_x)
            ->setOffsetY($offset_y);
        $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_AUTO);
        $textRun = $shape->createTextRun("Why did we design this innovation?");
        $textRun->getFont()
            ->setSize(16)
            ->setName(UtilsPpt::FONT_MONTSERRAT)
            ->setBold(true)
            ->setColor(new Color(UtilsPpt::COLOR_BLACK));


        // content 1
        $offset_y = $why_invest_offset_y + 60;
        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(80)
            ->setWidth(980)
            ->setOffsetX($offset_x)
            ->setOffsetY($offset_y);
        $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_AUTO);
        if ($innovation['quali']['left_column']['why_invest_in_this_innovation'] !== "") {
            $textRun = $shape->createTextRun($innovation['quali']['left_column']['why_invest_in_this_innovation']);
            $textRun->getFont()
                ->setSize(14)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color(UtilsPpt::COLOR_BLACK));
        } else {
            $shape->getBorder()->setLineStyle(Border::LINE_SINGLE)->setDashStyle(Border::DASH_SOLID)->setColor(new Color(UtilsPpt::COLOR_RED_MISSING_DATA));
            $textRun = $shape->createTextRun("Missing data");
            $textRun->getFont()
                ->setSize(14)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color(UtilsPpt::COLOR_RED_MISSING_DATA));
        }

        if (count($innovation['quali']['left_column']['links']) > 0) {
            $links_offset_y = $why_invest_offset_y + 170;
            $shape = $elements['current_slide']->createDrawingShape();
            $shape->setName('')
                ->setDescription('')
                ->setPath(__DIR__ . '/../../web/ppt/couvs/block/grey_links.png')
                ->setWidthAndHeight(1031, 45)
                ->setOffsetX($offset_padding_x)
                ->setOffsetY($links_offset_y);
            $shape->setResizeProportional(false);

            // Links
            $shape = $elements['current_slide']->createRichTextShape()
                ->setHeight(45)
                ->setWidth(980)
                ->setOffsetX($offset_x)
                ->setOffsetY($links_offset_y);
            $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_AUTO);
            $nb_links = 1;
            foreach ($innovation['quali']['left_column']['links'] as $link) {
                $textRun = $shape->createTextRun($link['libelle']);
                $textRun->getFont()
                    ->setSize(14)
                    ->setName(UtilsPpt::FONT_WORK_SANS)
                    ->setColor(new Color(UtilsPpt::COLOR_BLACK));
                $textRun->getHyperlink()->setUrl($link['link']);
                if ($nb_links < count($innovation['quali']['left_column']['links'])) {
                    $textRun = $shape->createTextRun(", ");
                    $textRun->getFont()
                        ->setSize(14)
                        ->setName(UtilsPpt::FONT_WORK_SANS)
                        ->setColor(new Color(UtilsPpt::COLOR_BLACK));
                }
                $nb_links++;
            }
        }


        /* END
          ---------------------------------------------------------------------------------------- */

        return $elements;
    }

    /**
     * Methode reprise sur drupal
     *
     * @param $entity
     * @param $settings
     * @param bool $with_quanti
     * @param null $the_date
     * @param bool $force_quanti
     * @return array
     */
    public static function getProperInnovationForQualiQuanti($entity, $settings, $with_quanti = true, $the_date = null, $force_quanti = false)
    {
        $web_dir = ''; //__DIR__.'/../../web/';
        $ret = array(
            'title' => '',
            'quali' => array(),
            'quanti' => array(),
            'current_stage' => '',
        );
        $innovation = $entity;
        $innovation_is_a_service = Innovation::innovationArrayIsAService($innovation);
        $ret['title'] = $innovation['title'];

        /* QUALI
    ----------------------------------------------------------------------------------------*/
        $ret['current_stage'] = $innovation['current_stage'];
        $ret['is_frozen'] = $innovation['is_frozen'];
        $ret['is_a_service'] = $innovation_is_a_service;


        $ret['quali']['is_valid'] = true;

        $ret["classification_type"] = ($innovation['classification_type']) ? $innovation['classification_type'] : '';

        // LEFT COLUMN
        $ret['quali']['left_column'] = array();

        $ret['quali']['left_column']['entity_Brand'] = self::get_innovation_entity_brand_libelle($innovation);
        $ret['quali']['left_column']['innovation_title'] = strtoupper($innovation['title']);
        $in_market_date_libelle = ($innovation['in_market_date'] != null) ? gmdate("m/Y", $innovation['in_market_date']) : "";
        $market_introduction = (in_array($innovation['current_stage'], array('discover', 'ideate'))) ? 'Early stage' : "Market introduction: " . $in_market_date_libelle;
        $ret['quali']['left_column']['market_introduction'] = $market_introduction;
        $ret['quali']['left_column']['stage_icon'] = self::getExportIconByType("stage", $innovation);
        $ret['quali']['left_column']['classification_icon'] = self::getExportIconByType("classification", $innovation);
        $ret['quali']['left_column']['type_icon'] = self::getExportIconByType("type", $innovation);
        $ret['quali']['left_column']['lines'] = array();
        if(!$innovation_is_a_service) {
            if ($innovation['growth_model'] == 'fast_growth') {
                $line = array(
                    'icon' => 'icons/growth_model/icon-fast_growth-light.png',
                    'libelle' => 'Fast Growth = 2-4 years'
                );
            } else {
                $line = array(
                    'icon' => 'icons/growth_model/icon-slow_build-light.png',
                    'libelle' => 'Slow Build = 4-8 years'
                );
            }
            $ret['quali']['left_column']['lines'][] = $line;
        }
        if ($innovation['growth_strategy'] == "Big Bet") {
            $ret['quali']['left_column']['lines'][] = self::getExportIconByType("growth_strategy", $innovation);
        }
        if (!$innovation_is_a_service && $innovation['replace_existing_product'] == '1') {
            $replace = ($innovation['existing_product']) ? $innovation['existing_product'] : 'Empty';
            $ret['quali']['left_column']['lines'][] = array(
                'icon' => 'icons/icon-replace.png',
                'libelle' => "Replaces: " . $replace
            );
        }
        if (!$innovation_is_a_service && !in_array($innovation['current_stage'], array('discover', 'ideate', 'experiment'))) {
            $ret['quali']['left_column']['lines'][] = array(
                'icon' => 'icons/icon-market.png',
                'libelle' => "Available in: " . self::displayNbMarkets($innovation['markets_in_array'])
            );
        }

        if (!$innovation_is_a_service && !in_array($innovation['current_stage'], array('discover', 'ideate'))) {
            $latest_volume_value = $innovation['financial']['data']['latest']['calc']['volume'];
            $latest_volume = ($latest_volume_value) ? $latest_volume_value . " " . "k9Lcs" : "0 " . "k9Lcs";
            $ret['quali']['left_column']['lines'][] = array(
                'icon' => 'icons/icon-volume.png',
                'libelle' => "Volumes: " . $latest_volume
            );
        }
        if (!$innovation_is_a_service && $innovation['moc'] && $innovation['moc'] != 'Empty') {
            $ret['quali']['left_column']['lines'][] = array(
                'icon' => 'icons/icon-moc.png',
                'libelle' => $innovation['moc']
            );
        }
        if (!$innovation_is_a_service && $innovation['business_drivers'] && $innovation['business_drivers'] != 'None') {
            $ret['quali']['left_column']['lines'][] = array(
                'icon' => self::getExportIconByType('business_drivers', $innovation),
                'libelle' => $innovation['business_drivers']
            );
        }

        if (!$innovation_is_a_service && !in_array($innovation['current_stage'], array('discover', 'ideate')) && $innovation['abv']) {
            $ret['quali']['left_column']['lines'][] = array(
                'icon' => 'icons/icon-abv.png',
                'libelle' => $innovation['abv']
            );
        }

        $ret['quali']['left_column']['lines_page_1'] = array();

        if (!$innovation_is_a_service && self::getExportIconByType('consumer_opportunity', $innovation)) {
            $ret['quali']['left_column']['lines_page_1'][] = array(
                'icon' => self::getExportIconByType('consumer_opportunity', $innovation),
                'libelle' => $innovation['consumer_opportunity_title']
            );
        }

        $ret['quali']['left_column']['picture'] = ($innovation['ppt_picture_quali']) ? $web_dir . $innovation['ppt_picture_quali'] : null;

        $ret['quali']['left_column']['beautyshot_quali_bg'] = ($innovation['ppt_beautyshot_quali_bg']) ? $web_dir . $innovation['ppt_beautyshot_quali_bg'] : null;

        $ret['quali']['left_column']['consumer_insight'] = ($innovation['consumer_insight']) ? $innovation['consumer_insight'] : '';
        $ret['quali']['left_column']['story'] = ($innovation['story']) ? $innovation['story'] : '';
        $ret['quali']['left_column']['unique_experience'] = ($innovation['unique_experience']) ? $innovation['unique_experience'] : '';
        $ret['quali']['left_column']['why_invest_in_this_innovation'] = ($innovation['why_invest_in_this_innovation']) ? $innovation['why_invest_in_this_innovation'] : '';

        $ret['quali']['left_column']['links'] = array();
        if ($innovation['website_url'] && $innovation['website_url'] != "") {
            $ret['quali']['left_column']['links'][] = array(
                'link' => $innovation['website_url'],
                'libelle' => ($innovation_is_a_service) ? 'Website' : 'Page My Brands'
            );
        }
        if ($innovation['ibp_link'] && $innovation['ibp_link'] != "") {
            $ret['quali']['left_column']['links'][] = array(
                'link' => $innovation['ibp_link'],
                'libelle' => 'IBP content'
            );
        }
        if ($innovation['video_link'] && $innovation['video_link'] != "") {
            $ret['quali']['left_column']['links'][] = array(
                'link' => $innovation['video_link'],
                'libelle' => 'Video URL'
            );
        }

        // HEADER
        $ret['quali']['header'] = array();
        $ret['quali']['header']['stage_market_pith'] = self::getLibelleStageByFakeLibelle($innovation['current_stage']) . " Elevator pitch";
        $ret['quali']['header']['contact']['picture'] = null;
        $ret['quali']['header']['contact']['name'] = "Person to contact for more information";
        $ret['quali']['header']['contact']['email'] = ($innovation['contact']) ? $innovation['contact']['email'] : "";

        // VALUE PROPOSITION
        $ret['quali']['value_proposition'] = array();
        $ret['quali']['value_proposition']['value_proposition'] = $innovation['value_proposition'];

        // CONSUMER
        $ret['quali']['consumer'] = array();
        $ret['quali']['consumer']['early_adopter_persona'] = $innovation['early_adopter_persona'];
        $ret['quali']['consumer']['consumer_benefit'] = $innovation['consumer_insight'];

        // BUSINESS
        $ret['quali']['business'] = array();
        $ret['quali']['business']['source_of_business'] = $innovation['source_of_business'];
        #$ret['quali']['business']['portfolio_intent'] = $innovation['portfolio_intent'];
        $ret['quali']['business']['portfolio_intent'] = 'portfolio_intent';

        //$ret['quali']['open_questions_frictions'] = ($innovation['current_stage'] == 'incubation') ? $innovation['incubate_open_questions_frictions'] : $innovation['experiment_open_questions_frictions'];

        // FOOTER
        $ret['quali']['footer'] = array();
        switch ($innovation['current_stage']) {
            case "discover":
            case "ideate":
                $ret['quali']['footer']['block_1']['color'] = "FAB700";
                $ret['quali']['footer']['block_1']['title'] = "Key learnings so far";
                $ret['quali']['footer']['block_1']['text'] = $innovation['universal_key_learning_so_far'];
                $ret['quali']['footer']['next_steps'] = $innovation['universal_next_steps'];
                break;
            case "experiment":
                $ret['quali']['footer']['block_1']['color'] = UtilsPpt::COLOR_45ab34;
                $ret['quali']['footer']['block_1']['title'] = "Key learnings so far";
                $ret['quali']['footer']['block_1']['text'] = $innovation['universal_key_learning_so_far'];
                $ret['quali']['footer']['next_steps'] = $innovation['universal_next_steps'];
                break;
            case "incubation":
                $ret['quali']['footer']['block_1']['color'] = "e8485c";
                $ret['quali']['footer']['block_1']['title'] = "Key learnings so far";
                $ret['quali']['footer']['block_1']['text'] = $innovation['universal_key_learning_so_far'];
                $ret['quali']['footer']['next_steps'] = $innovation['universal_next_steps'];
                break;
            case "scaling-up":
            case "Permanent range":
            case "discontinued":
            default:
                $ret['quali']['footer']['block_1']['color'] = UtilsPpt::COLOR_80a7d0;
                $ret['quali']['footer']['block_1']['title'] = "Key learnings so far";
                $ret['quali']['footer']['block_1']['text'] = $innovation['universal_key_learning_so_far'];
                $ret['quali']['footer']['next_steps'] = $innovation['universal_next_steps'];
                break;
        }

        $ret['quali']['key_information'] = "";
        $bull = '•';
        if ($innovation['universal_key_information_1']) {
            $ret['quali']['key_information'] .= $bull . ' Perfect Serve: ' . $innovation['universal_key_information_1'] . PHP_EOL;
        }
        if ($innovation['universal_key_information_2']) {
            $ret['quali']['key_information'] .= $bull . ' RTM: ' . $innovation['universal_key_information_2'] . PHP_EOL;
        }

        $text_universal_information_4 = $innovation['universal_key_information_4'];
        $text_universal_information_3 = $innovation['universal_key_information_3'];

        if ($innovation['universal_key_information_3'] && $innovation['universal_key_information_3_vs']) {
            $text_universal_information_3 = ' vs ' . innovation['universal_key_information_3_vs'];
        } else if (!$innovation['universal_key_information_3'] && $innovation['universal_key_information_3_vs']) {
            $text_universal_information_3 = innovation['universal_key_information_3_vs'];
        }

        if ($innovation['universal_key_information_4'] && $innovation['universal_key_information_4_vs']) {
            $text_universal_information_4 = ' vs ' . $innovation['universal_key_information_4_vs'];
        } else if (!$innovation['universal_key_information_4'] && $innovation['universal_key_information_4_vs']) {
            $text_universal_information_4 = $innovation['universal_key_information_4_vs'];
        }


        if ($text_universal_information_4) {
            $ret['quali']['key_information'] .= $bull . ' Price index versus main competitor: ' . $text_universal_information_4 . PHP_EOL;
        }

        if (!$innovation['new_to_the_world'] && $text_universal_information_3) {
            $ret['quali']['key_information'] .= $bull . ' Price index vs mother brand or main competitor: ' . $text_universal_information_3 . PHP_EOL;
        }

        if ($innovation['universal_key_information_5']) {
            $ret['quali']['key_information'] .= $bull . ' Other: ' . $innovation['universal_key_information_5'] . PHP_EOL;
        }

        $ret['quali']['proofs_of_traction_picture_1'] = ($innovation['ppt_pot_picture_1']) ? $web_dir . $innovation['ppt_pot_picture_1'] : null;
        $ret['quali']['proofs_of_traction_legend_1'] = $innovation['proofs_of_traction_picture_1_legend'];
        $ret['quali']['proofs_of_traction_picture_2'] = ($innovation['ppt_pot_picture_2']) ? $web_dir . $innovation['ppt_pot_picture_2'] : null;
        $ret['quali']['proofs_of_traction_legend_2'] = $innovation['proofs_of_traction_picture_2_legend'];;

        if ($with_quanti) {
            /* QUANTI
        ----------------------------------------------------------------------------------------*/

            $ret['quanti']['is_valid'] = (!$innovation['financial']['is_incomplete'] && !in_array($innovation['current_stage'], array('discover', 'ideate', 'experiment')));
            if ($force_quanti) {
                $ret['quanti']['is_valid'] = (!in_array($innovation['current_stage'], array('discover', 'ideate', 'experiment')));
            }

            // LEFT COLUMN
            $ret['quanti']['left_column'] = $ret['quali']['left_column'];
            $ret['quanti']['left_column']['lines'] = array();
            if(!$innovation_is_a_service) {
                if ($innovation['growth_model'] == 'fast_growth') {
                    $line = array(
                        'icon' => 'icons/growth_model/icon-fast_growth-light.png',
                        'libelle' => 'Fast Growth = 2-4 years'
                    );
                } else {
                    $line = array(
                        'icon' => 'icons/growth_model/icon-slow_build-light.png',
                        'libelle' => 'Slow Build = 4-8 years'
                    );
                }
                $ret['quanti']['left_column']['lines'][] = $line;
            }
            if (in_array($innovation['growth_strategy'], array("Big Bet", "Top contributor"))) {
                $ret['quanti']['left_column']['lines'][] = self::getExportIconByType("growth_strategy", $innovation);
            }

            if (!in_array($innovation['current_stage'], array('discover', 'ideate', 'experiment'))) {
                $ret['quanti']['left_column']['lines'][] = array(
                    'icon' => 'icons/icon-market.png',
                    'libelle' => "Available in: " . self::displayNbMarkets($innovation['markets_in_array'])
                );
            }
            $ret['quanti']['left_column']['picture'] = ($innovation['performance_picture']) ? $web_dir . $innovation['performance_picture'] : null;

            $market_introduction = (in_array($innovation['current_stage'], array('discover', 'ideate'))) ? 'Early stage' : "In market since " . $innovation['years_since_launch'] . " years";
            $ret['quanti']['left_column']['market_introduction'] = $market_introduction;

            // HEADER
            $ret['quanti']['header'] = array();
            $ret['quanti']['header']['stage_trimester'] = self::getLibelleStageByFakeLibelle($innovation['current_stage']) . " " . $settings->getFinancialDateLibelle() . " performance";

            // CHART
            $ret['quanti']['chart'] = array();
            $ret['quanti']['chart']['picture'] = ($innovation['financial_graph_picture']) ? $innovation['financial_graph_picture'] : null;

            // COMMENT
            $ret['quanti']['comment'] = array();
            $financial_date = $settings->getCurrentFinancialDate();
            $ret['quanti']['comment']['value'] = ($innovation['performance_review']) ? $innovation['performance_review'] : "";


            // FINANCIAL STATS LEFT
            $ret['quanti']['financial_stats_left'] = array();
            $ret['quanti']['financial_stats_left']['column_1'] = array();
            $ret['quanti']['financial_stats_left']['column_2'] = array();

            $column_1 = array();
            $column_2 = array();

            $new_blue_color = "FF36609B";
            $positive_green_color = "FF45ab34";
            $negative_red_color = "FFe8485c";

            $net_sales_percent = Settings::getPercentDiffBetweenTwoValues(
                $innovation['financial']['data']['latest_a']['calc']['net_sales'],
                $innovation['financial']['data']['latest']['calc']['net_sales']
            );
            $net_sales_class = FinancialData::getFinancialDataClass($net_sales_percent);
            $icon = ($net_sales_class == 'positive') ? self::ICON_PERCENT_POSITIVE : self::ICON_PERCENT_NEGATIVE;
            $color = ($net_sales_class == 'positive') ? $positive_green_color : $negative_red_color;
            $the_value_percent = ($net_sales_percent != -100) ? $net_sales_percent . "%" : "";
            if ($net_sales_class == 'new' || $net_sales_percent == 'N/A') {
                $icon = self::ICON_PERCENT_POSITIVE;
                $color = $positive_green_color;
            }
            if($the_value_percent == ""){
                $icon = "";
            }
            $the_value_ke =  self::reformatNumber($innovation['financial']['data']['latest']['calc']['net_sales']) . " k€";
            $column_1[] = array(
                'title' => "Net Sales",
                'value' => $the_value_ke,
                'percent_icon' => $icon,
                'percent_color' => $color,
                'percent_value' => $the_value_percent
            );


            $total_ap_percent = Settings::getPercentDiffBetweenTwoValues(
                $innovation['financial']['data']['latest_a']['calc']['total_ap'],
                $innovation['financial']['data']['latest']['calc']['total_ap']
            );
            $total_ap_class = FinancialData::getFinancialDataClass($total_ap_percent);

            $icon = ($total_ap_class == 'positive') ? self::ICON_PERCENT_POSITIVE : self::ICON_PERCENT_NEGATIVE;
            $color = ($total_ap_class == 'positive') ? $positive_green_color : $negative_red_color;
            $the_value_percent = ($total_ap_percent != -100) ? $total_ap_percent . "%" : "";
            if($the_value_percent == ""){
                $icon = "";
            }
            if ($total_ap_class == 'new' || $total_ap_percent == 'N/A') {
                $icon = "";
                $color = $new_blue_color;
                $the_value_percent = $total_ap_percent;
            }
            $the_value_ke = self::reformatNumber($innovation['financial']['data']['latest']['calc']['total_ap']) . " k€";
            $column_1[] = array(
                'title' => "Total A&P",
                'value' => $the_value_ke,
                'percent_icon' => $icon,
                'percent_color' => $color,
                'percent_value' => $the_value_percent
            );
            $ret['quanti']['financial_stats_left']['column_1'] = $column_1;
            $volumes_percent = Settings::getPercentDiffBetweenTwoValues(
                $innovation['financial']['data']['latest_a']['calc']['volume'],
                $innovation['financial']['data']['latest']['calc']['volume']
            );
            $volumes_class = FinancialData::getFinancialDataClass($volumes_percent);
            $icon = ($volumes_class == 'positive') ? self::ICON_PERCENT_POSITIVE : self::ICON_PERCENT_NEGATIVE;
            $color = ($volumes_class == 'positive') ? $positive_green_color : $negative_red_color;
            $the_value_percent = ($volumes_percent != -100) ? $volumes_percent . "%" : "";
            if($the_value_percent == ""){
                $icon = "";
            }
            $the_value_ke =  self::reformatNumber($innovation['financial']['data']['latest']['calc']['volume']) . " k9Lcs";
            $column_2[] = array(
                'title' => "Volumes",
                'value' => $the_value_ke,
                'percent_icon' => $icon,
                'percent_color' => $color,
                'percent_value' => $the_value_percent
            );

            if (!in_array($innovation['current_stage'], array('discover', 'ideate', 'experiment'))) {
                $caap_percent = Settings::getPercentDiffBetweenTwoValues(
                    $innovation['financial']['data']['latest_a']['calc']['caap'],
                    $innovation['financial']['data']['latest']['calc']['caap']
                );
                $caap_class = FinancialData::getFinancialDataClass($caap_percent);
                $icon = ($caap_class == 'positive') ? self::ICON_PERCENT_POSITIVE : self::ICON_PERCENT_NEGATIVE;
                $color = ($caap_class == 'positive') ? $positive_green_color : $negative_red_color;
                $the_value_percent = ($caap_percent != -100) ? $caap_percent . "%" : '';
                if($the_value_percent == ""){
                    $icon = "";
                }
                $the_value_ke = self::reformatNumber($innovation['financial']['data']['latest']['calc']['caap']) . " k€";
                $column_2[] = array(
                    'title' => "CAAP",
                    'value' => $the_value_ke,
                    'percent_icon' => $icon,
                    'percent_color' => $color,
                    'percent_value' => $the_value_percent
                );
            }
            $ret['quanti']['financial_stats_left']['column_2'] = $column_2;



            // KEY INDICATORS
            $ret['quanti']['financial_stats_center'] = array();
            $ret['quanti']['financial_stats_center']['column_1'] = array();
            $ret['quanti']['financial_stats_center']['column_2'] = array();

            $column_1 = array();
            $column_2 = array();

            $level_of_investment_value = FinancialData::calculateLevelOfInvestment(
                $innovation['financial']['data']['latest']['calc']['total_ap'],
                $innovation['financial']['data']['latest']['calc']['net_sales']
            );

            $level_of_investment_old = FinancialData::calculateLevelOfInvestment(
                $innovation['financial']['data']['latest_a']['calc']['total_ap'],
                $innovation['financial']['data']['latest_a']['calc']['net_sales']
            );
            $pts_level_of_investment = ($level_of_investment_old != "NEW" && $level_of_investment_value != 'NEW') ? round($level_of_investment_value - $level_of_investment_old) : 0;
            $level_of_investment_class = FinancialData::getFinancialDataClass($pts_level_of_investment);
            $icon = ($level_of_investment_class == 'positive') ? self::ICON_PERCENT_POSITIVE : self::ICON_PERCENT_NEGATIVE;
            $color = ($level_of_investment_class == 'positive') ? $positive_green_color : $negative_red_color;
            $the_value_percent = $pts_level_of_investment . " pts";
            $the_value =  self::reformatNumber($level_of_investment_value) . "%";
            $column_1[] = array(
                'title' => "A&P/Net Sales ratio",
                'value' => $the_value,
                'percent_icon' => $icon,
                'percent_color' => $color,
                'percent_value' => $the_value_percent
            );


            $level_of_profitability_value = FinancialData::calculateLevelOfProfitability(
                $innovation['financial']['data']['latest']['calc']['contributing_margin'],
                $innovation['financial']['data']['latest']['calc']['net_sales']
            );

            $level_of_profitability_old = FinancialData::calculateLevelOfProfitability(
                $innovation['financial']['data']['latest_a']['calc']['contributing_margin'],
                $innovation['financial']['data']['latest_a']['calc']['net_sales']
            );
            $pts_level_of_profitability = round($level_of_profitability_value - $level_of_profitability_old);
            $level_of_profitability_class = FinancialData::getFinancialDataClass($pts_level_of_profitability);
            $icon = ($level_of_profitability_class == 'positive') ? self::ICON_PERCENT_POSITIVE : self::ICON_PERCENT_NEGATIVE;
            $color = ($level_of_profitability_class == 'positive') ? $positive_green_color : $negative_red_color;
            $the_value_percent = $pts_level_of_profitability . " pts";
            $the_value =  self::reformatNumber($level_of_profitability_value) . "%";
            $column_1[] = array(
                'title' => "CM/Net Sales ratio",
                'value' => $the_value,
                'percent_icon' => $icon,
                'percent_color' => $color,
                'percent_value' => $the_value_percent
            );

            $ret['quanti']['financial_stats_center']['column_1'] = $column_1;


            // CM PER CASE
            $cm_per_case_value = FinancialData::calculateCmPerCase(
                $innovation['financial']['data']['latest']['calc']['volume'],
                $innovation['financial']['data']['latest']['calc']['contributing_margin']
            );
            $cm_per_case_value_old = FinancialData::calculateCmPerCase(
                $innovation['financial']['data']['latest_a']['calc']['volume'],
                $innovation['financial']['data']['latest_a']['calc']['contributing_margin']
            );
            $cm_per_case_percent = Settings::getPercentDiffBetweenTwoValues(
                $cm_per_case_value_old,
                $cm_per_case_value
            );
            $cm_per_case_class = FinancialData::getFinancialDataClass($cm_per_case_percent);
            $icon = ($cm_per_case_class == 'positive') ? self::ICON_PERCENT_POSITIVE : self::ICON_PERCENT_NEGATIVE;
            $color = ($cm_per_case_class == 'positive') ? $positive_green_color : $negative_red_color;
            $the_value_percent = ($cm_per_case_percent != -100) ? $cm_per_case_percent . "%" : "";
            if($the_value_percent == ""){
                $icon = "";
            }
            $column_2[] = array(
                'title' => "CM per case",
                'value' => self::reformatNumber($cm_per_case_value) . "€",
                'percent_icon' => $icon,
                'percent_color' => $color,
                'percent_value' => $the_value_percent
            );


            // COGS PER CASE
            $innovation_is_a_service = Innovation::innovationArrayIsAService($innovation);
            if (!$innovation_is_a_service && !in_array($innovation['current_stage'], array('discover', 'ideate', 'experiment'))) {
                $cogs_per_case_value = FinancialData::calculateCmPerCase(
                    $innovation['financial']['data']['latest']['calc']['volume'],
                    $innovation['financial']['data']['latest']['calc']['cogs']
                );
                $cogs_per_case_value_old = FinancialData::calculateCmPerCase(
                    $innovation['financial']['data']['latest_a']['calc']['volume'],
                    $innovation['financial']['data']['latest_a']['calc']['cogs']
                );
                $cogs_per_case_percent = Settings::getPercentDiffBetweenTwoValues(
                    $cogs_per_case_value_old,
                    $cogs_per_case_value
                );
                $cogs_per_case_class = FinancialData::getFinancialDataClass($cogs_per_case_percent);
                $icon = ($cogs_per_case_class == 'positive') ? self::ICON_PERCENT_POSITIVE : self::ICON_PERCENT_NEGATIVE;
                $color = ($cogs_per_case_class == 'positive') ? $positive_green_color : $negative_red_color;
                $the_value_percent = ($cogs_per_case_percent != -100) ? $cogs_per_case_percent . "%" : "";
                if ($the_value_percent == "") {
                    $icon = "";
                }
                $column_2[] = array(
                    'title' => "COGS per case",
                    'value' => self::reformatNumber($cogs_per_case_value) . "€",
                    'percent_icon' => $icon,
                    'percent_color' => $color,
                    'percent_value' => $the_value_percent
                );
            }


            $ret['quanti']['financial_stats_center']['column_2'] = $column_2;

            $ret['quanti']['financial_stats_right'] = array();
            $ret['quanti']['financial_stats_right']['column_1'] = array();

            $column_1 = array();

            $cumul_total_ap =  $innovation['financial']['data']['cumul']['total_ap'];
            $cumul_total_ap_old =  $cumul_total_ap - $innovation['financial']['data']['latest']['calc']['total_ap'];

            $cumul_total_ap_percent = Settings::getPercentDiffBetweenTwoValues(
                $cumul_total_ap_old,
                $cumul_total_ap
            );
            $cumul_total_ap_class = FinancialData::getFinancialDataClass($cumul_total_ap_percent);

            $icon = ($cumul_total_ap_class == 'positive') ? self::ICON_PERCENT_POSITIVE : self::ICON_PERCENT_NEGATIVE;
            $color = ($cumul_total_ap_class == 'positive') ? $positive_green_color : $negative_red_color;
            $the_value_percent = ($cumul_total_ap_percent != -100) ? $cumul_total_ap_percent . "%" : "";
            if($the_value_percent == ""){
                $icon = "";
            }
            if ($cumul_total_ap_class == 'new' || $cumul_total_ap_percent == 'N/A') {
                $icon = "";
                $color = $new_blue_color;
                $the_value_percent = $cumul_total_ap_percent;
            }
            $the_value_ke = self::reformatNumber($cumul_total_ap) . " k€";
            $column_1[] = array(
                'title' => "Cumul Total A&P",
                'value' => $the_value_ke,
                'percent_icon' => $icon,
                'percent_color' => $color,
                'percent_value' => $the_value_percent
            );

            if (!in_array($innovation['current_stage'], array('discover', 'ideate', 'experiment'))) {
                $cumul_caap =  $innovation['financial']['data']['cumul']['caap'];
                $cumul_caap_old =  $cumul_caap - $innovation['financial']['data']['latest']['calc']['caap'];

                $cumul_caap_percent = Settings::getPercentDiffBetweenTwoValues(
                    $cumul_caap_old,
                    $cumul_caap
                );
                $cumul_caap_class = FinancialData::getFinancialDataClass($cumul_caap_percent);
                $icon = ($cumul_caap_class == 'positive') ? self::ICON_PERCENT_POSITIVE : self::ICON_PERCENT_NEGATIVE;
                $color = ($cumul_caap_class == 'positive') ? $positive_green_color : $negative_red_color;
                $the_value_percent = ($cumul_caap_percent != -100) ? $cumul_caap_percent . "%" : '';
                if($the_value_percent == ""){
                    $icon = "";
                }
                $the_value_ke = self::reformatNumber($cumul_caap) . " k€";
                $column_1[] = array(
                    'title' => "Cumul CAAP",
                    'value' => $the_value_ke,
                    'percent_icon' => $icon,
                    'percent_color' => $color,
                    'percent_value' => $the_value_percent
                );
            }

            $ret['quanti']['financial_stats_right']['column_1'] = $column_1;


        }
        return $ret;
    }


    /**
     * addInnovationVeryEarlyQualiPage
     *
     * @param $elements
     * @param null $el_date
     * @param null $innovation
     * @return mixed
     *
     */
    public static function addInnovationVeryEarlyQualiPage($elements, $el_date = null, $innovation = null)
    {
        $elements['nb_slide']++;
        $elements['current_slide'] = $elements['ppt']->createSlide();
        // Ajout du background
        $shape = $elements['current_slide']->createDrawingShape();
        $shape->setName('')
            ->setDescription('')
            ->setPath(__DIR__ . '/../../web/ppt/couvs/couv-quali-2-very-early.png')
            ->setHeight(1079)
            ->setOffsetX(0)
            ->setOffsetY(0);
        $shape->getShadow()->setVisible(false)
            ->setDirection(0)
            ->setDistance(0);

        /* LEFT COLUMN
          ---------------------------------------------------------------------------------------- */

        $elements = self::generateLeftColumnQuali($elements, $el_date, $innovation);


        /* RIGHT COLUMN
          ---------------------------------------------------------------------------------------- */

        $elements = self::generateRightColumnHeaderQuali($elements, $el_date, $innovation);


        $elements = self::generateRightColumnQualiVeryEarly($elements, $el_date, $innovation);

        /* END
          ---------------------------------------------------------------------------------------- */

        return $elements;
    }

    /**
     * addInnovationQualiPage
     *
     * @param $elements
     * @param null $el_date
     * @param null $innovation
     * @return mixed
     *
     */
    public static function addInnovationQualiPage($elements, $el_date = null, $innovation = null)
    {
        $elements['nb_slide']++;
        $elements['current_slide'] = $elements['ppt']->createSlide();
        // Ajout du background
        $shape = $elements['current_slide']->createDrawingShape();
        $shape->setName('')
            ->setDescription('')
            ->setPath(__DIR__ . '/../../web/ppt/couvs/couv-quali-2.png')
            ->setHeight(1079)
            ->setOffsetX(0)
            ->setOffsetY(0);
        $shape->getShadow()->setVisible(false)
            ->setDirection(0)
            ->setDistance(0);

        /* LEFT COLUMN
          ---------------------------------------------------------------------------------------- */

        $elements = self::generateLeftColumnQuali($elements, $el_date, $innovation);


        /* RIGHT COLUMN
          ---------------------------------------------------------------------------------------- */

        $elements = self::generateRightColumnHeaderQuali($elements, $el_date, $innovation);

        // Column Value proposition
        $elements = self::generateRightColumnValuePropositionQuali($elements, $el_date, $innovation);

        // Column Consumer
        $elements = self::generateRightColumnConsumerQuali($elements, $el_date, $innovation);

        // Column Business
        $elements = self::generateRightColumnBusinessQuali($elements, $el_date, $innovation);

        $elements = self::generateRightColumnFooterQuali($elements, $el_date, $innovation);

        /* END
          ---------------------------------------------------------------------------------------- */

        return $elements;
    }

    /**
     * get_innovation_entity_brand_libelle
     * @param innovation
     * @returns {string}
     */
    public static function get_innovation_entity_brand_libelle($innovation){
        $innovation_entity_title = ($innovation['entity']) ? strtoupper($innovation['entity']['title']) : '';
        $innovation_brand_title = 'Missing data';
        $innovation_is_a_service = Innovation::innovationArrayIsAService($innovation);
        if ($innovation['brand']['title']) {
            $innovation_brand_title = $innovation['brand']['title'];
        }
        if ($innovation['new_to_the_world']) {
            $innovation_brand_title = 'NEW';
        }
        if($innovation_is_a_service && $innovation['is_multi_brand']){
            $innovation_brand_title = 'Multi brand';
        }
        return $innovation_entity_title . ' • ' . $innovation_brand_title;
    }

    /**
     * Methode reprise sur drupal
     *
     * @param $elements
     * @param null $el_date
     * @param null $innovation
     * @return mixed
     */
    public static function generateLeftColumnQualiPage1($elements, $el_date = null, $innovation = null)
    {

        /* LEFT COLUMN
          ---------------------------------------------------------------------------------------- */

        // ENTITY - Brand
        if ($innovation['quali']['left_column']['entity_Brand'] !== "") {
            $shape = $elements['current_slide']->createRichTextShape()
                ->setHeight(50)
                ->setWidth(470)
                ->setOffsetX(30)
                ->setOffsetY(30);
            $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_AUTO);
            $textRun = $shape->createTextRun($innovation['quali']['left_column']['entity_Brand']);
            $textRun->getFont()
                ->setSize(22)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color(UtilsPpt::COLOR_b5b5b5));
        }

        // Innovation title
        if ($innovation['quali']['left_column']['innovation_title'] !== "") {
            $shape = $elements['current_slide']->createRichTextShape()
                ->setHeight(140)
                ->setWidth(470)
                ->setOffsetX(30)
                ->setOffsetY(80);
            $textRun = $shape->createTextRun($innovation['quali']['left_column']['innovation_title']);
            $textRun->getFont()
                ->setSize(30)
                ->setName(UtilsPpt::FONT_MONTSERRAT)
                ->setBold(true)
                ->setColor(new Color(UtilsPpt::COLOR_BLACK));
        }

        // Market introduction

        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(50)
            ->setWidth(470)
            ->setOffsetX(30)
            ->setOffsetY(270);
        if ($innovation['quali']['left_column']['market_introduction'] !== "") {
            $textRun = $shape->createTextRun($innovation['quali']['left_column']['market_introduction']);
            $textRun->getFont()
                ->setSize(15)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color(UtilsPpt::COLOR_BLACK));
        } else {
            $textRun = $shape->createTextRun("Missing data");
            $textRun->getFont()
                ->setSize(15)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color(UtilsPpt::COLOR_RED_MISSING_DATA));
        }


        // Stage icon
        $shape = $elements['current_slide']->createDrawingShape();
        $shape->setName('')
            ->setDescription('')
            ->setPath(__DIR__ . '/../../web/ppt/' . $innovation['quali']['left_column']['stage_icon'])
            ->setWidthAndHeight(100, 100)
            ->setOffsetX(350)
            ->setOffsetY(269);
        $shape->setResizeProportional(true);

        if ($innovation['is_frozen']) {
            $shape = $elements['current_slide']->createDrawingShape();
            $shape->setName('')
                ->setDescription('')
                ->setPath(__DIR__ . '/../../web/ppt/icons/stage/stage-frozen-light.png')
                ->setWidthAndHeight(70, 70)
                ->setOffsetX(365)
                ->setOffsetY(284);
            $shape->setResizeProportional(true);
        }

        // Classification icon
        $shape = $elements['current_slide']->createDrawingShape();
        $shape->setName('')
            ->setDescription('')
            ->setPath(__DIR__ . '/../../web/ppt/' . $innovation['quali']['left_column']['classification_icon'])
            ->setWidthAndHeight(64, 64)
            ->setOffsetX(422)
            ->setOffsetY(250);
        $shape->setResizeProportional(true);

        // Type icon
        $shape = $elements['current_slide']->createDrawingShape();
        $shape->setName('')
            ->setDescription('')
            ->setPath(__DIR__ . '/../../web/ppt/' . $innovation['quali']['left_column']['type_icon'])
            ->setWidthAndHeight(64, 64)
            ->setOffsetX(422)
            ->setOffsetY(323);
        $shape->setResizeProportional(true);


        // Lines
        $lines = $innovation['quali']['left_column']['lines_page_1'];


        for ($i = 0; $i < count($lines); $i++) {

            $the_line = $lines[$i];
            $icon_offset_y = 345 + ($i * 70);
            $libelle_offset_y = 352 + ($i * 70);
            if ($the_line['icon'] !== "") {
                $shape = $elements['current_slide']->createDrawingShape();
                $shape->setName('')
                    ->setDescription('')
                    ->setPath(__DIR__ . '/../../web/ppt/' . $the_line['icon'])
                    ->setWidthAndHeight(50, 50)
                    ->setOffsetX(35)
                    ->setOffsetY($icon_offset_y);
                $shape->setResizeProportional(true);
            }

            if ($the_line['libelle'] !== "") {
                $shape = $elements['current_slide']->createRichTextShape()
                    ->setHeight(30)
                    ->setWidth(330)
                    ->setOffsetX(90)
                    ->setOffsetY($libelle_offset_y);
                $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_AUTO);
                $textRun = $shape->createTextRun($the_line['libelle']);
                $textRun->getFont()
                    ->setSize(14)
                    ->setName(UtilsPpt::FONT_WORK_SANS)
                    ->setColor(new Color(UtilsPpt::COLOR_BLACK));
            }
        }

        // CHECK IF INNOVATION IS NOT EARLY STAGE
        if ($innovation['is_a_service'] || (!$innovation['is_a_service'] && !in_array($innovation['current_stage'], array('discover', 'ideate')))) {
            // content 1
            // title Consumer insight
            $shape = $elements['current_slide']->createRichTextShape()
                ->setHeight(40)
                ->setWidth(430)
                ->setOffsetX(40)
                ->setOffsetY(430);
            $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_AUTO);
            $textRun = $shape->createTextRun("Consumer benefit");
            $textRun->getFont()
                ->setSize(16)
                ->setName(UtilsPpt::FONT_MONTSERRAT)
                ->setBold(true)
                ->setColor(new Color(UtilsPpt::COLOR_BLACK));

            $shape = $elements['current_slide']->createRichTextShape()
                ->setHeight(140)
                ->setWidth(430)
                ->setOffsetX(40)
                ->setOffsetY(470);
            if ($innovation['quali']['left_column']['consumer_insight'] !== "") {
                $textRun = $shape->createTextRun($innovation['quali']['left_column']['consumer_insight']);
                $textRun->getFont()
                    ->setSize(14)
                    ->setName(UtilsPpt::FONT_WORK_SANS)
                    ->setColor(new Color(UtilsPpt::COLOR_BLACK));
            } else {
                $shape->getBorder()->setLineStyle(Border::LINE_SINGLE)->setDashStyle(Border::DASH_SOLID)->setColor(new Color(UtilsPpt::COLOR_RED_MISSING_DATA));
                $textRun = $shape->createTextRun("Missing data");
                $textRun->getFont()
                    ->setSize(14)
                    ->setName(UtilsPpt::FONT_WORK_SANS)
                    ->setColor(new Color(UtilsPpt::COLOR_RED_MISSING_DATA));
            }
        }

        // content 1
        $innovation_is_a_service = $Innovation::innovationArrayIsAService($innovation);
        // title Story
        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(40)
            ->setWidth(290)
            ->setOffsetX(40)
            ->setOffsetY(640);
        $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_AUTO);

        if ($innovation_is_a_service) {
            $textRun = $shape->createTextRun("Unique experience");
            $textRun->getFont()
                ->setSize(16)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color(UtilsPpt::COLOR_BLACK));

            $shape = $elements['current_slide']->createRichTextShape()
                ->setHeight(300)
                ->setWidth(430)
                ->setOffsetX(40)
                ->setOffsetY(690);
            $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_AUTO);

            if ($innovation['quali']['left_column']['unique_experience'] !== "") {
                $textRun = $shape->createTextRun($innovation['quali']['left_column']['unique_experience']);
                $textRun->getFont()
                    ->setSize(14)
                    ->setName(UtilsPpt::FONT_WORK_SANS)
                    ->setColor(new Color(UtilsPpt::COLOR_BLACK));
            } else {
                $shape->getBorder()->setLineStyle(Border::LINE_SINGLE)->setDashStyle(Border::DASH_SOLID)->setColor(new Color(UtilsPpt::COLOR_RED_MISSING_DATA));
                $textRun = $shape->createTextRun("Missing data");
                $textRun->getFont()
                    ->setSize(14)
                    ->setName(UtilsPpt::FONT_WORK_SANS)
                    ->setColor(new Color(UtilsPpt::COLOR_RED_MISSING_DATA));
            }
        } else {
            $textRun = $shape->createTextRun("Story");
            $textRun->getFont()
                ->setSize(16)
                ->setName(UtilsPpt::FONT_MONTSERRAT)
                ->setBold(true)
                ->setColor(new Color(UtilsPpt::COLOR_BLACK));

            $shape = $elements['current_slide']->createRichTextShape()
                ->setHeight(300)
                ->setWidth(430)
                ->setOffsetX(40)
                ->setOffsetY(690);
            $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_AUTO);
            if ($innovation['quali']['left_column']['story'] !== "") {
                $textRun = $shape->createTextRun($innovation['quali']['left_column']['story']);
                $textRun->getFont()
                    ->setSize(14)
                    ->setName(UtilsPpt::FONT_WORK_SANS)
                    ->setColor(new Color(UtilsPpt::COLOR_BLACK));
            } else {
                $shape->getBorder()->setLineStyle(Border::LINE_SINGLE)->setDashStyle(Border::DASH_SOLID)->setColor(new Color(UtilsPpt::COLOR_RED_MISSING_DATA));
                $textRun = $shape->createTextRun("Missing data");
                $textRun->getFont()
                    ->setSize(14)
                    ->setName(UtilsPpt::FONT_WORK_SANS)
                    ->setColor(new Color(UtilsPpt::COLOR_RED_MISSING_DATA));
            }
        }

        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(30)
            ->setWidth(530)
            ->setOffsetX(0)
            ->setOffsetY(1050);
        $textRun = $shape->createTextRun(strtoupper("Pernod Ricard") . " STRICLTY CONFIDENTIAL / DO NOT SHARE");
        $textRun->getFont()
            ->setSize(11)
            ->setName(UtilsPpt::FONT_WORK_SANS)
            ->setColor(new Color(UtilsPpt::COLOR_WHITE));

        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(30)
            ->setWidth(530)
            ->setOffsetX(540)
            ->setOffsetY(1050);
        $textRun = $shape->createTextRun("Based on declarative data. Exported  from the " . "Innovation Hub" . ".");
        $textRun->getFont()
            ->setSize(11)
            ->setName(UtilsPpt::FONT_WORK_SANS)
            ->setColor(new Color(UtilsPpt::COLOR_d8d8d8));


        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(30)
            ->setWidth(300)
            ->setOffsetX(1670)
            ->setOffsetY(1050);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $text = date('M d, Y g:i A');
        $textRun = $shape->createTextRun($text);
        $textRun->getFont()
            ->setSize(11)
            ->setName(UtilsPpt::FONT_WORK_SANS)
            ->setColor(new Color(UtilsPpt::COLOR_e3eaf1));


        return $elements;
    }

    /**
     * Methode reprise sur drupal
     *
     * @param $elements
     * @param null $el_date
     * @param null $innovation
     * @return mixed
     */
    public static function generateLeftColumnQuali($elements, $el_date = null, $innovation = null)
    {
        /* LEFT COLUMN
          ---------------------------------------------------------------------------------------- */

        // ENTITY - Brand
        if ($innovation['quali']['left_column']['entity_Brand'] !== "") {
            $shape = $elements['current_slide']->createRichTextShape()
                ->setHeight(50)
                ->setWidth(470)
                ->setOffsetX(30)
                ->setOffsetY(30);
            $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_AUTO);
            $textRun = $shape->createTextRun($innovation['quali']['left_column']['entity_Brand']);
            $textRun->getFont()
                ->setSize(22)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color(UtilsPpt::COLOR_b5b5b5));
        }

        // Innovation title
        if ($innovation['quali']['left_column']['innovation_title'] !== "") {
            $shape = $elements['current_slide']->createRichTextShape()
                ->setHeight(140)
                ->setWidth(470)
                ->setOffsetX(30)
                ->setOffsetY(80);
            $textRun = $shape->createTextRun($innovation['quali']['left_column']['innovation_title']);
            $textRun->getFont()
                ->setSize(30)
                ->setName(UtilsPpt::FONT_MONTSERRAT)
                ->setBold(true)
                ->setColor(new Color(UtilsPpt::COLOR_BLACK));
        }

        // Market introduction
        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(50)
            ->setWidth(470)
            ->setOffsetX(30)
            ->setOffsetY(270);
        if ($innovation['quali']['left_column']['market_introduction'] !== "") {
            $textRun = $shape->createTextRun($innovation['quali']['left_column']['market_introduction']);
            $textRun->getFont()
                ->setSize(15)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color(UtilsPpt::COLOR_BLACK));
        } else {
            $textRun = $shape->createTextRun("Missing data");
            $textRun->getFont()
                ->setSize(15)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color(UtilsPpt::COLOR_RED_MISSING_DATA));
        }


        // Stage icon
        $shape = $elements['current_slide']->createDrawingShape();
        $shape->setName('')
            ->setDescription('')
            ->setPath(__DIR__ . '/../../web/ppt/' . $innovation['quali']['left_column']['stage_icon'])
            ->setWidthAndHeight(100, 100)
            ->setOffsetX(350)
            ->setOffsetY(269);
        $shape->setResizeProportional(true);

        if ($innovation['is_frozen']) {
            $shape = $elements['current_slide']->createDrawingShape();
            $shape->setName('')
                ->setDescription('')
                ->setPath(__DIR__ . '/../../web/ppt/icons/stage/stage-frozen-light.png')
                ->setWidthAndHeight(70, 70)
                ->setOffsetX(365)
                ->setOffsetY(284);
            $shape->setResizeProportional(true);
        }

        // Classification icon
        $shape = $elements['current_slide']->createDrawingShape();
        $shape->setName('')
            ->setDescription('')
            ->setPath(__DIR__ . '/../../web/ppt/' . $innovation['quali']['left_column']['classification_icon'])
            ->setWidthAndHeight(64, 64)
            ->setOffsetX(422)
            ->setOffsetY(250);
        $shape->setResizeProportional(true);

        // Type icon
        $shape = $elements['current_slide']->createDrawingShape();
        $shape->setName('')
            ->setDescription('')
            ->setPath(__DIR__ . '/../../web/ppt/' . $innovation['quali']['left_column']['type_icon'])
            ->setWidthAndHeight(64, 64)
            ->setOffsetX(422)
            ->setOffsetY(323);
        $shape->setResizeProportional(true);


        // Lines
        $lines = $innovation['quali']['left_column']['lines'];


        for ($i = 0; $i < count($lines); $i++) {

            $the_line = $lines[$i];
            $icon_offset_y = 345 + ($i * 50);
            $libelle_offset_y = 349 + ($i * 50);
            if ($the_line['icon'] !== "") {
                $shape = $elements['current_slide']->createDrawingShape();
                $shape->setName('')
                    ->setDescription('')
                    ->setPath(__DIR__ . '/../../web/ppt/' . $the_line['icon'])
                    ->setWidthAndHeight(44, 44)
                    ->setOffsetX(35)
                    ->setOffsetY($icon_offset_y);
                $shape->setResizeProportional(true);
            }

            if ($the_line['libelle'] !== "") {
                $shape = $elements['current_slide']->createRichTextShape()
                    ->setHeight(30)
                    ->setWidth(330)
                    ->setOffsetX(80)
                    ->setOffsetY($libelle_offset_y);
                $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_AUTO);
                $textRun = $shape->createTextRun($the_line['libelle']);
                $textRun->getFont()
                    ->setSize(14)
                    ->setName(UtilsPpt::FONT_WORK_SANS)
                    ->setColor(new Color(UtilsPpt::COLOR_BLACK));
            }
        }


        // Innovation picture
        if ($innovation['quali']['left_column']['picture'] && $innovation['quali']['left_column']['picture'] !== "") {
            $imageData = self::getBase64PictureDataFromUrl($innovation['quali']['left_column']['picture']);
            if ($imageData) {
                $shape = new Base64();
                $shape->setName('')
                    ->setDescription('')
                    ->setResizeProportional(false)
                    ->setData($imageData)
                    ->setWidth(455)
                    ->setHeight(311)
                    ->setOffsetX(35)
                    ->setOffsetY(705);
                /* perfect fit:
                ->setWidth(523)
                    ->setHeight(359)
                    ->setOffsetX(0)
                    ->setOffsetY(690)*/
                $elements['current_slide']->addShape($shape);
            }
        }

        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(30)
            ->setWidth(530)
            ->setOffsetX(0)
            ->setOffsetY(1050);
        $textRun = $shape->createTextRun(strtoupper("Pernod Ricard") . " STRICLTY CONFIDENTIAL / DO NOT SHARE");
        $textRun->getFont()
            ->setSize(11)
            ->setName(UtilsPpt::FONT_WORK_SANS)
            ->setColor(new Color(UtilsPpt::COLOR_WHITE));

        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(30)
            ->setWidth(530)
            ->setOffsetX(540)
            ->setOffsetY(1050);
        $textRun = $shape->createTextRun("Based on declarative data. Exported  from the " . "Innovation Hub" . ".");
        $textRun->getFont()
            ->setSize(11)
            ->setName(UtilsPpt::FONT_WORK_SANS)
            ->setColor(new Color(UtilsPpt::COLOR_d8d8d8));


        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(30)
            ->setWidth(300)
            ->setOffsetX(1670)
            ->setOffsetY(1050);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $text = date('M d, Y g:i A');
        $textRun = $shape->createTextRun($text);
        $textRun->getFont()
            ->setSize(11)
            ->setName(UtilsPpt::FONT_WORK_SANS)
            ->setColor(new Color(UtilsPpt::COLOR_e3eaf1));


        return $elements;
    }

    /**
     * Methode reprise sur drupal
     *
     * @param $elements
     * @param null $el_date
     * @param null $innovation
     * @return mixed
     */
    public static function generateRightColumnHeaderQuali($elements, $el_date = null, $innovation = null)
    {

        // Stage Market Pitch
        if ($innovation['quali']['header']['stage_market_pith'] !== "") {
            $shape = $elements['current_slide']->createRichTextShape()
                ->setHeight(50)
                ->setWidth(525)// SIZE 520
                ->setOffsetX(560)
                ->setOffsetY(30);
            $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_AUTO);
            $textRun = $shape->createTextRun($innovation['quali']['header']['stage_market_pith']);
            $textRun->getFont()
                ->setSize(24)
                ->setName(UtilsPpt::FONT_MONTSERRAT)
                ->setBold(true)
                ->setColor(new Color(UtilsPpt::COLOR_BLACK));
        }

        // Contact picture
        /*
          if ($innovation['quali']['header']['contact']['picture']) {
          $shape = $elements['current_slide']->createDrawingShape();
          $shape->setName('')
          ->setDescription('')
          ->setPath($innovation['quali']['header']['contact']['picture'])
          ->setWidthAndHeight(50, 50)
          ->setOffsetX(1465)
          ->setOffsetY(35);
          $shape->setResizeProportional(true);
          // Contact mask
          $shape = $elements['current_slide']->createDrawingShape();
          $shape->setName('')
          ->setDescription('')
          ->setPath(__DIR__.'/../../web/ppt/default/user-mask.png')
          ->setWidthAndHeight(53, 53)
          ->setOffsetX(1463.5)
          ->setOffsetY(33.5);
          $shape->setResizeProportional(true);
          } */

        // Contact name
        if ($innovation['quali']['header']['contact']['name'] !== "") {
            $shape = $elements['current_slide']->createRichTextShape()
                ->setHeight(30)
                ->setWidth(520)// SIZE 520
                ->setOffsetX(1363)
                ->setOffsetY(35);
            $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_AUTO)->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $textRun = $shape->createTextRun($innovation['quali']['header']['contact']['name']);
            $textRun->getFont()
                ->setSize(13)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color(UtilsPpt::COLOR_BLACK));
        }
        // Contact email
        if ($innovation['quali']['header']['contact']['email'] !== "") {
            $shape = $elements['current_slide']->createRichTextShape()
                ->setHeight(20)
                ->setWidth(520)// SIZE 520
                ->setOffsetX(1363)
                ->setOffsetY(60);
            $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_AUTO)->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $textRun = $shape->createTextRun($innovation['quali']['header']['contact']['email']);
            $textRun->getFont()
                ->setSize(13)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color(UtilsPpt::COLOR_fab700));
        }

        return $elements;
    }

    /**
     * Methode reprise sur drupal
     *
     * @param $elements
     * @param null $el_date
     * @param null $innovation
     * @return mixed
     */
    public static function generateRightColumnValuePropositionQuali($elements, $el_date = null, $innovation = null)
    {
        $the_offsetX = 560;
        
        // title Value proposition
        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(50)
            ->setWidth(300)
            ->setOffsetX($the_offsetX + 90)
            ->setOffsetY(175);
        $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_AUTO);
        $textRun = $shape->createTextRun("Value proposition");
        $textRun->getFont()
            ->setSize(22)
            ->setName(UtilsPpt::FONT_MONTSERRAT)
            ->setBold(true)
            ->setColor(new Color(UtilsPpt::COLOR_BLACK));

        // subtitle 1
        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(50)
            ->setWidth(400)
            ->setOffsetX($the_offsetX)
            ->setOffsetY(260);
        $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_AUTO);
        $textRun = $shape->createTextRun("Uniqueness");
        $textRun->getFont()
            ->setSize(16)
            ->setName(UtilsPpt::FONT_MONTSERRAT)
            ->setColor(new Color(UtilsPpt::COLOR_BLACK));
        // content 1

        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(180)
            ->setWidth(420)
            ->setOffsetX($the_offsetX)
            ->setOffsetY(310);
        $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_AUTO);
        if ($innovation['quali']['value_proposition']['value_proposition'] !== "") {
            $textRun = $shape->createTextRun($innovation['quali']['value_proposition']['value_proposition']);
            $textRun->getFont()
                ->setSize(14)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color(UtilsPpt::COLOR_BLACK));
        } else {
            $shape->getBorder()->setLineStyle(Border::LINE_SINGLE)->setDashStyle(Border::DASH_SOLID)->setColor(new Color(UtilsPpt::COLOR_RED_MISSING_DATA));
            $textRun = $shape->createTextRun("Missing data");
            $textRun->getFont()
                ->setSize(14)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color(UtilsPpt::COLOR_RED_MISSING_DATA));
        }

        // subtitle 2
        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(50)
            ->setWidth(400)
            ->setOffsetX($the_offsetX)
            ->setOffsetY(550);
        $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_AUTO);
        $textRun = $shape->createTextRun("Key information");
        $textRun->getFont()
            ->setSize(16)
            ->setName(UtilsPpt::FONT_WORK_SANS)
            ->setColor(new Color($innovation['quali']['footer']['block_1']['color']));
        // content 2

        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(200)
            ->setWidth(420)
            ->setOffsetX($the_offsetX)
            ->setOffsetY(600);
        $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_AUTO);
        if ($innovation['quali']['key_information'] !== "") {
            $textRun = $shape->createTextRun($innovation['quali']['key_information']);
            $textRun->getFont()
                ->setSize(14)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color(UtilsPpt::COLOR_BLACK));
        } else {
            $shape->getBorder()->setLineStyle(Border::LINE_SINGLE)->setDashStyle(Border::DASH_SOLID)->setColor(new Color(UtilsPpt::COLOR_RED_MISSING_DATA));
            $textRun = $shape->createTextRun("Missing data");
            $textRun->getFont()
                ->setSize(14)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color(UtilsPpt::COLOR_RED_MISSING_DATA));
        }


        // Proof of traction picture 1
        if ($innovation['quali']['proofs_of_traction_picture_1'] && $innovation['quali']['proofs_of_traction_picture_1'] !== "") {
            $imageData = self::getBase64PictureDataFromUrl($innovation['quali']['proofs_of_traction_picture_1']);
            if ($imageData) {
                $shape = new Base64();
                $shape->setName('')
                    ->setDescription('')
                    ->setResizeProportional(false)
                    ->setData($imageData)
                    ->setWidth(400)
                    ->setHeight(222)
                    ->setOffsetX(1020)
                    ->setOffsetY(550);
                $elements['current_slide']->addShape($shape);
            }
            if ($innovation['quali']['proofs_of_traction_legend_1'] !== "") {
                $shape = $elements['current_slide']->createRichTextShape()
                    ->setHeight(40)
                    ->setWidth(420)
                    ->setOffsetX(1010)
                    ->setOffsetY(785);
                $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_AUTO);
                $textRun = $shape->createTextRun($innovation['quali']['proofs_of_traction_legend_1']);
                $textRun->getFont()
                    ->setSize(12)
                    ->setName(UtilsPpt::FONT_WORK_SANS)
                    ->setColor(new Color(UtilsPpt::COLOR_BLACK));
            }
        }


        // Proof of traction picture 2
        if ($innovation['quali']['proofs_of_traction_picture_2'] && $innovation['quali']['proofs_of_traction_picture_2'] !== "") {
            $imageData = self::getBase64PictureDataFromUrl($innovation['quali']['proofs_of_traction_picture_2']);
            if ($imageData) {
                $shape = new Base64();
                $shape->setName('')
                    ->setDescription('')
                    ->setResizeProportional(false)
                    ->setData($imageData)
                    ->setWidth(400)
                    ->setHeight(222)
                    ->setOffsetX(1470)
                    ->setOffsetY(550);
                $elements['current_slide']->addShape($shape);
            }

            if ($innovation['quali']['proofs_of_traction_legend_2'] !== "") {
                $shape = $elements['current_slide']->createRichTextShape()
                    ->setHeight(40)
                    ->setWidth(420)
                    ->setOffsetX(1460)
                    ->setOffsetY(785);
                $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_AUTO);
                $textRun = $shape->createTextRun($innovation['quali']['proofs_of_traction_legend_2']);
                $textRun->getFont()
                    ->setSize(12)
                    ->setName(UtilsPpt::FONT_WORK_SANS)
                    ->setColor(new Color(UtilsPpt::COLOR_BLACK));
            }
        }
        return $elements;
    }



    /**
     * generateRightColumnQualiVeryEarly
     *
     * @param $elements
     * @param null $el_date
     * @param null $innovation
     * @return mixed
     */
    public static function generateRightColumnQualiVeryEarly($elements, $el_date = null, $innovation = null)
    {
        $the_offsetX = 570;


        // title Value proposition
        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(50)
            ->setWidth(405)
            ->setOffsetX($the_offsetX)
            ->setOffsetY(265);
        $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_AUTO);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $textRun = $shape->createTextRun("Value proposition");
        $textRun->getFont()
            ->setSize(22)
            ->setName(UtilsPpt::FONT_MONTSERRAT)
            ->setBold(true)
            ->setColor(new Color(UtilsPpt::COLOR_BLACK));


        // subtitle 1
        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(50)
            ->setWidth(405)
            ->setOffsetX($the_offsetX)
            ->setOffsetY(395);
        $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_AUTO);
        $textRun = $shape->createTextRun("Uniqueness");
        $textRun->getFont()
            ->setSize(18)
            ->setName(UtilsPpt::FONT_MONTSERRAT)
            ->setColor(new Color(UtilsPpt::COLOR_BLACK));
        // content 1


        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(180)
            ->setWidth(405)
            ->setOffsetX($the_offsetX)
            ->setOffsetY(460);
        //$shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
        if ($innovation['quali']['value_proposition']['value_proposition'] !== "") {
            $textRun = $shape->createTextRun($innovation['quali']['value_proposition']['value_proposition']);
            $textRun->getFont()
                ->setSize(16)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color(UtilsPpt::COLOR_BLACK));
        } else {
            $shape->getBorder()->setLineStyle(Border::LINE_SINGLE)->setDashStyle(Border::DASH_SOLID)->setColor(new Color(UtilsPpt::COLOR_RED_MISSING_DATA));
            $textRun = $shape->createTextRun("Missing data");
            $textRun->getFont()
                ->setSize(16)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color(UtilsPpt::COLOR_RED_MISSING_DATA));
        }

        $the_offsetX = 1020;

        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(50)
            ->setWidth(405)
            ->setOffsetX($the_offsetX)
            ->setOffsetY(265);
        $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_AUTO);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $textRun = $shape->createTextRun("Consumer");
        $textRun->getFont()
            ->setSize(22)
            ->setName(UtilsPpt::FONT_MONTSERRAT)
            ->setBold(true)
            ->setColor(new Color(UtilsPpt::COLOR_BLACK));

        // subtitle 1
        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(50)
            ->setWidth(405)
            ->setOffsetX($the_offsetX)
            ->setOffsetY(395);
        $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_AUTO);
        $textRun = $shape->createTextRun("Consumer benefit");
        $textRun->getFont()
            ->setSize(18)
            ->setName(UtilsPpt::FONT_MONTSERRAT)
            ->setColor(new Color(UtilsPpt::COLOR_BLACK));
        // content 1


        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(180)
            ->setWidth(405)
            ->setOffsetX($the_offsetX)
            ->setOffsetY(460);
        //$shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
        if ($innovation['quali']['consumer']['consumer_benefit'] !== "") {
            $textRun = $shape->createTextRun($innovation['quali']['consumer']['consumer_benefit']);
            $textRun->getFont()
                ->setSize(16)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color(UtilsPpt::COLOR_BLACK));
        } else {
            $shape->getBorder()->setLineStyle(Border::LINE_SINGLE)->setDashStyle(Border::DASH_SOLID)->setColor(new Color(UtilsPpt::COLOR_RED_MISSING_DATA));
            $textRun = $shape->createTextRun("Missing data");
            $textRun->getFont()
                ->setSize(16)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color(UtilsPpt::COLOR_RED_MISSING_DATA));
        }


        $the_offsetX = 1470;

        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(50)
            ->setWidth(405)
            ->setOffsetX($the_offsetX)
            ->setOffsetY(265);
        $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_AUTO);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $textRun = $shape->createTextRun("Business");
        $textRun->getFont()
            ->setSize(22)
            ->setName(UtilsPpt::FONT_MONTSERRAT)
            ->setBold(true)
            ->setColor(new Color(UtilsPpt::COLOR_BLACK));

        // subtitle 1
        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(50)
            ->setWidth(405)
            ->setOffsetX($the_offsetX)
            ->setOffsetY(395);
        $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_AUTO);
        $textRun = $shape->createTextRun("Source of business");
        $textRun->getFont()
            ->setSize(18)
            ->setName(UtilsPpt::FONT_MONTSERRAT)
            ->setColor(new Color(UtilsPpt::COLOR_BLACK));
        // content 1


        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(180)
            ->setWidth(405)
            ->setOffsetX($the_offsetX)
            ->setOffsetY(460);
        //$shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
        if ($innovation['quali']['business']['source_of_business'] !== "") {
            $textRun = $shape->createTextRun($innovation['quali']['business']['source_of_business']);
            $textRun->getFont()
                ->setSize(16)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color(UtilsPpt::COLOR_BLACK));
        } else {
            $shape->getBorder()->setLineStyle(Border::LINE_SINGLE)->setDashStyle(Border::DASH_SOLID)->setColor(new Color(UtilsPpt::COLOR_RED_MISSING_DATA));
            $textRun = $shape->createTextRun("Missing data");
            $textRun->getFont()
                ->setSize(16)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color(UtilsPpt::COLOR_RED_MISSING_DATA));
        }




        return $elements;
    }

    /**
     * Methode reprise sur drupal
     *
     * @param $elements
     * @param null $el_date
     * @param null $innovation
     * @return mixed
     */
    public static function generateRightColumnConsumerQuali($elements, $el_date = null, $innovation = null)
    {
        $the_offsetX = 1010;

        // title Consumer
        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(50)
            ->setWidth(300)
            ->setOffsetX($the_offsetX + 90)
            ->setOffsetY(175);
        $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_AUTO);
        $textRun = $shape->createTextRun("Consumer");
        $textRun->getFont()
            ->setSize(22)
            ->setName(UtilsPpt::FONT_MONTSERRAT)
            ->setBold(true)
            ->setColor(new Color(UtilsPpt::COLOR_BLACK));

        // subtitle 1
        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(50)
            ->setWidth(400)
            ->setOffsetX($the_offsetX)
            ->setOffsetY(260);
        $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_AUTO);
        $textRun = $shape->createTextRun("Early adopters persona");
        $textRun->getFont()
            ->setSize(16)
            ->setName(UtilsPpt::FONT_MONTSERRAT)
            ->setColor(new Color(UtilsPpt::COLOR_BLACK));
        // content 1

        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(180)
            ->setWidth(420)
            ->setOffsetX($the_offsetX)
            ->setOffsetY(310);
        $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_AUTO);
        if ($innovation['quali']['consumer']['early_adopter_persona'] !== "") {
            $textRun = $shape->createTextRun($innovation['quali']['consumer']['early_adopter_persona']);
            $textRun->getFont()
                ->setSize(14)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color(UtilsPpt::COLOR_BLACK));
        } else {
            $shape->getBorder()->setLineStyle(Border::LINE_SINGLE)->setDashStyle(Border::DASH_SOLID)->setColor(new Color(UtilsPpt::COLOR_RED_MISSING_DATA));
            $textRun = $shape->createTextRun("Missing data");
            $textRun->getFont()
                ->setSize(14)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color(UtilsPpt::COLOR_RED_MISSING_DATA));
        }

        return $elements;
    }

    /**
     * Methode reprise sur drupal
     *
     * @param $elements
     * @param null $el_date
     * @param null $innovation
     * @return mixed
     */
    public static function generateRightColumnBusinessQuali($elements, $el_date = null, $innovation = null)
    {
        $the_offsetX = 1460;

        // title Consumer
        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(50)
            ->setWidth(300)
            ->setOffsetX($the_offsetX + 90)
            ->setOffsetY(175);
        $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_AUTO);
        $textRun = $shape->createTextRun("Business");
        $textRun->getFont()
            ->setSize(22)
            ->setName(UtilsPpt::FONT_MONTSERRAT)
            ->setBold(true)
            ->setColor(new Color(UtilsPpt::COLOR_BLACK));

        // subtitle 1
        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(50)
            ->setWidth(400)
            ->setOffsetX($the_offsetX)
            ->setOffsetY(260);
        $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_AUTO);
        $textRun = $shape->createTextRun("Source of business");
        $textRun->getFont()
            ->setSize(16)
            ->setName(UtilsPpt::FONT_MONTSERRAT)
            ->setColor(new Color(UtilsPpt::COLOR_BLACK));
        // content 1

        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(180)
            ->setWidth(420)
            ->setOffsetX($the_offsetX)
            ->setOffsetY(310);
        $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_AUTO);
        if ($innovation['quali']['business']['source_of_business'] !== "") {
            $textRun = $shape->createTextRun($innovation['quali']['business']['source_of_business']);
            $textRun->getFont()
                ->setSize(14)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color(UtilsPpt::COLOR_BLACK));
        } else {
            $shape->getBorder()->setLineStyle(Border::LINE_SINGLE)->setDashStyle(Border::DASH_SOLID)->setColor(new Color(UtilsPpt::COLOR_RED_MISSING_DATA));
            $textRun = $shape->createTextRun("Missing data");
            $textRun->getFont()
                ->setSize(14)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color(UtilsPpt::COLOR_RED_MISSING_DATA));
        }
        return $elements;
    }

    /**
     * Methode reprise sur drupal
     *
     * @param $elements
     * @param null $el_date
     * @param null $innovation
     * @return mixed
     */
    public static function generateRightColumnFooterQuali($elements, $el_date = null, $innovation = null)
    {
        $stage_color = $innovation['quali']['footer']['block_1']['color'];

        $the_width = 650;
        // subtitle 1
        if ($innovation['quali']['footer']['block_1']['title'] !== "") {
            $shape = $elements['current_slide']->createRichTextShape()
                ->setHeight(50)
                ->setWidth($the_width)
                ->setOffsetX(560)
                ->setOffsetY(865);
            $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_AUTO);
            $textRun = $shape->createTextRun($innovation['quali']['footer']['block_1']['title']);
            $textRun->getFont()
                ->setSize(16)
                ->setName(UtilsPpt::FONT_MONTSERRAT)
                ->setColor(new Color($stage_color));
        }
        // content 1

        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(110)
            ->setWidth($the_width)
            ->setOffsetX(560)
            ->setOffsetY(915);
        $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_AUTO);
        if ($innovation['quali']['footer']['block_1']['text'] && $innovation['quali']['footer']['block_1']['text'] !== "") {
            $textRun = $shape->createTextRun($innovation['quali']['footer']['block_1']['text']);
            $textRun->getFont()
                ->setSize(14)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color(UtilsPpt::COLOR_BLACK));
        } else {
            $shape->getBorder()->setLineStyle(Border::LINE_SINGLE)->setDashStyle(Border::DASH_SOLID)->setColor(new Color(UtilsPpt::COLOR_RED_MISSING_DATA));
            $textRun = $shape->createTextRun("Missing data");
            $textRun->getFont()
                ->setSize(14)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color(UtilsPpt::COLOR_RED_MISSING_DATA));
        }


        // subtitle 2
        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(50)
            ->setWidth($the_width)
            ->setOffsetX(1230)
            ->setOffsetY(865);
        $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_AUTO);
        $libelle = "Next steps";
        $textRun = $shape->createTextRun($libelle);
        $textRun->getFont()
            ->setSize(16)
            ->setName(UtilsPpt::FONT_MONTSERRAT)
            ->setColor(new Color($stage_color));
        // content 2
        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(110)
            ->setWidth($the_width)
            ->setOffsetX(1230)
            ->setOffsetY(915);
        $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_AUTO);
        if ($innovation['quali']['footer']['next_steps'] && $innovation['quali']['footer']['next_steps'] !== "") {
            $textRun = $shape->createTextRun($innovation['quali']['footer']['next_steps']);
            $textRun->getFont()
                ->setSize(14)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color(UtilsPpt::COLOR_BLACK));
        } else {
            $shape->getBorder()->setLineStyle(Border::LINE_SINGLE)->setDashStyle(Border::DASH_SOLID)->setColor(new Color(UtilsPpt::COLOR_RED_MISSING_DATA));
            $textRun = $shape->createTextRun("Missing data");
            $textRun->getFont()
                ->setSize(14)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color(UtilsPpt::COLOR_RED_MISSING_DATA));
        }


        return $elements;
    }

    /**
     * Methode reprise sur drupal
     *
     * @param $elements
     * @param null $el_date
     * @param null $innovation
     * @return mixed
     */
    public static function addInnovationQuantiPage($elements, $el_date = null, $innovation = null)
    {
        $elements['nb_slide']++;
        $elements['current_slide'] = $elements['ppt']->createSlide();
        // Ajout du background
        $shape = $elements['current_slide']->createDrawingShape();
        $shape->setName('')
            ->setDescription('')
            ->setPath(__DIR__ . '/../../web/ppt/couvs/couv-quanti-1-v2.png')
            ->setHeight(1080)
            ->setOffsetX(0)
            ->setOffsetY(0);
        $shape->getShadow()->setVisible(false)
            ->setDirection(0)
            ->setDistance(0);


        /* LEFT COLUMN
          ---------------------------------------------------------------------------------------- */

        $elements = self::generateLeftColumnQuanti($elements, $el_date, $innovation);


        /* RIGHT COLUMN
          ---------------------------------------------------------------------------------------- */


        $elements = self::generateRightColumnHeaderQuanti($elements, $el_date, $innovation);

        // Chart
        $elements = self::generateRightColumnChartQuanti($elements, $el_date, $innovation);

        // Column Comment
        $elements = self::generateRightColumnCommentQuanti($elements, $el_date, $innovation);

        // Column Financial Left (Consolidated performance)
        $elements = self::generateRightColumnFinancialLeft($elements, $el_date, $innovation);

        // Column Financial Center (Key indicators)
        $elements = self::generateRightColumnFinancialCenter($elements, $el_date, $innovation);

        // Column Financial Right (Performance over time)
        $elements = self::generateRightColumnFinancialRight($elements, $el_date, $innovation);

        // COLUMN FOOTER INFOS
        $elements = self::generateRightColumnFooterInfos($elements, $el_date, $innovation);

        /* END
          ---------------------------------------------------------------------------------------- */

        return $elements;
    }

    /**
     * Methode reprise sur drupal
     *
     * @param $elements
     * @param null $el_date
     * @param null $innovation
     * @return mixed
     */
    public static function generateRightColumnFooterInfos($elements, $el_date = null, $innovation = null)
    {
        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(30)
            ->setWidth(530)
            ->setOffsetX(0)
            ->setOffsetY(1050);
        $textRun = $shape->createTextRun(strtoupper("Pernod Ricard") . " STRICLTY CONFIDENTIAL / DO NOT SHARE");
        $textRun->getFont()
            ->setSize(11)
            ->setName(UtilsPpt::FONT_WORK_SANS)
            ->setColor(new Color(UtilsPpt::COLOR_WHITE));

        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(30)
            ->setWidth(530)
            ->setOffsetX(540)
            ->setOffsetY(1050);
        $textRun = $shape->createTextRun("Based on declarative data. Exported  from the " . "Innovation Hub" . ".");
        $textRun->getFont()
            ->setSize(11)
            ->setName(UtilsPpt::FONT_WORK_SANS)
            ->setColor(new Color(UtilsPpt::COLOR_d8d8d8));


        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(30)
            ->setWidth(300)
            ->setOffsetX(1670)
            ->setOffsetY(1050);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $text = date('M d, Y g:i A');
        $textRun = $shape->createTextRun($text);
        $textRun->getFont()
            ->setSize(11)
            ->setName(UtilsPpt::FONT_WORK_SANS)
            ->setColor(new Color(UtilsPpt::COLOR_e3eaf1));

        return $elements;
    }

    /**
     * Methode reprise sur drupal
     *
     * @param $elements
     * @param null $el_date
     * @param null $innovation
     * @return mixed
     */
    public static function generateLeftColumnQuanti($elements, $el_date = null, $innovation = null)
    {

        /* LEFT COLUMN
          ---------------------------------------------------------------------------------------- */

        // ENTITY - Brand
        if ($innovation['quanti']['left_column']['entity_Brand'] !== "") {
            $shape = $elements['current_slide']->createRichTextShape()
                ->setHeight(50)
                ->setWidth(470)
                ->setOffsetX(30)
                ->setOffsetY(30);
            $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_AUTO);
            $textRun = $shape->createTextRun($innovation['quanti']['left_column']['entity_Brand']);
            $textRun->getFont()
                ->setSize(22)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color(UtilsPpt::COLOR_b5b5b5));
        }

        // Innovation title
        if ($innovation['quanti']['left_column']['innovation_title'] !== "") {
            $shape = $elements['current_slide']->createRichTextShape()
                ->setHeight(140)
                ->setWidth(470)
                ->setOffsetX(30)
                ->setOffsetY(80);
            $textRun = $shape->createTextRun($innovation['quanti']['left_column']['innovation_title']);
            $textRun->getFont()
                ->setSize(30)
                ->setName(UtilsPpt::FONT_MONTSERRAT)
                ->setBold(true)
                ->setColor(new Color(UtilsPpt::COLOR_BLACK));
        }

        // Market introduction
        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(50)
            ->setWidth(470)
            ->setOffsetX(30)
            ->setOffsetY(270);

        if ($innovation['quali']['left_column']['market_introduction'] !== "") {
            $textRun = $shape->createTextRun($innovation['quanti']['left_column']['market_introduction']);
            $textRun->getFont()
                ->setSize(15)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color(UtilsPpt::COLOR_BLACK));
        } else {
            $textRun = $shape->createTextRun("Missing data");
            $textRun->getFont()
                ->setSize(15)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color(UtilsPpt::COLOR_RED_MISSING_DATA));
        }


        // Stage icon
        $shape = $elements['current_slide']->createDrawingShape();
        $shape->setName('')
            ->setDescription('')
            ->setPath(__DIR__ . '/../../web/ppt/' . $innovation['quanti']['left_column']['stage_icon'])
            ->setWidthAndHeight(100, 100)
            ->setOffsetX(350)
            ->setOffsetY(269);
        $shape->setResizeProportional(true);

        if ($innovation['is_frozen']) {
            $shape = $elements['current_slide']->createDrawingShape();
            $shape->setName('')
                ->setDescription('')
                ->setPath(__DIR__ . '/../../web/ppt/icons/stage/stage-frozen-light.png')
                ->setWidthAndHeight(70, 70)
                ->setOffsetX(365)
                ->setOffsetY(284);
            $shape->setResizeProportional(true);
        }

        // Class icon
        $shape = $elements['current_slide']->createDrawingShape();
        $shape->setName('')
            ->setDescription('')
            ->setPath(__DIR__ . '/../../web/ppt/' . $innovation['quanti']['left_column']['classification_icon'])
            ->setWidthAndHeight(64, 64)
            ->setOffsetX(422)
            ->setOffsetY(250);
        $shape->setResizeProportional(true);

        // Type icon
        $shape = $elements['current_slide']->createDrawingShape();
        $shape->setName('')
            ->setDescription('')
            ->setPath(__DIR__ . '/../../web/ppt/' . $innovation['quanti']['left_column']['type_icon'])
            ->setWidthAndHeight(64, 64)
            ->setOffsetX(422)
            ->setOffsetY(323);
        $shape->setResizeProportional(true);


        // Lines
        $lines = $innovation['quanti']['left_column']['lines'];

        for ($i = 0; $i < count($lines); $i++) {

            $the_line = $lines[$i];
            $icon_offset_y = 345 + ($i * 70);
            $libelle_offset_y = 352 + ($i * 70);
            if ($the_line['icon'] !== "") {
                $shape = $elements['current_slide']->createDrawingShape();
                $shape->setName('')
                    ->setDescription('')
                    ->setPath(__DIR__ . '/../../web/ppt/' . $the_line['icon'])
                    ->setWidthAndHeight(50, 50)
                    ->setOffsetX(35)
                    ->setOffsetY($icon_offset_y);
                $shape->setResizeProportional(true);
            }

            if ($the_line['libelle'] !== "") {
                $shape = $elements['current_slide']->createRichTextShape()
                    ->setHeight(30)
                    ->setWidth(330)
                    ->setOffsetX(90)
                    ->setOffsetY($libelle_offset_y);
                $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_AUTO);
                $textRun = $shape->createTextRun($the_line['libelle']);
                $textRun->getFont()
                    ->setSize(14)
                    ->setName(UtilsPpt::FONT_WORK_SANS)
                    ->setColor(new Color(UtilsPpt::COLOR_BLACK));
            }
        }

        // content 1
        if ($innovation['quanti']['comment']['value'] !== "") {
            // title Comment
            $shape = $elements['current_slide']->createRichTextShape()
                ->setHeight(40)
                ->setWidth(290)
                ->setOffsetX(40)
                ->setOffsetY(590);
            $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_AUTO);
            $textRun = $shape->createTextRun("Performance review");
            $textRun->getFont()
                ->setSize(16)
                ->setName(UtilsPpt::FONT_MONTSERRAT)
                ->setColor(new Color(UtilsPpt::COLOR_BLACK));
            $shape = $elements['current_slide']->createRichTextShape()
                ->setHeight(400)
                ->setWidth(430)
                ->setOffsetX(40)
                ->setOffsetY(640);
            $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_AUTO);
            $textRun = $shape->createTextRun($innovation['quanti']['comment']['value']);
            $textRun->getFont()
                ->setSize(14)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color(UtilsPpt::COLOR_BLACK));
        } else if ($innovation['quali']['left_column']['beautyshot_quali_bg'] && $innovation['quali']['left_column']['beautyshot_quali_bg'] != "") {
            $imageData = self::getBase64PictureDataFromUrl($innovation['quali']['left_column']['beautyshot_quali_bg']);
            if ($imageData) {
                $shape = new Base64();
                $shape->setName('')
                    ->setDescription('')
                    ->setResizeProportional(false)
                    ->setData($imageData)
                    ->setWidth(430)
                    ->setHeight(324)
                    ->setOffsetX(40)
                    ->setOffsetY(640);
                $elements['current_slide']->addShape($shape);
            }
        } else {
            $shape = $elements['current_slide']->createRichTextShape()
                ->setHeight(400)
                ->setWidth(430)
                ->setOffsetX(40)
                ->setOffsetY(640);
            $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_AUTO);
            $textRun = $shape->createTextRun("Missing data");
            $textRun->getFont()
                ->setSize(14)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color(UtilsPpt::COLOR_RED_MISSING_DATA));
        }

        return $elements;
    }

    /**
     * Methode reprise sur drupal
     *
     * @param $elements
     * @param null $el_date
     * @param null $innovation
     * @return mixed
     */
    public static function generateRightColumnHeaderQuanti($elements, $el_date = null, $innovation = null)
    {
        // Stage Trimester
        if ($innovation['quanti']['header']['stage_trimester'] !== "") {
            $shape = $elements['current_slide']->createRichTextShape()
                ->setHeight(50)
                ->setWidth(825)// SIZE 520
                ->setOffsetX(560)
                ->setOffsetY(30);
            $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_AUTO);
            $textRun = $shape->createTextRun($innovation['quanti']['header']['stage_trimester']);
            $textRun->getFont()
                ->setSize(24)
                ->setName(UtilsPpt::FONT_MONTSERRAT)
                ->setBold(true)
                ->setColor(new Color(UtilsPpt::COLOR_BLACK));
        }

        return $elements;
    }

    /**
     * Methode reprise sur drupal
     *
     * @param $elements
     * @param null $el_date
     * @param null $innovation
     * @return mixed
     */
    public static function generateRightColumnChartQuanti($elements, $el_date = null, $innovation = null)
    {
        if ($innovation['quanti']['chart']['picture'] && $innovation['quanti']['chart']['picture'] !== "") {
            $imageData = self::getBase64PictureDataFromUrl($innovation['quanti']['chart']['picture']);
            if ($imageData) {
                $shape = new Base64();
                $shape->setName('')
                    ->setDescription('')
                    ->setResizeProportional(false)
                    ->setData($imageData)
                    ->setWidth(827)
                    ->setHeight(500)
                    ->setOffsetX(570)
                    ->setOffsetY(140);
                $elements['current_slide']->addShape($shape);
            }
        }

        return $elements;
    }

    /**
     * Methode reprise sur drupal
     *
     * @param $elements
     * @param null $el_date
     * @param null $innovation
     * @return mixed
     */
    public static function generateRightColumnCommentQuanti($elements, $el_date = null, $innovation = null)
    {
        // Innovation picture
        if ($innovation['quanti']['left_column']['picture'] && $innovation['quanti']['left_column']['picture'] !== "") {
            $imageData = self::getBase64PictureDataFromUrl($innovation['quanti']['left_column']['picture']);
            if ($imageData) {
                $shape = new Base64();
                $shape->setName('')
                    ->setDescription('')
                    ->setResizeProportional(false)
                    ->setData($imageData)
                    ->setWidth(300)
                    ->setHeight(480)
                    ->setOffsetX(1545)
                    ->setOffsetY(140);
                $elements['current_slide']->addShape($shape);
            }
        }

        return $elements;
    }

    /**
     * generateRightColumnFinancialLeft
     *
     * @param $elements
     * @param null $el_date
     * @param null $innovation
     * @return mixed
     */
    public static function generateRightColumnFinancialLeft($elements, $el_date = null, $innovation = null)
    {
        $the_offsetX = 560;
        $versus_date = str_replace('final', '', str_replace('_', ' ', self::getLibelleBudgetCurrentYear($el_date)));

        // title Evolution over time
        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(50)
            ->setWidth(400)
            ->setOffsetX($the_offsetX)
            ->setOffsetY(690);
        $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_AUTO);
        $libelle_budget_next_year = self::getFinancialDateLibelle($el_date);
        $textRun = $shape->createTextRun("Consolidated performance ".$libelle_budget_next_year);
        $textRun->getFont()
            ->setSize(19)
            ->setName(UtilsPpt::FONT_MONTSERRAT)
            ->setBold(true)
            ->setColor(new Color(UtilsPpt::COLOR_BLACK));
        $libelle_current_a = self::getLibelleBudgetCurrentYear($el_date);
        $textRun = $shape->createTextRun(" vs ".$libelle_current_a."");
        $textRun->getFont()
            ->setSize(19)
            ->setName(UtilsPpt::FONT_MONTSERRAT)
            ->setBold(true)
            ->setColor(new Color(UtilsPpt::COLOR_03AFF0));


        /*
        // SOUS TITRE since A15
        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(30)
            ->setWidth(660)
            ->setOffsetX($the_offsetX)
            ->setOffsetY(730);
        $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_AUTO);
        $libelle_fy = self::getLibelleFY($el_date);
        $textRun = $shape->createTextRun("*Evolutions at current rate. All figures from ".$libelle_fy." are restated for IFRS15 norm.");
        $textRun->getFont()
            ->setSize(12)
            ->setName(UtilsPpt::FONT_WORK_SANS)
            ->setColor(new Color(UtilsPpt::COLOR_03AFF0));
        */

        $column_1 = $innovation['quanti']['financial_stats_left']['column_1'];

        $y_add = 130;
        for ($i = 0; $i < count($column_1); $i++) {

            $the_block = $column_1[$i];
            $title_offset_y = 800 + ($i * $y_add);
            $value_offset_y = 823 + ($i * $y_add);
            $icon_offset_y = 876 + ($i * $y_add);
            $percent_offset_y = 863 + ($i * $y_add);

            // block info 1 title
            if ($the_block['title'] !== "") {
                $shape = $elements['current_slide']->createRichTextShape()
                    ->setHeight(22)
                    ->setWidth(280)
                    ->setOffsetX($the_offsetX)
                    ->setOffsetY($title_offset_y);
                $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_AUTO);
                $textRun = $shape->createTextRun($the_block['title']);
                $textRun->getFont()
                    ->setSize(13)
                    ->setName(UtilsPpt::FONT_WORK_SANS)
                    ->setColor(new Color(UtilsPpt::COLOR_9b9b9b));
            }
            // block info 1 value
            if ($the_block['value'] !== "") {
                $shape = $elements['current_slide']->createRichTextShape()
                    ->setHeight(36)
                    ->setWidth(280)
                    ->setOffsetX($the_offsetX)
                    ->setOffsetY($value_offset_y);
                $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_AUTO);
                $textRun = $shape->createTextRun($the_block['value']);
                $textRun->getFont()
                    ->setSize(22)
                    ->setName(UtilsPpt::FONT_WORK_SANS)
                    ->setColor(new Color(UtilsPpt::COLOR_BLACK));
            }

            if ($the_block['percent_value'] == "NEW") {
                $the_offsetX = $the_offsetX - 5;
            }
            // block info 1 arrow
            if ($the_block['percent_icon'] !== "") {
                $shape = $elements['current_slide']->createDrawingShape();
                $shape->setName('')
                    ->setDescription('')
                    ->setPath(__DIR__ . '/../../web/ppt/' . $the_block['percent_icon'])
                    ->setWidthAndHeight(11, 11)
                    ->setOffsetX($the_offsetX + 11)
                    ->setOffsetY($icon_offset_y);
                $shape->setResizeProportional(true);
            }
            // block info 1 percent
            if ($the_block['percent_value'] !== "") {
                $percent_width = 200;
                $shape = $elements['current_slide']->createRichTextShape()
                    ->setHeight(20)
                    ->setWidth($percent_width)
                    ->setOffsetX($the_offsetX + 15)
                    ->setOffsetY($percent_offset_y);
                $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_AUTO);
                $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $textRun = $shape->createTextRun($the_block['percent_value']);
                $textRun->getFont()
                    ->setSize(14)
                    ->setName(UtilsPpt::FONT_WORK_SANS)
                    ->setColor(new Color($the_block['percent_color']));
                /*
                if ($versus_date !== "" && $the_block['percent_value'] !== "NEW") {
                    $textRun = $shape->createTextRun(" vs " . $versus_date);
                    $textRun->getFont()
                        ->setSize(14)
                        ->setName(UtilsPpt::FONT_WORK_SANS)
                        ->setColor(new Color(UtilsPpt::COLOR_b5b5b5));
                }
                */
            }
        }
        $the_offsetX = 825;
        $column_2 = $innovation['quanti']['financial_stats_left']['column_2'];
        for ($i = 0; $i < count($column_2); $i++) {

            $the_block = $column_2[$i];
            $title_offset_y = 800 + ($i * $y_add);
            $value_offset_y = 823 + ($i * $y_add);
            $icon_offset_y = 876 + ($i * $y_add);
            $percent_offset_y = 863 + ($i * $y_add);

            // block info 1 title
            if ($the_block['title'] !== "") {
                $shape = $elements['current_slide']->createRichTextShape()
                    ->setHeight(22)
                    ->setWidth(280)
                    ->setOffsetX($the_offsetX)
                    ->setOffsetY($title_offset_y);
                $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_AUTO);
                $textRun = $shape->createTextRun($the_block['title']);
                $textRun->getFont()
                    ->setSize(13)
                    ->setName(UtilsPpt::FONT_WORK_SANS)
                    ->setColor(new Color(UtilsPpt::COLOR_9b9b9b));
            }
            // block info 1 value
            if ($the_block['value'] !== "") {
                $shape = $elements['current_slide']->createRichTextShape()
                    ->setHeight(36)
                    ->setWidth(280)
                    ->setOffsetX($the_offsetX)
                    ->setOffsetY($value_offset_y);
                $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_AUTO);
                $textRun = $shape->createTextRun($the_block['value']);
                $textRun->getFont()
                    ->setSize(22)
                    ->setName(UtilsPpt::FONT_WORK_SANS)
                    ->setColor(new Color(UtilsPpt::COLOR_BLACK));
            }

            if ($the_block['percent_value'] == "NEW") {
                $the_offsetX = $the_offsetX - 5;
            }
            // block info 1 arrow
            if ($the_block['percent_icon'] !== "") {
                $shape = $elements['current_slide']->createDrawingShape();
                $shape->setName('')
                    ->setDescription('')
                    ->setPath(__DIR__ . '/../../web/ppt/' . $the_block['percent_icon'])
                    ->setWidthAndHeight(11, 30)
                    ->setOffsetX($the_offsetX + 11)
                    ->setOffsetY($icon_offset_y);
                $shape->setResizeProportional(true);
            }
            // block info 1 percent
            if ($the_block['percent_value'] !== "") {
                $percent_width = 200;
                $shape = $elements['current_slide']->createRichTextShape()
                    ->setHeight(20)
                    ->setWidth($percent_width)
                    ->setOffsetX($the_offsetX + 15)
                    ->setOffsetY($percent_offset_y);
                $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_AUTO);
                $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $textRun = $shape->createTextRun($the_block['percent_value']);
                $textRun->getFont()
                    ->setSize(14)
                    ->setName(UtilsPpt::FONT_WORK_SANS)
                    ->setColor(new Color($the_block['percent_color']));
                /*
                if ($versus_date !== "" && $the_block['percent_value'] !== "NEW") {
                    $textRun = $shape->createTextRun(" vs " . $versus_date);
                    $textRun->getFont()
                        ->setSize(14)
                        ->setName(UtilsPpt::FONT_WORK_SANS)
                        ->setColor(new Color(UtilsPpt::COLOR_b5b5b5));
                }
                */
            }
        }



        return $elements;
    }


    /**
     * generateRightColumnFinancialCenter
     *
     * @param $elements
     * @param null $el_date
     * @param null $innovation
     * @return mixed
     */
    public static function generateRightColumnFinancialCenter($elements, $el_date = null, $innovation = null)
    {
        $the_offsetX = 1115;

        // title Evolution over time
        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(50)
            ->setWidth(400)
            ->setOffsetX($the_offsetX)
            ->setOffsetY(690);
        $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_AUTO);
        $textRun = $shape->createTextRun("Key indicators");
        $textRun->getFont()
            ->setSize(19)
            ->setName(UtilsPpt::FONT_MONTSERRAT)
            ->setBold(true)
            ->setColor(new Color(UtilsPpt::COLOR_BLACK));


        $column_1 = $innovation['quanti']['financial_stats_center']['column_1'];

        $y_add = 130;
        for ($i = 0; $i < count($column_1); $i++) {

            $the_block = $column_1[$i];
            $title_offset_y = 800 + ($i * $y_add);
            $value_offset_y = 823 + ($i * $y_add);
            $icon_offset_y = 876 + ($i * $y_add);
            $percent_offset_y = 863 + ($i * $y_add);

            // block info 1 title
            if ($the_block['title'] !== "") {
                $shape = $elements['current_slide']->createRichTextShape()
                    ->setHeight(22)
                    ->setWidth(280)
                    ->setOffsetX($the_offsetX)
                    ->setOffsetY($title_offset_y);
                $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_AUTO);
                $textRun = $shape->createTextRun($the_block['title']);
                $textRun->getFont()
                    ->setSize(13)
                    ->setName(UtilsPpt::FONT_WORK_SANS)
                    ->setColor(new Color(UtilsPpt::COLOR_9b9b9b));
            }
            // block info 1 value
            if ($the_block['value'] !== "") {
                $shape = $elements['current_slide']->createRichTextShape()
                    ->setHeight(36)
                    ->setWidth(280)
                    ->setOffsetX($the_offsetX)
                    ->setOffsetY($value_offset_y);
                $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_AUTO);
                $textRun = $shape->createTextRun($the_block['value']);
                $textRun->getFont()
                    ->setSize(22)
                    ->setName(UtilsPpt::FONT_WORK_SANS)
                    ->setColor(new Color(UtilsPpt::COLOR_BLACK));
            }

            if ($the_block['percent_value'] == "NEW") {
                $the_offsetX = $the_offsetX - 5;
            }
            // block info 1 arrow
            if ($the_block['percent_icon'] !== "") {
                $shape = $elements['current_slide']->createDrawingShape();
                $shape->setName('')
                    ->setDescription('')
                    ->setPath(__DIR__ . '/../../web/ppt/' . $the_block['percent_icon'])
                    ->setWidthAndHeight(11, 11)
                    ->setOffsetX($the_offsetX + 11)
                    ->setOffsetY($icon_offset_y);
                $shape->setResizeProportional(true);
            }
            // block info 1 percent
            if ($the_block['percent_value'] !== "") {
                $percent_width = 200;
                $shape = $elements['current_slide']->createRichTextShape()
                    ->setHeight(20)
                    ->setWidth($percent_width)
                    ->setOffsetX($the_offsetX + 15)
                    ->setOffsetY($percent_offset_y);
                $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_AUTO);
                $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $textRun = $shape->createTextRun($the_block['percent_value']);
                $textRun->getFont()
                    ->setSize(14)
                    ->setName(UtilsPpt::FONT_WORK_SANS)
                    ->setColor(new Color($the_block['percent_color']));
                /*
                if ($versus_date !== "" && $the_block['percent_value'] !== "NEW") {
                    $textRun = $shape->createTextRun(" vs " . $versus_date);
                    $textRun->getFont()
                        ->setSize(14)
                        ->setName(UtilsPpt::FONT_WORK_SANS)
                        ->setColor(new Color(UtilsPpt::COLOR_b5b5b5));
                }
                */
            }
        }

        $the_offsetX = 1380;
        $column_2 = $innovation['quanti']['financial_stats_center']['column_2'];
        for ($i = 0; $i < count($column_2); $i++) {

            $the_block = $column_2[$i];
            $title_offset_y = 800 + ($i * $y_add);
            $value_offset_y = 823 + ($i * $y_add);
            $icon_offset_y = 876 + ($i * $y_add);
            $percent_offset_y = 863 + ($i * $y_add);

            // block info 1 title
            if ($the_block['title'] !== "") {
                $shape = $elements['current_slide']->createRichTextShape()
                    ->setHeight(22)
                    ->setWidth(280)
                    ->setOffsetX($the_offsetX)
                    ->setOffsetY($title_offset_y);
                $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_AUTO);
                $textRun = $shape->createTextRun($the_block['title']);
                $textRun->getFont()
                    ->setSize(13)
                    ->setName(UtilsPpt::FONT_WORK_SANS)
                    ->setColor(new Color(UtilsPpt::COLOR_9b9b9b));
            }
            // block info 1 value
            if ($the_block['value'] !== "") {
                $shape = $elements['current_slide']->createRichTextShape()
                    ->setHeight(36)
                    ->setWidth(280)
                    ->setOffsetX($the_offsetX)
                    ->setOffsetY($value_offset_y);
                $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_AUTO);
                $textRun = $shape->createTextRun($the_block['value']);
                $textRun->getFont()
                    ->setSize(22)
                    ->setName(UtilsPpt::FONT_WORK_SANS)
                    ->setColor(new Color(UtilsPpt::COLOR_BLACK));
            }

            if ($the_block['percent_value'] == "NEW") {
                $the_offsetX = $the_offsetX - 5;
            }
            // block info 1 arrow
            if ($the_block['percent_icon'] !== "") {
                $shape = $elements['current_slide']->createDrawingShape();
                $shape->setName('')
                    ->setDescription('')
                    ->setPath(__DIR__ . '/../../web/ppt/' . $the_block['percent_icon'])
                    ->setWidthAndHeight(11, 11)
                    ->setOffsetX($the_offsetX + 11)
                    ->setOffsetY($icon_offset_y);
                $shape->setResizeProportional(true);
            }
            // block info 1 percent
            if ($the_block['percent_value'] !== "") {
                $percent_width = 200;
                $shape = $elements['current_slide']->createRichTextShape()
                    ->setHeight(20)
                    ->setWidth($percent_width)
                    ->setOffsetX($the_offsetX + 15)
                    ->setOffsetY($percent_offset_y);
                $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_AUTO);
                $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $textRun = $shape->createTextRun($the_block['percent_value']);
                $textRun->getFont()
                    ->setSize(14)
                    ->setName(UtilsPpt::FONT_WORK_SANS)
                    ->setColor(new Color($the_block['percent_color']));
                /*
                if ($versus_date !== "" && $the_block['percent_value'] !== "NEW") {
                    $textRun = $shape->createTextRun(" vs " . $versus_date);
                    $textRun->getFont()
                        ->setSize(14)
                        ->setName(UtilsPpt::FONT_WORK_SANS)
                        ->setColor(new Color(UtilsPpt::COLOR_b5b5b5));
                }
                */
            }
        }
        return $elements;
    }


    /**
     * generateRightColumnFinancialRight
     *
     * @param $elements
     * @param null $el_date
     * @param null $innovation
     * @return mixed
     */
    public static function generateRightColumnFinancialRight($elements, $el_date = null, $innovation = null)
    {
        $the_offsetX = 1665;
        $versus_date = str_replace('final', '', str_replace('_', ' ', self::getLibelleBudgetCurrentYear($el_date)));

        // title Evolution over time
        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(50)
            ->setWidth(200)
            ->setOffsetX($the_offsetX)
            ->setOffsetY(690);
        $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_AUTO);
        $textRun = $shape->createTextRun("Performance over time");
        $textRun->getFont()
            ->setSize(19)
            ->setName(UtilsPpt::FONT_MONTSERRAT)
            ->setBold(true)
            ->setColor(new Color(UtilsPpt::COLOR_BLACK));


        $column_1 = $innovation['quanti']['financial_stats_right']['column_1'];

        $y_add = 130;
        for ($i = 0; $i < count($column_1); $i++) {

            $the_block = $column_1[$i];
            $title_offset_y = 800 + ($i * $y_add);
            $value_offset_y = 823 + ($i * $y_add);
            $icon_offset_y = 876 + ($i * $y_add);
            $percent_offset_y = 863 + ($i * $y_add);

            // block info 1 title
            if ($the_block['title'] !== "") {
                $shape = $elements['current_slide']->createRichTextShape()
                    ->setHeight(22)
                    ->setWidth(280)
                    ->setOffsetX($the_offsetX)
                    ->setOffsetY($title_offset_y);
                $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_AUTO);
                $textRun = $shape->createTextRun($the_block['title']);
                $textRun->getFont()
                    ->setSize(13)
                    ->setName(UtilsPpt::FONT_WORK_SANS)
                    ->setColor(new Color(UtilsPpt::COLOR_9b9b9b));
            }
            // block info 1 value
            if ($the_block['value'] !== "") {
                $shape = $elements['current_slide']->createRichTextShape()
                    ->setHeight(36)
                    ->setWidth(280)
                    ->setOffsetX($the_offsetX)
                    ->setOffsetY($value_offset_y);
                $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_AUTO);
                $textRun = $shape->createTextRun($the_block['value']);
                $textRun->getFont()
                    ->setSize(22)
                    ->setName(UtilsPpt::FONT_WORK_SANS)
                    ->setColor(new Color(UtilsPpt::COLOR_BLACK));
            }

            if ($the_block['percent_value'] == "NEW") {
                $the_offsetX = $the_offsetX - 5;
            }
            // block info 1 arrow
            if ($the_block['percent_icon'] !== "") {
                $shape = $elements['current_slide']->createDrawingShape();
                $shape->setName('')
                    ->setDescription('')
                    ->setPath(__DIR__ . '/../../web/ppt/' . $the_block['percent_icon'])
                    ->setWidthAndHeight(11, 11)
                    ->setOffsetX($the_offsetX + 11)
                    ->setOffsetY($icon_offset_y);
                $shape->setResizeProportional(true);
            }
            // block info 1 percent
            if ($the_block['percent_value'] !== "") {
                $percent_width = 200;
                $shape = $elements['current_slide']->createRichTextShape()
                    ->setHeight(20)
                    ->setWidth($percent_width)
                    ->setOffsetX($the_offsetX + 15)
                    ->setOffsetY($percent_offset_y);
                $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_AUTO);
                $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $textRun = $shape->createTextRun($the_block['percent_value']);
                $textRun->getFont()
                    ->setSize(14)
                    ->setName(UtilsPpt::FONT_WORK_SANS)
                    ->setColor(new Color($the_block['percent_color']));
                /*
                if ($versus_date !== "" && $the_block['percent_value'] !== "NEW") {
                    $textRun = $shape->createTextRun(" vs " . $versus_date);
                    $textRun->getFont()
                        ->setSize(14)
                        ->setName(UtilsPpt::FONT_WORK_SANS)
                        ->setColor(new Color(UtilsPpt::COLOR_b5b5b5));
                }
                */
            }
        }
        return $elements;
    }


    /**
     * Methode reprise sur drupal
     *
     * @param $type
     * @param $innovation
     * @return array|string
     */
    public static function getExportIconByType($type, $innovation)
    {
        $is_hq = '';
        $default = "icons/icon-empty.png";
        if ($type == 'stage') {
            $current_stage = $innovation['current_stage'];
            switch ($current_stage) {
                case "discover":
                    return "icons/stage/stage-discover.png";
                case "ideate":
                    return "icons/stage/stage-ideate.png";
                case "experiment":
                    return "icons/stage/stage-experiment.png";
                case "incubate":
                    return "icons/stage/stage-incubate.png";
                case "scale_up":
                    return "icons/stage/stage-scale_up.png";
                case "permanent_range":
                    return "icons/stage/stage-permanent_range.png";
                case "discontinued":
                    return "icons/stage/stage-discontinued.png";
                default:
                    return "icons/icon-empty.png";
            }
        } elseif ($type == 'classification') {
            $classification = $innovation['classification_type'];
            switch ($classification) {
                case 'Product':
                    return 'icons/classification/icon-product.png';
                case 'Service':
                    return 'icons/classification/icon-service.png';
                default:
                    return "icons/icon-empty.png";
            }
        } elseif ($type == 'type') {
            $the_type = $innovation['innovation_type'];
            switch ($the_type) {
                case 'Stretch':
                    return 'icons/type/icon-stretch.png';
                case 'Incremental':
                    return 'icons/type/icon-incremental.png';
                case 'Breakthrough':
                    return 'icons/type/icon-breakthrough.png';
                default:
                    return "icons/icon-empty.png";
            }
        } elseif ($type == 'business_drivers') {
            $classification = $innovation['business_drivers'];
            switch ($classification) {
                case 'RTD / RTS':
                    return 'icons/business_drivers/icon-rtd_rts.png';
                case 'No & Low Alcohol':
                    return 'icons/business_drivers/icon-no_low_alcohol.png';
                case 'Specialty':
                    return 'icons/business_drivers/icon-specialty.png';
                default:
                    return "icons/icon-empty-round.png";
            }
        } elseif ($type == 'growth_strategy') {
            $growth_strategy = $innovation['growth_strategy'];
            if (!$is_hq && $growth_strategy != "Big Bet") {
                $growth_strategy = 'Contributor';
            }
            switch ($growth_strategy) {
                case 'Contributor':
                    return array(
                        'icon' => 'icons/growth_strategy/icon-contributor.png',
                        'libelle' => "Contributor"
                    );
                case 'Big Bet':
                    return array(
                        'icon' => 'icons/growth_strategy/icon-big_bet.png',
                        'libelle' => "Big Bet"
                    );
                case 'Top contributor':
                    return array(
                        'icon' => 'icons/growth_strategy/icon-top_contributor.png',
                        'libelle' => "Top contributor"
                    );
                case 'Negative CAAP':
                    return array(
                        'icon' => 'icons/growth_strategy/icon-negative_caap.png',
                        'libelle' => "Negative CAAP"
                    );
                case 'High investment':
                    return array(
                        'icon' => 'icons/growth_strategy/icon-high_investment.png',
                        'libelle' => "High investment"
                    );
                default:
                    return array(
                        'icon' => 'icons/growth_strategy/icon-contributor.png',
                        'libelle' => "Contributor"
                    );
            }
        }else if ($type == 'consumer_opportunity') {
            $consumer_opportunity = $innovation['consumer_opportunity'];
            switch (intval($consumer_opportunity)) {
                case 1:
                    return 'icons/coop/icon-human_authenticity.png';
                case 2:
                    return 'icons/coop/icon-easy_at_home_everywhere.png';
                case 3:
                    return 'icons/coop/icon-shaking_the_codes.png';
                case 4:
                    return 'icons/coop/icon-power_to_consumers.png';
                case 5:
                    return 'icons/coop/icon-feminine_identity.png';
                case 6:
                    return 'icons/coop/icon-doing_good.png';
                case 7:
                    return 'icons/coop/icon-better_for_me.png';
                case 8:
                    return 'icons/coop/icon-tactical_innovation.png';
                default:
                    return null;
            }
        }
        return $default;
    }

    /**
     * Display nb markets
     *
     * @param $markets_in_array
     * @param bool $only_nb
     * @return string
     */
    public static function displayNbMarkets($markets_in_array, $only_nb = false)
    {
        $nb_markets = count($markets_in_array);
        if ($only_nb || $nb_markets > 2) {
            $market_word = ($nb_markets > 1) ? 'markets' : 'market';
            return $nb_markets . ' ' . $market_word;
        } elseif ($nb_markets == 2) {
            $response = UtilsCountry::getCountryNameByCode($markets_in_array[0]) . ' and ' . UtilsCountry::getCountryNameByCode($markets_in_array[1]);
        } elseif ($nb_markets == 1) {
            $response = UtilsCountry::getCountryNameByCode($markets_in_array[0]);
        } else {
            $response = implode(", ", $markets_in_array);
        }
        return $response;
    }


    /**
     * Methode reprise sur drupal
     *
     * @param $fake_libelle
     * @return mixed|string
     */
    public static function getLibelleStageByFakeLibelle($fake_libelle)
    {
        $stages = array(
            '' => 'Discover',
            'discover' => 'Discover',
            'ideate' => 'Ideate',
            'experiment' => 'Experiment',
            'incubate' => 'Incubate',
            'scale_up' => 'Scale up',
            'discontinued' => 'Discontinued',
            'permanent_range' => 'Permanent range',
        );
        if (!array_key_exists($fake_libelle, $stages)) {
            return 'Unknown stage';
        }
        return $stages[$fake_libelle];
    }

    /**
     * getLibelleBudgetCurrentYear
     *
     * @param null $date
     * @return string
     */
    public static function getLibelleBudgetCurrentYear($date = null)
    {

        $the_date = new \DateTime($date);
        $year = intval($the_date->format('y'));
        $month = intval($the_date->format('n'));
        if ($month < 4) {
            return 'A' . ($year - 1);
        } elseif ($month < 7) {
            return 'A' . ($year - 1);
        } elseif ($month < 10) {
            return 'A' . $year;
        } else {
            return 'A' . $year;
        }
    }

    /**
     * getLibelleFY
     * @param the_date
     * @return {string}
     */
    public static function getLibelleFY($date = null) {
        $the_date = new \DateTime($date);
        $year = intval($the_date->format('y'));
        $month = intval($the_date->format('n'));
        $future_year = $year + 1;
        if ($month < 4) {
            return 'FY' . $year;
        } else if ($month < 7) {
            return 'FY' . $future_year;
        } else if ($month < 10) {
            return 'FY' . $future_year;
        } else {
            return 'FY' . $future_year;
        }
    }

    /**
     * getFinancialDateLibelle
     *
     * @param null $date
     * @return string
     *
     */
    public static function getFinancialDateLibelle($date = null)
    {
        $the_date = new \DateTime($date);
        $year = intval($the_date->format('y'));
        $month = intval($the_date->format('n'));
        $futur_year = $year + 1;

        if ($month < 4) {
            return 'LE2 ' . $year;
        } elseif ($month < 7) {
            return 'LE3 ' . $year;
        } elseif ($month < 10) {
            return 'B' . $futur_year;
        } else {
            return 'LE1 ' . $futur_year;
        }
    }

    /**
     * Methode reprise sur drupal
     *
     * @param $elements
     * @param null $entity_id
     * @param null $el_date
     * @param null $title_entity
     * @return mixed
     */
    public static function addEntityQualiFirstPage($elements, $entity_id = null, $el_date = null, $title_entity = null)
    {
        $elements['nb_slide']++;
        $elements['current_slide'] = $elements['ppt']->createSlide();
        // Ajout du background
        $shape = $elements['current_slide']->createDrawingShape();
        $shape->setName('')
            ->setDescription('')
            ->setPath(__DIR__ . '/../../web/ppt/couvs/couv-doc.png')
            ->setHeight(1080)
            ->setOffsetX(0)
            ->setOffsetY(0);
        $shape->getShadow()->setVisible(false)
            ->setDirection(0)
            ->setDistance(0);


        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(300)
            ->setWidth(800)
            ->setOffsetX(100)
            ->setOffsetY(380);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $text = "The Innovation Book";
        $textRun = $shape->createTextRun($text);
        $textRun->getFont()
            ->setSize(50)
            ->setBold(true)
            ->setName(UtilsPpt::FONT_MONTSERRAT)
            ->setColor(new Color(UtilsPpt::COLOR_WHITE));

        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(100)
            ->setWidth(600)
            ->setOffsetX(100)
            ->setOffsetY(480);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $text = self::getFinancialDateLibelle($el_date);
        $textRun = $shape->createTextRun($text);
        $textRun->getFont()
            ->setSize(40)
            ->setName(UtilsPpt::FONT_MONTSERRAT)
            ->setColor(new Color(UtilsPpt::COLOR_WHITE));


        if ($title_entity) {
            $shape = $elements['current_slide']->createRichTextShape()
                ->setHeight(100)
                ->setWidth(1000)
                ->setOffsetX(100)
                ->setOffsetY(580);
            $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $text = $title_entity;
            $textRun = $shape->createTextRun($text);
            $textRun->getFont()
                ->setSize(30)
                ->setName(UtilsPpt::FONT_MONTSERRAT)
                ->setColor(new Color(UtilsPpt::COLOR_WHITE));
        }

        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(100)
            ->setWidth(1000)
            ->setOffsetX(100)
            ->setOffsetY(700);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $text = date('F j Y');
        $textRun = $shape->createTextRun($text);
        $textRun->getFont()
            ->setSize(20)
            ->setName(UtilsPpt::FONT_MONTSERRAT)
            ->setColor(new Color(UtilsPpt::COLOR_WHITE));

        /*
        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(50)
            ->setWidth(1920)
            ->setOffsetX(0)
            ->setOffsetY(1032);
        $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_AUTO);
        //$shape->getFill()->setFillType(Fill::FILL_SOLID)->setRotation(90)->setStartColor(new Color(UtilsPpt::COLOR_CA1A3C))->setEndColor(new Color(UtilsPpt::COLOR_C9193C));
        $textRun = $shape->createTextRun("   IMPORTANT NOTE: This document is highly confidential. This copy should not be further communicated to any whom.");
        $textRun->getFont()
            ->setSize(20)
            ->setName('Century Gothic')
            ->setColor(new Color(UtilsPpt::COLOR_WHITE));
        */

        return $elements;
    }

    /**
     * Methode reprise sur drupal
     *
     * @param $products
     * @param $el_date
     * @param bool $sort_by_entity
     * @param bool $is_classic_ppt
     * @param bool $with_quanti
     * @param bool $force_quanti
     * @param $settings
     * @param $em
     * @return array
     */
    public static function getInfosForInnovationFormPPT($products, $el_date, $sort_by_entity = false, $is_classic_ppt = true, $with_quanti = false, $force_quanti = false, $settings, $em)
    {
        $retour = array(
            'total' => array(
                'innovations' => array(),
                'overview' => self::getOverviewPptInfos($products, $el_date, $settings, $em)
            ),
            'by_entity' => array(),
        );
        if ($sort_by_entity) {
            $order_entity = array(
                72 => "Chivas Brothers (CBL)",
                76 => "Irish Distillers (IDL)",
                97 => "The Absolut Company (TAC)",
                79 => "MMPJ",
                74 => "HCI (Havana)",
                81 => "Pernod Ricard Winemakers",
                83 => "PR Australia",
                75 => "House of Tequila",
                95 => "Ramazzotti",
                98 => "Yerevan Brandy Company",
                86 => "PR Finland",
                85 => "PR Espana",
                90 => "PR Mexico",
                91 => "PR Polska",
                92 => "PR South Africa",
                84 => "PR Brasil",
                82 => "PR Argentina",
                77 => "Jan Becher",
                88 => "PR India",
                89 => "PR Korea",
                87 => "PR Greater China",
                93 => "PR USA",
                73 => "Corby dist Ltd",
                80 => "Pernod",
                96 => "Ricard",
                71 => "BIG",
                78 => "KFUND",
                94 => "PR HQ",
            );
            foreach ($order_entity as $key => $value) {
                $retour['by_entity'][$key] = array(
                    'title' => $value,
                    'gid' => $key,
                    'data' => array(
                        'total' => array(
                            'innovations' => array(),
                            'overview' => array(),
                        )
                    )
                );
            }
        }

        $availableTypes = array('discover', 'ideate', 'scale_up', 'incubate', 'experiment');
        $force_refresh = (count($products) < 50);
        foreach ($products as $product) {
            $type = $product['current_stage'];
            if (in_array($type, $availableTypes)) {
                $productArray = $product;
                if ($is_classic_ppt) {
                    $productArray = $product;
                } else {
                    $productArray = self::getProperInnovationForQualiQuanti($product, $settings, $with_quanti, $force_quanti, $force_refresh);
                }

                $retour['total']['innovations'][] = $productArray;
                if ($sort_by_entity) {
                    if ($product['entity']) {
                        if (!array_key_exists($product['entity']['id'], $retour['by_entity'])) {
                            $retour['by_entity'][$product['entity']['id']] = array(
                                'title' => $product['entity']['title'],
                                'id' => $product['entity']['id'],
                                'data' => array(
                                    'total' => array(
                                        'innovations' => array(),
                                    )
                                )
                            );
                        }
                        $retour['by_entity'][$product['entity']['id']]['data']['total']['innovations'][] = $productArray;
                    }
                }
            }
        }

        return $retour;
    }

    /**
     * Methode reprise sur drupal
     *
     * @param $elements
     * @param $data
     * @param null $el_date
     * @param bool $is_hq
     * @return mixed
     */
    public static function addGuardPage($elements, $data, $el_date = null, $is_hq = false)
    {
        $elements['nb_slide']++;
        $elements['current_slide'] = $elements['ppt']->createSlide();
        // Ajout du background
        $shape = $elements['current_slide']->createDrawingShape();
        $shape->setName('')
            ->setDescription('')
            ->setPath(__DIR__ . '/../../web/ppt/couvs/couv-doc.png')
            ->setHeight(1080)
            ->setOffsetX(0)
            ->setOffsetY(0);
        $shape->getShadow()->setVisible(false)
            ->setDirection(0)
            ->setDistance(0);


        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(300)
            ->setWidth(800)
            ->setOffsetX(100)
            ->setOffsetY(380);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)
            ->setVertical(Alignment::VERTICAL_CENTER);
        $text = "Scale-up" . PHP_EOL . "innovation performance";
        $textRun = $shape->createTextRun($text);
        $textRun->getFont()
            ->setSize(50)
            ->setBold(true)
            ->setName(UtilsPpt::FONT_MONTSERRAT)
            ->setColor(new Color(UtilsPpt::COLOR_WHITE));

        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(100)
            ->setWidth(600)
            ->setOffsetX(100)
            ->setOffsetY(660);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $text = self::getFinancialDateLibelle($el_date);
        $textRun = $shape->createTextRun($text);
        $textRun->getFont()
            ->setSize(40)
            ->setName(UtilsPpt::FONT_MONTSERRAT)
            ->setColor(new Color(UtilsPpt::COLOR_WHITE));


        return $elements;
    }

    /**
     * Methode reprise sur drupal
     *
     * @param $el_date
     * @param $datas
     * @return array
     */
    public static function getInnovationsForExportPerformancePpt($el_date, $products, $is_admin)
    {
        $big_bet = array();
        $top = array();
        $worst = array();
        $high = array();
        $ret = array(
            'big_bet' => array(
                'innovations' => array(),
                'caap' => 0,
                'title' => 'Big Bets Performance',
                'subtitle' => ' – sorted by cumulative CAAP since launch (up to A15)',
                'logo' => 'icons/portfolio/big_bet.png',
                'libelle' => 'BIG BETS',
            ),
            'top' => array(
                'innovations' => array(),
                'caap' => 0,
                'title' => 'Top Contributors Performance',
                'subtitle' => ' – sorted by cumulative CAAP since launch (up to A15)',
                'logo' => 'icons/portfolio/top_contributor.png',
                'libelle' => 'TOP CONTRIBUTORS',
            ),
            'worst' => array(
                'innovations' => array(),
                'caap' => 0,
                'title' => 'Negative CAAP Performance',
                'subtitle' => ' – sorted by cumulative CAAP since launch (up to A15)',
                'logo' => 'icons/portfolio/negative_caap.png',
                'libelle' => 'NEGATIVE CAAP',
            ),
            'high' => array(
                'innovations' => array(),
                'caap' => 0,
                'title' => 'High Investment Performance',
                'subtitle' => ' – sorted by cumulative A&P since launch (up to A15)',
                'logo' => 'icons/portfolio/high_investment.png',
                'libelle' => 'HIGH INVESTMENT',
            ),
            'date_libelle' => self::getFinancialDateLibelle($el_date)
        );
        $ids = array();


        // First We order by cumul CAAP
        $sortArray = array();
        foreach ($products as $proper_product) {
            $sortArray['financial']['data']['cumul']['caap'][] = $proper_product['financial']['data']['cumul']['caap'];
        }
        array_multisort($sortArray['financial']['data']['cumul']['caap'], SORT_DESC, $products);


        // First, get big bets by limit
        $limit = ($is_admin) ? false : 5;
        $nb = 0;
        foreach ($products as $proper_product) {
            if (
                !in_array($proper_product['id'], $ids) &&
                $proper_product['growth_strategy'] == 'Big Bet' &&
                (!$limit || ($limit && $nb < $limit))
            ) {
                $big_bet[] = $proper_product;
                $ids[] = $proper_product['id'];
                $nb++;
            }
        }

        // Then, get Top contributors by limit
        $limit = ($is_admin) ? 10 : 5;
        $nb = 0;
        foreach ($products as $proper_product) {
            if (!in_array($proper_product['id'], $ids) &&
                $proper_product['growth_strategy'] == 'Top contributor' &&
                $nb < $limit
            ) {
                $top[] = $proper_product;
                $ids[] = $proper_product['id'];
                $nb++;
            }
        }

        // Then, get Negative CAAP
        // order by ASC
        array_multisort($sortArray['financial']['data']['cumul']['caap'], SORT_ASC, $products);
        $limit = ($is_admin) ? 10 : 5;
        $nb = 0;
        foreach ($products as $proper_product) {
            if (!in_array($proper_product['id'], $ids) &&
                $proper_product['growth_strategy'] == 'Negative CAAP' &&
                $proper_product['financial']['data']['cumul']['caap'] < 0 &&
                $nb < $limit
            ) {
                $worst[] = $proper_product;
                $ids[] = $proper_product['id'];
                $nb++;
            }
        }

        // Then We order by cumul A&P
        $sortArray = array();
        foreach ($products as $proper_product) {
            $sortArray['financial']['data']['cumul']['total_ap'][] = $proper_product['financial']['data']['cumul']['total_ap'];
        }
        // Then, get High investment
        // order by DESC
        $limit = ($is_admin) ? 10 : 5;
        $nb = 0;
        array_multisort($sortArray['financial']['data']['cumul']['total_ap'], SORT_ASC, $products);
        foreach ($products as $proper_product) {
            if (!in_array($proper_product['id'], $ids) &&
                $proper_product['growth_strategy'] == 'High investment' &&
                $nb < $limit
            ) {
                $high[] = $proper_product;
                $ids[] = $proper_product['id'];
                $nb++;
            }
        }

        // Je transforme les tableaux pour ne garder que le concret
        $caap = 0;
        foreach ($big_bet as $product) {
            $productArray = self::getProductForEntityPerformanceReview($product);
            $caap += $productArray['caap'];
            $ret['big_bet']['innovations'][] = $productArray;
        }
        $ret['big_bet']['caap'] = $caap;

        $caap = 0;
        foreach ($top as $product) {
            $productArray = self::getProductForEntityPerformanceReview($product);
            $caap += $productArray['caap'];
            $ret['top']['innovations'][] = $productArray;
        }

        $ret['top']['caap'] = $caap;

        $caap = 0;
        foreach ($worst as $product) {
            $productArray = self::getProductForEntityPerformanceReview($product);
            $caap += $productArray['caap'];
            $ret['worst']['innovations'][] = $productArray;
        }
        $ret['worst']['caap'] = $caap;

        $caap = 0;
        foreach ($high as $product) {
            $productArray = self::getProductForEntityPerformanceReview($product);
            $caap += $productArray['caap'];
            $ret['high']['innovations'][] = $productArray;
        }
        $ret['high']['caap'] = $caap;


        return $ret;
    }

    public static function getProductForEntityPerformanceReview($product)
    {
        return array(
            'id' => $product['id'],
            'title' => $product['title'],
            'classification_type' => $product['classification_type'],
            'growth_strategy' => $product['growth_strategy'],
            'packshot' => (!$product['performance_picture'] ? null : $product['performance_picture']),
            'years_since_launch' => $product['years_since_launch'],
            'growth_model' => $product['growth_model'],
            'volume' => self::reformatNumber($product['ppt']['volume']),
            'Evol_VOL_vs' => $product['ppt']['Evol_VOL_vs'],
            'ap_vs_ns' => $product['ppt']['ap_vs_ns'],
            'caap' => $product['ppt']['caap'],
            'cumul_caap_since_a15' => $product['financial']['data']['cumul']['caap'],
            'cumul_ap_since_a15' => $product['financial']['data']['cumul']['total_ap']
        );
    }

    /**
     * Methode reprise sur drupal
     *
     * @param $elements
     * @param $data
     * @param null $el_date
     * @param bool $is_hq
     * @return mixed
     */
    public static function addTopPriorityInnovationsPage($elements, $data, $el_date = null, $is_hq = false)
    {
        $elements['nb_slide']++;
        $elements['current_slide'] = $elements['ppt']->createSlide();
        // Ajout du background
        $shape = $elements['current_slide']->createDrawingShape();
        $shape->setName('')
            ->setDescription('')
            ->setPath(__DIR__ . '/../../web/ppt/couvs/entity_performance_review/output-guard.png')
            ->setHeight(1080)
            ->setOffsetX(0)
            ->setOffsetY(0);
        $shape->getShadow()->setVisible(false)
            ->setDirection(0)
            ->setDistance(0);


        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(80)
            ->setWidth(600)
            ->setOffsetX(40)
            ->setOffsetY(20);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $text = "Top Priority Innovations";
        $textRun = $shape->createTextRun($text);
        $textRun->getFont()
            ->setSize(24)
            ->setName(UtilsPpt::FONT_MONTSERRAT)
            ->setBold(true)
            ->setColor(new Color(UtilsPpt::COLOR_BLACK));

        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(80)
            ->setWidth(600)
            ->setOffsetX(40)
            ->setOffsetY(110);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $text = "Based on " . $data['date_libelle'];
        $textRun = $shape->createTextRun($text);
        $textRun->getFont()
            ->setSize(18)
            ->setName(UtilsPpt::FONT_WORK_SANS)
            ->setColor(new Color(UtilsPpt::COLOR_BLACK));

        // LOGO
        $shape = $elements['current_slide']->createDrawingShape();
        $shape->setName('')
            ->setDescription('')
            ->setPath(__DIR__ . '/../../web/ppt/icons/logo-pr.png')
            ->setHeight(180)
            ->setOffsetX(678)
            ->setOffsetY(140);
        $shape->getShadow()->setVisible(false)
            ->setDirection(0)
            ->setDistance(0);


        $blocks = array(
            array(
                'logo' => 'big_bet.png',
                'libelle' => 'BIG BETS',
                'under-libelle' => false,
                'type' => 'big_bet'
            ),
            array(
                'logo' => 'top_contributor.png',
                'libelle' => 'TOP CONTRIBUTORS',
                'under-libelle' => 'Cumul CAAP since 2015',
                'type' => 'top'
            ),
            array(
                'logo' => 'negative_caap.png',
                'libelle' => 'NEGATIVE CAAP',
                'under-libelle' => 'Cumul CAAP since 2015',
                'type' => 'worst'
            ),
            array(
                'logo' => 'high_investment.png',
                'libelle' => 'HIGH INVESTMENT',
                'under-libelle' => 'Largest cumul A&P, excl. BB, Top contrib & Negative CAAP',
                'type' => 'high'
            )
        );
        $block_i = 0;
        $block_adder = 433;
        foreach ($blocks as $block) {
            $shape = $elements['current_slide']->createDrawingShape();
            $offsetX_logo = $block_i * $block_adder + 250;
            $shape->setName('')
                ->setDescription('')
                ->setPath(__DIR__ . '/../../web/ppt/icons/portfolio/' . $block['logo'])
                ->setHeight(120)
                ->setOffsetX($offsetX_logo)
                ->setOffsetY(400);
            $shape->getShadow()->setVisible(false)
                ->setDirection(0)
                ->setDistance(0);

            $offsetX_libelle = $block_i * $block_adder + 110;
            $shape = $elements['current_slide']->createRichTextShape()
                ->setHeight(40)
                ->setWidth(400)
                ->setOffsetX($offsetX_libelle)
                ->setOffsetY(530);
            $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $text = $block['libelle'];
            $textRun = $shape->createTextRun($text);
            $textRun->getFont()
                ->setSize(22)
                ->setName(UtilsPpt::FONT_MONTSERRAT)
                ->setColor(new Color(UtilsPpt::COLOR_BLACK));

            if ($block['under-libelle']) {
                $shape = $elements['current_slide']->createRichTextShape()
                    ->setHeight(60)
                    ->setWidth(400)
                    ->setOffsetX($offsetX_libelle)
                    ->setOffsetY(570);
                $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $text = $block['under-libelle'];
                $textRun = $shape->createTextRun($text);
                $textRun->getFont()
                    ->setSize(14)
                    ->setName(UtilsPpt::FONT_WORK_SANS)
                    ->setColor(new Color(UtilsPpt::COLOR_BLACK));
            }

            // LIST
            $innovations = $data[$block['type']]['innovations'];
            if (count($innovations) > 5) {
                $font_size = 14;
                $base_top = 620;
                $base_height = 25;
                $max_char = 32;
            } else {
                $font_size = 16;
                $base_top = 620;
                $base_height = 25;
                $max_char = 26;
            }
            $inno_i = 1;
            foreach ($innovations as $innovation) {
                $offsetY_inno = $base_top + $inno_i * $base_height;
                $offsetX_nb_inno = $block_i * $block_adder + 100;
                $shape = $elements['current_slide']->createRichTextShape()
                    ->setHeight($base_height)
                    ->setWidth(50)
                    ->setOffsetX($offsetX_nb_inno)
                    ->setOffsetY($offsetY_inno);
                $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $text = $inno_i . '.';
                $textRun = $shape->createTextRun($text);
                $textRun->getFont()
                    ->setSize($font_size)
                    ->setName(UtilsPpt::FONT_WORK_SANS)
                    ->setColor(new Color(UtilsPpt::COLOR_b5b5b5));

                $offsetX_title_inno = $offsetX_nb_inno + 50;
                $shape = $elements['current_slide']->createRichTextShape()
                    ->setHeight($base_height)
                    ->setWidth(370)
                    ->setOffsetX($offsetX_title_inno)
                    ->setOffsetY($offsetY_inno);
                $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $text = self::textResume($innovation['title'], $max_char, true);
                $textRun = $shape->createTextRun($text);
                $textRun->getFont()
                    ->setSize($font_size)
                    ->setName(UtilsPpt::FONT_WORK_SANS)
                    ->setColor(new Color(UtilsPpt::COLOR_BLACK));

                $inno_i++;
            }


            $block_i++;

        }

        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(30)
            ->setWidth(530)
            ->setOffsetX(0)
            ->setOffsetY(1050);
        $textRun = $shape->createTextRun(strtoupper("Pernod Ricard") . " STRICLTY CONFIDENTIAL / DO NOT SHARE");
        $textRun->getFont()
            ->setSize(11)
            ->setName(UtilsPpt::FONT_WORK_SANS)
            ->setColor(new Color(UtilsPpt::COLOR_WHITE));

        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(30)
            ->setWidth(530)
            ->setOffsetX(540)
            ->setOffsetY(1050);
        $textRun = $shape->createTextRun("Based on declarative data. Exported  from the " . "Innovation Hub" . ".");
        $textRun->getFont()
            ->setSize(11)
            ->setName(UtilsPpt::FONT_WORK_SANS)
            ->setColor(new Color(UtilsPpt::COLOR_d8d8d8));


        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(30)
            ->setWidth(300)
            ->setOffsetX(1670)
            ->setOffsetY(1050);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $text = date('M d, Y g:i A');
        $textRun = $shape->createTextRun($text);
        $textRun->getFont()
            ->setSize(11)
            ->setName(UtilsPpt::FONT_WORK_SANS)
            ->setColor(new Color(UtilsPpt::COLOR_e3eaf1));


        return $elements;
    }

    /**
     * Methode reprise sur drupal
     *
     * @param $elements
     * @param $data
     * @param $type
     * @param $innovations
     * @param null $el_date
     * @param bool $is_hq
     * @param $settings
     * @return mixed
     */
    public static function addPerformancePage($elements, $data, $type, $innovations, $el_date = null, $is_hq = false, $settings)
    {
        $elements['nb_slide']++;
        $elements['current_slide'] = $elements['ppt']->createSlide();

        $the_data = $data[$type];
        $nb_innovations = count($innovations);

        $couv = 'output-top-less-than-10.png';
        $margin = 200;
        $base_size = 395;
        if ($nb_innovations > 5) {
            $couv = 'output-top-10.png';
            $margin = self::getMargin($nb_innovations);
            $base_size = 320;
        }

        // Ajout du background
        $shape = $elements['current_slide']->createDrawingShape();
        $shape->setName('')
            ->setDescription('')
            ->setPath(__DIR__ . '/../../web/ppt/couvs/entity_performance_review/' . $couv)
            ->setHeight(1080)
            ->setOffsetX(0)
            ->setOffsetY(0);
        $shape->getShadow()->setVisible(false)
            ->setDirection(0)
            ->setDistance(0);


        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(80)
            ->setWidth(1600)
            ->setOffsetX(40)
            ->setOffsetY(20);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $text = $the_data['title'];
        $textRun = $shape->createTextRun($text);
        $textRun->getFont()
            ->setSize(24)
            ->setName(UtilsPpt::FONT_MONTSERRAT)
            ->setBold(true)
            ->setColor(new Color(UtilsPpt::COLOR_BLACK));
        $text = $the_data['subtitle'];
        $textRun = $shape->createTextRun($text);
        $textRun->getFont()
            ->setSize(20)
            ->setName(UtilsPpt::FONT_WORK_SANS)
            ->setColor(new Color(UtilsPpt::COLOR_BLACK));

        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(80)
            ->setWidth(600)
            ->setOffsetX(40)
            ->setOffsetY(110);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $el_caap = (abs($the_data['caap']) > 1000) ? round($the_data['caap'] / 1000) . "M€" : $the_data['caap'] . "k€";
        $text = "A portfolio with CAAP in " . $data['date_libelle'] . " of: " . $el_caap;
        $textRun = $shape->createTextRun($text);
        $textRun->getFont()
            ->setSize(18)
            ->setName(UtilsPpt::FONT_WORK_SANS)
            ->setColor(new Color(UtilsPpt::COLOR_BLACK));

        // LOGO
        $offset_x_logo = ($nb_innovations > 5) ? 105 : 140;
        $shape = $elements['current_slide']->createDrawingShape();
        $shape->setName('')
            ->setDescription('')
            ->setPath(__DIR__ . '/../../web/ppt/' . $the_data['logo'])
            ->setHeight(120)
            ->setOffsetX($offset_x_logo)
            ->setOffsetY(300);
        $shape->getShadow()->setVisible(false)
            ->setDirection(0)
            ->setDistance(0);

        $offsetX_libelle = ($nb_innovations > 5) ? 45 : 45;
        $width_libelle = ($nb_innovations > 5) ? 245 : 320;
        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(40)
            ->setWidth($width_libelle)
            ->setOffsetX($offsetX_libelle)
            ->setOffsetY(430);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $text = $the_data['libelle'];
        $textRun = $shape->createTextRun($text);
        $textRun->getFont()
            ->setSize(20)
            ->setName(UtilsPpt::FONT_WORK_SANS)
            ->setColor(new Color(UtilsPpt::COLOR_BLACK));

        $last_a = $settings->getLibelleLastA();
        $labels = array(
            array(
                'title' => 'Time in market',
                'color' => UtilsPpt::COLOR_005095,
                'offset_y' => 570,
            ),
            array(
                'title' => 'Volume (in ' . "k9Lcs" . ')',
                'color' => UtilsPpt::COLOR_005095,
                'offset_y' => 638,
            ),
            array(
                'title' => 'Evol VOL vs LY',
                'color' => UtilsPpt::COLOR_005095,
                'offset_y' => 735,
            ),
            array(
                'title' => 'A&P/NS',
                'color' => UtilsPpt::COLOR_005095,
                'offset_y' => 805,
            ),
            array(
                'title' => 'CAAP',
                'color' => UtilsPpt::COLOR_005095,
                'offset_y' => 900,
            ),
            array(
                'title' => 'Cumul CAAP since A15',
                'color' => UtilsPpt::COLOR_005095,
                'offset_y' => 968,
            ),
        );

        $offsetX_label = ($nb_innovations > 5) ? 45 : 45;
        $width_label = ($nb_innovations > 5) ? 245 : 320;
        foreach ($labels as $label) {
            $shape = $elements['current_slide']->createRichTextShape()
                ->setHeight(50)
                ->setWidth($width_label)
                ->setOffsetX($offsetX_label)
                ->setOffsetY($label['offset_y']);
            $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $text = $label['title'];
            $textRun = $shape->createTextRun($text);
            $textRun->getFont()
                ->setSize(14)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color($label['color']));
        }

        $inno_i = 0;
        $width_innovation = 120;
        $height_innovation = 192;
        foreach ($innovations as $innovation) {
            $offsetX_innovation = $inno_i * ($width_innovation + $margin) + $base_size;
            $innovation_is_a_service = Innovation::innovationArrayIsAService($innovation);

            if(!$innovation_is_a_service) {
                // Fast Growth / Slow build
                $text_growth_model = ($innovation['growth_model'] == 'fast_growth') ? 'Fast Growth' : 'Slow Build';
                $picto_growth_model = ($innovation['growth_model'] == 'fast_growth') ? 'icon-fast_growth-light.png' : 'icon-slow_build-light.png';
                $shape = $elements['current_slide']->createDrawingShape();
                $shape->setName('')
                    ->setDescription('')
                    ->setPath(__DIR__ . '/../../web/ppt/icons/growth_model/' . $picto_growth_model)
                    ->setWidth(36)
                    ->setOffsetX($offsetX_innovation + 42)
                    ->setOffsetY(230);
                $shape->getShadow()->setVisible(false)
                    ->setDirection(0)
                    ->setDistance(0);

                $shape = $elements['current_slide']->createRichTextShape()
                    ->setHeight(30)
                    ->setWidth($width_innovation + 10)
                    ->setOffsetX($offsetX_innovation - 5)
                    ->setOffsetY(270);
                $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $textRun = $shape->createTextRun($text_growth_model);
                $textRun->getFont()
                    ->setSize(13)
                    ->setName(UtilsPpt::FONT_WORK_SANS)
                    ->setColor(new Color(UtilsPpt::COLOR_BLACK));
            }

            // Packshot
            if ($innovation['packshot']) {
                $imageData = self::getBase64PictureDataFromUrl($innovation['packshot']);
                if ($imageData) {
                    $shape = new Base64();
                    $shape->setName('')
                        ->setDescription('')
                        ->setResizeProportional(false)
                        ->setData($imageData)
                        ->setWidth($width_innovation)
                        ->setHeight($height_innovation)
                        ->setOffsetX($offsetX_innovation)
                        ->setOffsetY(310);
                    $shape->getShadow()->setVisible(false)
                        ->setDirection(0)
                        ->setDistance(0);
                    $elements['current_slide']->addShape($shape);
                }
            } else {
                $shape = $elements['current_slide']->createDrawingShape();
                $shape->setName('')
                    ->setDescription('')
                    ->setPath(__DIR__ . '/../../web/ppt/default/default-bottle.png')
                    ->setWidth($width_innovation)
                    ->setOffsetX($offsetX_innovation)
                    ->setOffsetY(310);
                $shape->getShadow()->setVisible(false)
                    ->setDirection(0)
                    ->setDistance(0);
            }

            // Time in market
            $shape = $elements['current_slide']->createRichTextShape()
                ->setHeight(60)
                ->setWidth($width_innovation)
                ->setOffsetX($offsetX_innovation)
                ->setOffsetY(570);
            $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $text = 'Year ' . $innovation['years_since_launch'];
            $textRun = $shape->createTextRun($text);
            $textRun->getFont()
                ->setSize(18)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color(UtilsPpt::COLOR_BLACK));

            // VOLUME
            $shape = $elements['current_slide']->createRichTextShape()
                ->setHeight(60)
                ->setWidth($width_innovation)
                ->setOffsetX($offsetX_innovation)
                ->setOffsetY(638);
            $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $text = $innovation['volume'] . 'k';
            $textRun = $shape->createTextRun($text);
            $textRun->getFont()
                ->setSize(18)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color(UtilsPpt::COLOR_BLACK));

            // Evol VOL vs
            $shape = $elements['current_slide']->createRichTextShape()
                ->setHeight(60)
                ->setWidth($width_innovation)
                ->setOffsetX($offsetX_innovation)
                ->setOffsetY(735);
            $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $text = round($innovation['Evol_VOL_vs']) . '%';
            $textRun = $shape->createTextRun($text);
            $textRun->getFont()
                ->setSize(18)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color(UtilsPpt::COLOR_9b9b9b));

            // A&P/NS
            $shape = $elements['current_slide']->createRichTextShape()
                ->setHeight(60)
                ->setWidth($width_innovation)
                ->setOffsetX($offsetX_innovation)
                ->setOffsetY(805);
            $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $text = round($innovation['ap_vs_ns']) . '%';
            $textRun = $shape->createTextRun($text);
            $textRun->getFont()
                ->setSize(18)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color(UtilsPpt::COLOR_9b9b9b));

            // CAAP
            $shape = $elements['current_slide']->createRichTextShape()
                ->setHeight(60)
                ->setWidth($width_innovation)
                ->setOffsetX($offsetX_innovation)
                ->setOffsetY(900);
            $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $text = (abs($innovation['caap']) > 1000) ? self::reformatNumber(round(($innovation['caap'] / 1000))) . ' M€' : self::reformatNumber($innovation['caap']) . ' k€';
            $textRun = $shape->createTextRun($text);
            $textRun->getFont()
                ->setSize(18)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color(UtilsPpt::COLOR_BLACK));

            // Cumul CAAP since A15
            $shape = $elements['current_slide']->createRichTextShape()
                ->setHeight(60)
                ->setWidth($width_innovation)
                ->setOffsetX($offsetX_innovation)
                ->setOffsetY(968);
            $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $text = (abs($innovation['cumul_caap_since_a15']) > 1000) ? self::reformatNumber(round(($innovation['cumul_caap_since_a15'] / 1000))) . ' M€' : self::reformatNumber($innovation['cumul_caap_since_a15']) . ' k€';
            $textRun = $shape->createTextRun($text);
            $textRun->getFont()
                ->setSize(18)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color(UtilsPpt::COLOR_BLACK));


            $inno_i++;
        }


        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(30)
            ->setWidth(530)
            ->setOffsetX(0)
            ->setOffsetY(1050);
        $textRun = $shape->createTextRun(strtoupper("Pernod Ricard") . " STRICLTY CONFIDENTIAL / DO NOT SHARE");
        $textRun->getFont()
            ->setSize(11)
            ->setName(UtilsPpt::FONT_WORK_SANS)
            ->setColor(new Color(UtilsPpt::COLOR_WHITE));

        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(30)
            ->setWidth(530)
            ->setOffsetX(540)
            ->setOffsetY(1050);
        $textRun = $shape->createTextRun("Based on declarative data. Exported  from the " . "Innovation Hub" . ".");
        $textRun->getFont()
            ->setSize(11)
            ->setName(UtilsPpt::FONT_WORK_SANS)
            ->setColor(new Color(UtilsPpt::COLOR_d8d8d8));


        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(30)
            ->setWidth(300)
            ->setOffsetX(1670)
            ->setOffsetY(1050);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $text = date('M d, Y g:i A');
        $textRun = $shape->createTextRun($text);
        $textRun->getFont()
            ->setSize(11)
            ->setName(UtilsPpt::FONT_WORK_SANS)
            ->setColor(new Color(UtilsPpt::COLOR_e3eaf1));

        return $elements;
    }

    /**
     * Methode reprise sur drupal
     *
     * @param $elements
     * @param $data
     * @param $type
     * @param $innovations
     * @param null $el_date
     * @param bool $is_hq
     * @param $settings
     * @return mixed
     */
    public static function addPerformancePageHighInvestment($elements, $data, $type, $innovations, $el_date = null, $is_hq = false, $settings)
    {
        $elements['nb_slide']++;
        $elements['current_slide'] = $elements['ppt']->createSlide();

        $the_data = $data[$type];
        $nb_innovations = count($innovations);

        $couv = 'output-high-less-than-10.png';
        $margin = 200;
        $base_size = 425;
        if ($nb_innovations > 5) {
            $couv = 'output-high-10.png';
            $margin = self::getMargin($nb_innovations);
            $base_size = 320;
        }

        // Ajout du background
        $shape = $elements['current_slide']->createDrawingShape();
        $shape->setName('')
            ->setDescription('')
            ->setPath(__DIR__ . '/../../web/ppt/couvs/entity_performance_review/' . $couv)
            ->setHeight(1080)
            ->setOffsetX(0)
            ->setOffsetY(0);
        $shape->getShadow()->setVisible(false)
            ->setDirection(0)
            ->setDistance(0);


        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(80)
            ->setWidth(1600)
            ->setOffsetX(40)
            ->setOffsetY(20);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $text = $the_data['title'];
        $textRun = $shape->createTextRun($text);
        $textRun->getFont()
            ->setSize(24)
            ->setName(UtilsPpt::FONT_MONTSERRAT)
            ->setBold(true)
            ->setColor(new Color(UtilsPpt::COLOR_BLACK));
        $text = $the_data['subtitle'];
        $textRun = $shape->createTextRun($text);
        $textRun->getFont()
            ->setSize(20)
            ->setName(UtilsPpt::FONT_WORK_SANS)
            ->setColor(new Color(UtilsPpt::COLOR_BLACK));

        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(80)
            ->setWidth(600)
            ->setOffsetX(40)
            ->setOffsetY(110);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $el_caap = (abs($the_data['caap']) > 1000) ? round($the_data['caap'] / 1000) . "M€" : $the_data['caap'] . "k€";
        $text = "A portfolio with CAAP in " . $data['date_libelle'] . " of: " . $el_caap;
        $textRun = $shape->createTextRun($text);
        $textRun->getFont()
            ->setSize(18)
            ->setName(UtilsPpt::FONT_WORK_SANS)
            ->setColor(new Color(UtilsPpt::COLOR_BLACK));
        // LOGO
        $offset_x_logo = ($nb_innovations > 5) ? 105 : 140;
        $shape = $elements['current_slide']->createDrawingShape();
        $shape->setName('')
            ->setDescription('')
            ->setPath(__DIR__ . '/../../web/ppt/' . $the_data['logo'])
            ->setHeight(120)
            ->setOffsetX($offset_x_logo)
            ->setOffsetY(300);
        $shape->getShadow()->setVisible(false)
            ->setDirection(0)
            ->setDistance(0);

        $offsetX_libelle = ($nb_innovations > 5) ? 45 : 45;
        $width_libelle = ($nb_innovations > 5) ? 245 : 320;
        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(40)
            ->setWidth($width_libelle)
            ->setOffsetX($offsetX_libelle)
            ->setOffsetY(430);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $text = $the_data['libelle'];
        $textRun = $shape->createTextRun($text);
        $textRun->getFont()
            ->setSize(20)
            ->setName(UtilsPpt::FONT_WORK_SANS)
            ->setColor(new Color(UtilsPpt::COLOR_BLACK));

        $last_a = $settings->getLibelleLastA();
        $labels = array(
            array(
                'title' => 'Time in market',
                'color' => UtilsPpt::COLOR_005095,
                'offset_y' => 570,
            ),
            array(
                'title' => 'Volume (in ' . "k9Lcs" . ')',
                'color' => UtilsPpt::COLOR_005095,
                'offset_y' => 628,
            ),
            array(
                'title' => 'Evol VOL vs LY',
                'color' => UtilsPpt::COLOR_005095,
                'offset_y' => 714,
            ),
            array(
                'title' => 'A&P/NS',
                'color' => UtilsPpt::COLOR_005095,
                'offset_y' => 773,
            ),
            array(
                'title' => 'CAAP',
                'color' => UtilsPpt::COLOR_005095,
                'offset_y' => 862,
            ),
            array(
                'title' => 'Cumul CAAP since A15',
                'color' => UtilsPpt::COLOR_005095,
                'offset_y' => 918,
            ),
            array(
                'title' => 'Cumul A&P since A15',
                'color' => UtilsPpt::COLOR_005095,
                'offset_y' => 980,
            ),
        );

        $offsetX_label = ($nb_innovations > 5) ? 45 : 45;
        $width_label = ($nb_innovations > 5) ? 245 : 320;
        foreach ($labels as $label) {
            $shape = $elements['current_slide']->createRichTextShape()
                ->setHeight(50)
                ->setWidth($width_label)
                ->setOffsetX($offsetX_label)
                ->setOffsetY($label['offset_y']);
            $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $text = $label['title'];
            $textRun = $shape->createTextRun($text);
            $textRun->getFont()
                ->setSize(14)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color($label['color']));
        }

        $inno_i = 0;
        $width_innovation = 120;
        $height_innovation = 192;
        foreach ($innovations as $innovation) {
            $offsetX_innovation = $inno_i * ($width_innovation + $margin) + $base_size;

            // Fast Growth / Slow build
            $text_growth_model = ($innovation['growth_model'] ==  'fast_growth') ? 'Fast Growth' : 'Slow Build';
            $picto_growth_model = ($innovation['growth_model'] == 'fast_growth') ? 'icon-fast_growth-light.png' : 'icon-slow_build-light.png';
            $shape = $elements['current_slide']->createDrawingShape();
            $shape->setName('')
                ->setDescription('')
                ->setPath(__DIR__ . '/../../web/ppt/icons/growth_model/' . $picto_growth_model)
                ->setWidth(36)
                ->setOffsetX($offsetX_innovation + 42)
                ->setOffsetY(230);
            $shape->getShadow()->setVisible(false)
                ->setDirection(0)
                ->setDistance(0);

            $shape = $elements['current_slide']->createRichTextShape()
                ->setHeight(30)
                ->setWidth($width_innovation + 10)
                ->setOffsetX($offsetX_innovation - 5)
                ->setOffsetY(270);
            $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $textRun = $shape->createTextRun($text_growth_model);
            $textRun->getFont()
                ->setSize(13)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color(UtilsPpt::COLOR_BLACK));


            // Packshot
            if ($innovation['packshot']) {
                $imageData = self::getBase64PictureDataFromUrl($innovation['packshot']);
                if ($imageData) {
                    $shape = new Base64();
                    $shape->setName('')
                        ->setDescription('')
                        ->setResizeProportional(false)
                        ->setData($imageData)
                        ->setWidth($width_innovation)
                        ->setHeight($height_innovation)
                        ->setOffsetX($offsetX_innovation)
                        ->setOffsetY(310);
                    $shape->getShadow()->setVisible(false)
                        ->setDirection(0)
                        ->setDistance(0);
                    $elements['current_slide']->addShape($shape);
                }
            } else {
                $shape = $elements['current_slide']->createDrawingShape();
                $shape->setName('')
                    ->setDescription('')
                    ->setPath(__DIR__ . '/../../web/ppt/default/default-bottle.png')
                    ->setWidth($width_innovation)
                    ->setOffsetX($offsetX_innovation)
                    ->setOffsetY(310);
                $shape->getShadow()->setVisible(false)
                    ->setDirection(0)
                    ->setDistance(0);
            }

            // Time in market
            $shape = $elements['current_slide']->createRichTextShape()
                ->setHeight(50)
                ->setWidth($width_innovation)
                ->setOffsetX($offsetX_innovation)
                ->setOffsetY(560);
            $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $text = 'Year ' . $innovation['years_since_launch'];
            $textRun = $shape->createTextRun($text);
            $textRun->getFont()
                ->setSize(18)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color(UtilsPpt::COLOR_BLACK));

            // VOLUME
            $shape = $elements['current_slide']->createRichTextShape()
                ->setHeight(50)
                ->setWidth($width_innovation)
                ->setOffsetX($offsetX_innovation)
                ->setOffsetY(623);
            $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $text = $innovation['volume'] . 'k';
            $textRun = $shape->createTextRun($text);
            $textRun->getFont()
                ->setSize(18)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color(UtilsPpt::COLOR_BLACK));

            // Evol VOL vs LY
            $shape = $elements['current_slide']->createRichTextShape()
                ->setHeight(50)
                ->setWidth($width_innovation)
                ->setOffsetX($offsetX_innovation)
                ->setOffsetY(708);
            $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $text = round($innovation['Evol_VOL_vs']) . '%';
            $textRun = $shape->createTextRun($text);
            $textRun->getFont()
                ->setSize(18)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color(UtilsPpt::COLOR_BLACK));

            // A&P/NS
            $shape = $elements['current_slide']->createRichTextShape()
                ->setHeight(50)
                ->setWidth($width_innovation)
                ->setOffsetX($offsetX_innovation)
                ->setOffsetY(768);
            $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $text = round($innovation['ap_vs_ns']) . '%';
            $textRun = $shape->createTextRun($text);
            $textRun->getFont()
                ->setSize(18)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color(UtilsPpt::COLOR_BLACK));

            // CAAP
            $shape = $elements['current_slide']->createRichTextShape()
                ->setHeight(50)
                ->setWidth($width_innovation)
                ->setOffsetX($offsetX_innovation)
                ->setOffsetY(857);
            $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $text = (abs($innovation['caap']) > 1000) ? self::reformatNumber(round(($innovation['caap'] / 1000))) . ' M€' : self::reformatNumber($innovation['caap']) . ' k€';
            $textRun = $shape->createTextRun($text);
            $textRun->getFont()
                ->setSize(18)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color(UtilsPpt::COLOR_BLACK));

            // Cumul CAAP since A15
            $shape = $elements['current_slide']->createRichTextShape()
                ->setHeight(50)
                ->setWidth($width_innovation)
                ->setOffsetX($offsetX_innovation)
                ->setOffsetY(916);
            $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $text = (abs($innovation['cumul_caap_since_a15']) > 1000) ? self::reformatNumber(round(($innovation['cumul_caap_since_a15'] / 1000))) . ' M€' : self::reformatNumber($innovation['cumul_caap_since_a15']) . ' k€';
            $textRun = $shape->createTextRun($text);
            $textRun->getFont()
                ->setSize(18)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color(UtilsPpt::COLOR_BLACK));

            // Cumul A&P since A15
            $shape = $elements['current_slide']->createRichTextShape()
                ->setHeight(50)
                ->setWidth($width_innovation)
                ->setOffsetX($offsetX_innovation)
                ->setOffsetY(977);
            $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $text = (abs($innovation['cumul_ap_since_a15']) > 1000) ? self::reformatNumber(round(($innovation['cumul_ap_since_a15'] / 1000))) . ' M€' : self::reformatNumber($innovation['cumul_ap_since_a15']) . ' k€';
            $textRun = $shape->createTextRun($text);
            $textRun->getFont()
                ->setSize(18)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color(UtilsPpt::COLOR_BLACK));


            $inno_i++;
        }


        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(30)
            ->setWidth(530)
            ->setOffsetX(0)
            ->setOffsetY(1050);
        $textRun = $shape->createTextRun(strtoupper("Pernod Ricard") . " STRICLTY CONFIDENTIAL / DO NOT SHARE");
        $textRun->getFont()
            ->setSize(11)
            ->setName(UtilsPpt::FONT_WORK_SANS)
            ->setColor(new Color(UtilsPpt::COLOR_WHITE));

        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(30)
            ->setWidth(530)
            ->setOffsetX(540)
            ->setOffsetY(1050);
        $textRun = $shape->createTextRun("Based on declarative data. Exported  from the " . "Innovation Hub" . ".");
        $textRun->getFont()
            ->setSize(11)
            ->setName(UtilsPpt::FONT_WORK_SANS)
            ->setColor(new Color(UtilsPpt::COLOR_d8d8d8));


        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(30)
            ->setWidth(300)
            ->setOffsetX(1670)
            ->setOffsetY(1050);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $text = date('M d, Y g:i A');
        $textRun = $shape->createTextRun($text);
        $textRun->getFont()
            ->setSize(11)
            ->setName(UtilsPpt::FONT_WORK_SANS)
            ->setColor(new Color(UtilsPpt::COLOR_e3eaf1));

        return $elements;
    }

    /**
     * Methode reprise sur drupal
     *
     * @param $nb
     * @return int
     */
    public static function getMargin($nb)
    {
        switch ($nb) {
            case 6:
                return 150;
            case 7:
                return 110;
            case 8:
                return 80;
            case 9:
                return 50;
            case 10:
                return 35;
        }
    }

    /**
     * Methode reprise sur drupal
     *
     * @param $nb
     * @return int|string
     */
    public static function reformatNumber($nb)
    {
        if (is_numeric($nb)) {
            $negative = $nb < 0;
            $nb = number_format($nb, 0, '.', ',');
            $ret = ($negative) ? '(' : '';
            $ret .= str_replace('-', '', $nb);
            $ret .= ($negative) ? ')' : '';
            return $ret;
        }
        return ($nb == '') ? 0 : $nb;
    }

    /**
     * Methode reprise sur drupal
     *
     * @param $products
     * @param $el_date
     * @param $settings
     * @param $em
     * @return array
     */
    public static function getOverviewPptInfos($products, $el_date, $settings, $em)
    {
        $ret = array(
            'total' => array(
                'actual_count' => 0,
                'old_count' => 0,
                'last_ap' => 0,
                'last_caap' => 0
            ),
            'discover' => array(
                'actual_count' => 0,
                'old_count' => 0,
                'last_ap' => 0,
                'last_caap' => 0
            ),
            'ideate' => array(
                'actual_count' => 0,
                'old_count' => 0,
                'last_ap' => 0,
                'last_caap' => 0
            ),
            'experiment' => array(
                'actual_count' => 0,
                'old_count' => 0,
                'last_ap' => 0,
                'last_caap' => 0
            ),
            'incubate' => array(
                'actual_count' => 0,
                'old_count' => 0,
                'last_ap' => 0,
                'last_caap' => 0
            ),
            'scale_up' => array(
                'actual_count' => 0,
                'old_count' => 0,
                'last_ap' => 0,
                'last_caap' => 0
            )
        );
        $divided_products = array(
            'discover' => array(),
            'ideate' => array(),
            'experiment' => array(),
            'incubate' => array(),
            'scale_up' => array(),
        );
        $last_a_financial_date = self::getFinancialDateForLastA($settings);
        $quarterly_key = 'quarterly_stage_' . $last_a_financial_date;
        $availableTypes = array('discover', 'ideate', 'scale_up', 'incubate', 'experiment');
        foreach ($products as $product) {
            #print_r($product);
            $type = $product['current_stage'];
            if (in_array($type, $availableTypes)) {
                $ret['total']['actual_count']++;
                $start_date_timestamp = ($product['start_date']) ? $product['start_date'] : $product['created_at'];
                $start_date_string = gmdate("Y-m-d", $start_date_timestamp);
                if ($start_date_string <= $last_a_financial_date) {
                    $ret['total']['old_count']++;
                }
                $the_stage_libelle = $type;
                if ($the_stage_libelle) {
                    $ret[$the_stage_libelle]['actual_count']++;
                    $divided_products[$the_stage_libelle][] = $product;
                }
            }
        }


        $ret['discover']['last_ap'] = $em->getRepository('AppBundle:FinancialData')->calculateTotalApFromProductArray($divided_products['discover'], $date = null);

        $ret['ideate']['last_ap'] = $em->getRepository('AppBundle:FinancialData')->calculateTotalApFromProductArray($divided_products['ideate'], $date = null);
        //$ret['ideate']['last_caap'] = pri_calculate_caap($divided_products['ideate']);

        $ret['experiment']['last_ap'] = $em->getRepository('AppBundle:FinancialData')->calculateTotalApFromProductArray($divided_products['experiment'], $date = null);
        //$ret['experiment']['last_caap'] = pri_calculate_caap($divided_products['experiment']);

        $ret['incubate']['last_ap'] = $em->getRepository('AppBundle:FinancialData')->calculateTotalApFromProductArray($divided_products['incubate'], $date = null);
        $ret['incubate']['last_caap'] = $em->getRepository('AppBundle:FinancialData')->calculateCaapFromProductArray($divided_products['incubate']);

        $ret['scale_up']['last_ap'] = $em->getRepository('AppBundle:FinancialData')->calculateTotalApFromProductArray($divided_products['scale_up'], $date = null);
        $ret['scale_up']['last_caap'] = $em->getRepository('AppBundle:FinancialData')->calculateCaapFromProductArray($divided_products['scale_up']);

        $all_caap_product = array_merge($divided_products['incubate'], $divided_products['scale_up']);
        $ret['total']['last_ap'] = $em->getRepository('AppBundle:FinancialData')->calculateTotalApFromProductArray($products, $date = null);
        $ret['total']['last_caap'] = $em->getRepository('AppBundle:FinancialData')->calculateCaapFromProductArray($all_caap_product);


        $ret['discover']['last_ap'] = self::reformatNumber((-$ret['discover']['last_ap'] / 1000));
        $ret['ideate']['last_ap'] = self::reformatNumber((-$ret['ideate']['last_ap'] / 1000));
        $ret['experiment']['last_ap'] = self::reformatNumber((-$ret['experiment']['last_ap'] / 1000));
        $ret['incubate']['last_ap'] = self::reformatNumber((-$ret['incubate']['last_ap'] / 1000));
        $ret['scale_up']['last_ap'] = self::reformatNumber((-$ret['scale_up']['last_ap'] / 1000));
        $ret['total']['last_ap'] = self::reformatNumber((-$ret['total']['last_ap'] / 1000));

        //$ret['ideate']['last_caap'] = reformatNumber(($ret['ideate']['last_caap']/1000));
        //$ret['experiment']['last_caap'] = reformatNumber(($ret['experiment']['last_caap']/1000));
        $ret['incubate']['last_caap'] = self::reformatNumber(($ret['incubate']['last_caap'] / 1000));
        $ret['scale_up']['last_caap'] = self::reformatNumber(($ret['scale_up']['last_caap'] / 1000));
        $ret['total']['last_caap'] = self::reformatNumber(($ret['total']['last_caap'] / 1000));

        return $ret;
    }

    /**
     * Methode reprise sur drupal
     *
     * @param $stage
     * @return null|string
     */
    public static function overviewPptInfosGetLibelleForStage($stage)
    {
        switch ($stage) {
            case 'discover':
                return 'discover';
            case 'ideatie':
                return 'ideate';
            case 'scale_up':
                return 'scale_up';
            case 'incubate':
                return 'incubate';
            case 'experimentation':
                return 'experiment';
        }
        return null;
    }

    /**
     * Methode reprise sur drupal
     *
     * @param null $datetime
     * @return string
     */
    public static function getCurrentFinancialDate($datetime = null)
    {
        if ($datetime) {
            $dateY = $datetime->format('Y');
            $month = intval($datetime->format('m'));
        } else {
            $dateY = date('Y');
            $month = intval(date('m'));
        }
        if ($month < 4) {
            $dateM = '01';
        } elseif ($month < 7) {
            $dateM = '04';
        } elseif ($month < 10) {
            $dateM = '07';
        } else {
            $dateM = 10;
        }
        return $dateY . '-' . $dateM . '-15';
    }

    /**
     * Methode reprise sur drupal
     *
     * @param $settings
     * @return string
     */
    public static function getFinancialDateForLastA($settings)
    {
        $date = $settings->getCurrentFinancialDate();
        $date_explode = explode('-', $date);
        $date_Y = intval($date_explode[0]);
        $date_y = $date_Y;
        $trimestre = $settings->getCurrentTrimester($date);
        if ($trimestre < 3) {
            $year = $date_y;
        } else {
            $year = $date_y - 1;
        }
        return $year . '-07-15';
    }

    /**
     * Methode reprise sur drupal
     *
     * @param $elements
     * @param $paramexport
     * @param $overview
     * @return mixed
     */
    public static function addOverviewPage($elements, $paramexport, $overview)
    {
        $elements['nb_slide']++;
        $elements['current_slide'] = $elements['ppt']->createSlide();

        // Ajout du background
        $shape = $elements['current_slide']->createDrawingShape();
        $shape->setName('')
            ->setDescription('')
            ->setPath(__DIR__ . '/../../web/ppt/couvs/couv-overview.png')
            ->setHeight(1080)
            ->setOffsetX(0)
            ->setOffsetY(0);
        $shape->getShadow()->setVisible(false)
            ->setDirection(0)
            ->setDistance(0);

        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(30)
            ->setWidth(530)
            ->setOffsetX(20)
            ->setOffsetY(1050);
        $textRun = $shape->createTextRun(strtoupper("Pernod Ricard") . " STRICLTY CONFIDENTIAL / DO NOT SHARE");
        $textRun->getFont()
            ->setSize(11)
            ->setName(UtilsPpt::FONT_WORK_SANS)
            ->setColor(new Color(UtilsPpt::COLOR_WHITE));

        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(30)
            ->setWidth(530)
            ->setOffsetX(570)
            ->setOffsetY(1050);
        $textRun = $shape->createTextRun("Based on declarative data. Exported  from the " . "Innovation Hub" . ".");
        $textRun->getFont()
            ->setSize(11)
            ->setName(UtilsPpt::FONT_WORK_SANS)
            ->setColor(new Color(UtilsPpt::COLOR_d8d8d8));


        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(30)
            ->setWidth(300)
            ->setOffsetX(1670)
            ->setOffsetY(1050);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $text = date('M d, Y g:i A');
        $textRun = $shape->createTextRun($text);
        $textRun->getFont()
            ->setSize(11)
            ->setName(UtilsPpt::FONT_WORK_SANS)
            ->setColor(new Color(UtilsPpt::COLOR_e3eaf1));


        // SUBTITLE

        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(40)
            ->setWidth(800)
            ->setOffsetX(140)
            ->setOffsetY(80);
        $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_AUTO);
        $textRun = $shape->createTextRun($paramexport['overview_subtitle']);
        $textRun->getFont()
            ->setItalic(true)
            ->setSize(24)
            ->setName(UtilsPpt::FONT_WORK_SANS)
            ->setColor(new Color(UtilsPpt::COLOR_b5b5b5));


        $width = 220;
        $offset_x_discover = 425;
        $offset_x_ideate = 425;
        $offset_x_experiment = 695;
        $offset_x_incubate = 1000;
        $offset_x_scale_up = 1267;
        $offset_x_vs_title = 70;
        $offset_x_total = 1555;

        $offset_y_actual = 625;
        $offset_y_vs = 725;
        $offset_y_total_ap = 855;
        $offset_y_caap = 960;


        // VS Title
        $versus_date = str_replace('final', '', str_replace('_', ' ', self::getLibelleBudgetCurrentYear()));
        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(49)
            ->setWidth($width)
            ->setOffsetX($offset_x_vs_title)
            ->setOffsetY($offset_y_vs);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $textRun = $shape->createTextRun("VS " . $versus_date);
        $textRun->getFont()
            ->setSize(18)
            ->setName(UtilsPpt::FONT_WORK_SANS)
            ->setColor(new Color(UtilsPpt::COLOR_b5b5b5));


        /**
         * IDEATE
         */
        $data = $overview['ideate'];
        // NB Actual
        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(60)
            ->setWidth($width)
            ->setOffsetX($offset_x_ideate)
            ->setOffsetY($offset_y_actual);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_AUTO);
        $textRun = $shape->createTextRun($data['actual_count']);
        $textRun->getFont()
            ->setSize(36)
            ->setName(UtilsPpt::FONT_WORK_SANS)
            ->setColor(new Color(UtilsPpt::COLOR_005095));

        // Total A&P
        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(49)
            ->setWidth($width)
            ->setOffsetX($offset_x_ideate)
            ->setOffsetY($offset_y_total_ap);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_BOTTOM);
        if ($data['last_ap'] == 'N/A') {
            $textRun = $shape->createTextRun('N/A');
            $textRun->getFont()
                ->setSize(28)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color(UtilsPpt::COLOR_b5b5b5));
        } else {
            $textRun = $shape->createTextRun($data['last_ap']);
            $textRun->getFont()
                ->setSize(28)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color(UtilsPpt::COLOR_BLACK));
            $textRun = $shape->createTextRun(' M€');
            $textRun->getFont()
                ->setSize(18)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color(UtilsPpt::COLOR_BLACK));
        }
        // CAAP
        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(49)
            ->setWidth($width)
            ->setOffsetX($offset_x_ideate)
            ->setOffsetY($offset_y_caap);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_BOTTOM);
        if ($data['last_caap'] == 'N/A') {
            $textRun = $shape->createTextRun('N/A');
            $textRun->getFont()
                ->setSize(28)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color(UtilsPpt::COLOR_b5b5b5));
        } else {
            $textRun = $shape->createTextRun($data['last_caap']);
            $textRun->getFont()
                ->setSize(28)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color(UtilsPpt::COLOR_BLACK));
            $textRun = $shape->createTextRun(' M€');
            $textRun->getFont()
                ->setSize(18)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color(UtilsPpt::COLOR_BLACK));
        }


        /**
         * EXPERIMENT
         */
        $data = $overview['experiment'];
        // NB Actual
        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(49)
            ->setWidth($width)
            ->setOffsetX($offset_x_experiment)
            ->setOffsetY($offset_y_actual);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_AUTO);
        $textRun = $shape->createTextRun($data['actual_count']);
        $textRun->getFont()
            ->setSize(36)
            ->setName(UtilsPpt::FONT_WORK_SANS)
            ->setColor(new Color(UtilsPpt::COLOR_005095));

        // Total A&P
        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(49)
            ->setWidth($width)
            ->setOffsetX($offset_x_experiment)
            ->setOffsetY($offset_y_total_ap);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_BOTTOM);
        if ($data['last_ap'] == 'N/A') {
            $textRun = $shape->createTextRun('N/A');
            $textRun->getFont()
                ->setSize(28)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color(UtilsPpt::COLOR_b5b5b5));
        } else {
            $textRun = $shape->createTextRun($data['last_ap']);
            $textRun->getFont()
                ->setSize(28)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color(UtilsPpt::COLOR_BLACK));
            $textRun = $shape->createTextRun(' M€');
            $textRun->getFont()
                ->setSize(18)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color(UtilsPpt::COLOR_BLACK));
        }
        // CAAP
        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(49)
            ->setWidth($width)
            ->setOffsetX($offset_x_experiment)
            ->setOffsetY($offset_y_caap);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_BOTTOM);
        if ($data['last_caap'] == 'N/A') {
            $textRun = $shape->createTextRun('N/A');
            $textRun->getFont()
                ->setSize(28)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color(UtilsPpt::COLOR_b5b5b5));
        } else {
            $textRun = $shape->createTextRun($data['last_caap']);
            $textRun->getFont()
                ->setSize(28)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color(UtilsPpt::COLOR_BLACK));
            $textRun = $shape->createTextRun(' M€');
            $textRun->getFont()
                ->setSize(18)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color(UtilsPpt::COLOR_BLACK));
        }


        /**
         * INCUBATE
         */
        $data = $overview['incubate'];
        // NB Actual
        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(49)
            ->setWidth($width)
            ->setOffsetX($offset_x_incubate)
            ->setOffsetY($offset_y_actual);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_AUTO);
        $textRun = $shape->createTextRun($data['actual_count']);
        $textRun->getFont()
            ->setSize(36)
            ->setName(UtilsPpt::FONT_WORK_SANS)
            ->setColor(new Color(UtilsPpt::COLOR_005095));

        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(49)
            ->setWidth($width)
            ->setOffsetX($offset_x_incubate)
            ->setOffsetY($offset_y_total_ap);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_BOTTOM);
        if ($data['last_ap'] == 'N/A') {
            $textRun = $shape->createTextRun('N/A');
            $textRun->getFont()
                ->setSize(28)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color(UtilsPpt::COLOR_b5b5b5));
        } else {
            $textRun = $shape->createTextRun($data['last_ap']);
            $textRun->getFont()
                ->setSize(28)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color(UtilsPpt::COLOR_BLACK));
            $textRun = $shape->createTextRun(' M€');
            $textRun->getFont()
                ->setSize(18)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color(UtilsPpt::COLOR_BLACK));
        }
        // CAAP
        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(49)
            ->setWidth($width)
            ->setOffsetX($offset_x_incubate)
            ->setOffsetY($offset_y_caap);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_BOTTOM);
        if ($data['last_caap'] == 'N/A') {
            $textRun = $shape->createTextRun('N/A');
            $textRun->getFont()
                ->setSize(28)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color(UtilsPpt::COLOR_b5b5b5));
        } else {
            $textRun = $shape->createTextRun($data['last_caap']);
            $textRun->getFont()
                ->setSize(28)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color(UtilsPpt::COLOR_BLACK));
            $textRun = $shape->createTextRun(' M€');
            $textRun->getFont()
                ->setSize(18)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color(UtilsPpt::COLOR_BLACK));
        }

        /**
         * SCALE_UP
         */
        $data = $overview['scale_up'];
        // NB Actual
        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(49)
            ->setWidth($width)
            ->setOffsetX($offset_x_scale_up)
            ->setOffsetY($offset_y_actual);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_AUTO);
        $textRun = $shape->createTextRun($data['actual_count']);
        $textRun->getFont()
            ->setSize(36)
            ->setName(UtilsPpt::FONT_WORK_SANS)
            ->setColor(new Color(UtilsPpt::COLOR_005095));

        // Total A&P
        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(49)
            ->setWidth($width)
            ->setOffsetX($offset_x_scale_up)
            ->setOffsetY($offset_y_total_ap);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_BOTTOM);
        if ($data['last_ap'] == 'N/A') {
            $textRun = $shape->createTextRun('N/A');
            $textRun->getFont()
                ->setSize(28)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color(UtilsPpt::COLOR_b5b5b5));
        } else {
            $textRun = $shape->createTextRun($data['last_ap']);
            $textRun->getFont()
                ->setSize(28)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color(UtilsPpt::COLOR_BLACK));
            $textRun = $shape->createTextRun(' M€');
            $textRun->getFont()
                ->setSize(18)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color(UtilsPpt::COLOR_BLACK));
        }
        // CAAP
        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(49)
            ->setWidth($width)
            ->setOffsetX($offset_x_scale_up)
            ->setOffsetY($offset_y_caap);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_BOTTOM);
        if ($data['last_caap'] == 'N/A') {
            $textRun = $shape->createTextRun('N/A');
            $textRun->getFont()
                ->setSize(28)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color(UtilsPpt::COLOR_b5b5b5));
        } else {
            $textRun = $shape->createTextRun($data['last_caap']);
            $textRun->getFont()
                ->setSize(28)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color(UtilsPpt::COLOR_BLACK));
            $textRun = $shape->createTextRun(' M€');
            $textRun->getFont()
                ->setSize(18)
                ->setName(UtilsPpt::FONT_WORK_SANS)
                ->setColor(new Color(UtilsPpt::COLOR_BLACK));
        }

        /**
         * TOTAL
         */
        $data = $overview['total'];
        // NB Actual
        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(49)
            ->setWidth($width)
            ->setOffsetX($offset_x_total)
            ->setOffsetY($offset_y_actual);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_AUTO);
        $textRun = $shape->createTextRun($data['actual_count']);
        $textRun->getFont()
            ->setSize(36)
            ->setName(UtilsPpt::FONT_WORK_SANS)
            ->setColor(new Color(UtilsPpt::COLOR_005095));

        // NB VS
        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(49)
            ->setWidth($width)
            ->setOffsetX($offset_x_total)
            ->setOffsetY($offset_y_vs);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $textRun = $shape->createTextRun($data['old_count']);
        $textRun->getFont()
            ->setSize(22)
            ->setName(UtilsPpt::FONT_WORK_SANS)
            ->setColor(new Color(UtilsPpt::COLOR_b5b5b5));

        // Total A&P
        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(49)
            ->setWidth($width)
            ->setOffsetX($offset_x_total)
            ->setOffsetY($offset_y_total_ap);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_BOTTOM);
        $textRun = $shape->createTextRun($data['last_ap']);
        $textRun->getFont()
            ->setSize(28)
            ->setName(UtilsPpt::FONT_WORK_SANS)
            ->setColor(new Color(UtilsPpt::COLOR_BLACK));

        // CAAP
        $shape = $elements['current_slide']->createRichTextShape()
            ->setHeight(49)
            ->setWidth($width)
            ->setOffsetX($offset_x_total)
            ->setOffsetY($offset_y_caap);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_BOTTOM);
        $textRun = $shape->createTextRun($data['last_caap']);
        $textRun->getFont()
            ->setSize(28)
            ->setName(UtilsPpt::FONT_WORK_SANS)
            ->setColor(new Color(UtilsPpt::COLOR_BLACK));


        return $elements;
    }

    /**
     * Methode reprise sur drupal
     *
     * @param $redis
     * @param $elements
     * @param $data
     * @param $el_date
     * @param null|int $export_id
     * @return mixed
     */
    public static function addAllProductsQualiFull($redis, $elements, $data, $el_date, $export_id)
    {
        $i = 1;
        $nb_innos = count($data['total']['innovations']);
        $coeff = $nb_innos / 80;


        if ($nb_innos < 100) {
            foreach ($data['total']['innovations'] as $innovation) {
                $elements = self::addInnovationQualiPage1($elements, $el_date, $innovation);
                if (!in_array($innovation['current_stage'], array('discover', 'ideation'))) {
                    $elements = self::addInnovationQualiPage($elements, $el_date, $innovation);
                }
                if ($export_id) {
                    $progress = round($i / $coeff) + 15;
                    if ($export_id) {
                        $redis->set($export_id, $progress);
                    }
                }
                $i++;
            }
        } else {
            foreach ($data['total']['innovations'] as $innovation) {
                $elements = self::addInnovationQualiPage1($elements, $el_date, $innovation);
                if ($export_id) {
                    $progress = round($i / $coeff) + 15;
                    if ($export_id) {
                        $redis->set($export_id, $progress);
                    }
                }
                $i++;
            }
        }

        return $elements;
    }

}

