<?php
/**
 * Created by PhpStorm.
 * User: jonathan.garcia
 * Date: 11/07/2018
 * Time: 14:13
 */


namespace AppBundle;

use AppBundle;
use AppBundle\Entity\Stage;
use AppBundle\Entity\User;
use AppBundle\Entity\Settings;
use AppBundle\Entity\Innovation;


class UtilsExcel
{

    /**
     *
     * Generate an excel with actives users
     *
     * @param $em
     * @param $redis
     * @param $excelObj
     * @param $path
     * @param $template_path
     * @param null|int $export_id
     * @param $awsS3Uploader
     * @return bool
     */
    public static function active_users($em, $redis, $excelObj, $path, $template_path, $export_id, $awsS3Uploader)
    {
        if ($export_id) {
            $redis->set($export_id, Settings::EXPORT_PROGRESS_BEFORE_DATA_LOADED);
        }
        $users = $em->getRepository('AppBundle:User')->getAllActiveUsers();
        $template = 'excel-generator-list-active-user.xlsx';

        if ($export_id) {
            $redis->set($export_id, Settings::EXPORT_PROGRESS_DATA_LAUNCH);
        }

        $phpExcelObject = $excelObj->createPHPExcelObject($template_path . $template);
        $phpExcelObject->setActiveSheetIndex(0);
        $phpExcelObject->getActiveSheet()->setTitle('Users');

        $i = 2;
        $arg = 1;
        $coeff = count($users) / Settings::EXPORT_PROGRESS_IN_LOOP;
        foreach ($users as $el_user) {
            $last_login = ($el_user->getLastlogin()) ? $el_user->getLastlogin()->format('Y-m-d H:i:s') : '';
            $role = $el_user->getProperRole(true);

            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(0, $i, 1);
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(1, $i, $last_login);
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(2, $i, $el_user->getProperUsername());
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(3, $i, $el_user->getEmail());
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(4, $i, $role);
            $i++;

            $progress = round($arg / $coeff) + Settings::EXPORT_PROGRESS_DATA_LAUNCH;
            if ($export_id) {
                $redis->set($export_id, $progress);
            }
            $arg++;
        }

        /*---------------------------------------------------------------------------------------*/
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        self::saveExcel($excelObj, $phpExcelObject, $path, $awsS3Uploader, $redis, $export_id);

        if ($export_id) {
            $redis->set($export_id, Settings::EXPORT_PROGRESS_MAX);
        }

        return true;
    }


    /**
     * Save Excel.
     *
     * @param $excelObj
     * @param $phpExcelObject
     * @param $path
     * @param $awsS3Uploader
     * @param $redis
     * @param int|null $export_id
     */
    public static function saveExcel($excelObj, $phpExcelObject, $path, $awsS3Uploader, $redis, $export_id)
    {
        $phpExcelObject->setActiveSheetIndex(0);
        $writer = $excelObj->createWriter($phpExcelObject, 'Excel2007');

        $directory_path = str_replace(basename($path), '', $path);
        if (!file_exists($directory_path)) {
            mkdir($directory_path, 0777, true);
        }
        if (file_exists($path)) unlink($path);
        // SAVE IN LOCAL CONTAINER
        $writer->save($path);

        if ($export_id) {
            $redis->set($export_id, 96);
        }

        // UPLOAD TO AWS
        $aws_path = 'exports/'.basename($directory_path).'/'.basename($path);
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
     *
     * Generate an excel with the users who subscribed to the newsletter
     *
     * @param $em
     * @param $redis
     * @param $excelObj
     * @param $path
     * @param $template_path
     * @param null|int $export_id
     * @param $awsS3Uploader
     * @return bool
     */
    public static function newsletter_users($em, $redis, $excelObj, $path, $template_path, $export_id, $awsS3Uploader)
    {
        if ($export_id) {
            $redis->set($export_id, Settings::EXPORT_PROGRESS_BEFORE_DATA_LOADED);
        }
        $users = $em->getRepository('AppBundle:User')->findNewsletterUsers();
        $template = 'excel-generator-list-active-user.xlsx';

        $phpExcelObject = $excelObj->createPHPExcelObject($template_path . $template);
        $phpExcelObject->setActiveSheetIndex(0);
        $phpExcelObject->getActiveSheet()->setTitle('Users');

        if ($export_id) {
            $redis->set($export_id, Settings::EXPORT_PROGRESS_DATA_LAUNCH);
        }

        $i = 2;
        $arg = 1;
        $coeff = count($users) / Settings::EXPORT_PROGRESS_IN_LOOP;
        foreach ($users as $el_user) {
            $last_login = ($el_user->getLastlogin()) ? $el_user->getLastlogin()->format('Y-m-d H:i:s') : '';
            $role = $el_user->getProperRole(true);

            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(0, $i, 1);
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(1, $i, $last_login);
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(2, $i, $el_user->getProperUsername());
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(3, $i, $el_user->getEmail());
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(4, $i, $role);
            $i++;

            $progress = round($arg / $coeff) + Settings::EXPORT_PROGRESS_DATA_LAUNCH;
            if ($export_id) {
                $redis->set($export_id, $progress);
            }
            $arg++;
        }


        /*---------------------------------------------------------------------------------------*/
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        self::saveExcel($excelObj, $phpExcelObject, $path, $awsS3Uploader, $redis, $export_id);

        if ($export_id) {
            $redis->set($export_id, Settings::EXPORT_PROGRESS_MAX);
        }

        return true;
    }


    /**
     *
     * Generate the team matrix update export
     *
     *
     * @param $em
     * @param $redis
     * @param $excelObj
     * @param $path
     * @param $template_path
     * @param null|int $export_id
     * @param $awsS3Uploader
     * @return bool
     */
    public static function team_matrix_update($em, $redis, $excelObj, $path, $template_path, $export_id, $awsS3Uploader)
    {
        if ($export_id) {
            $redis->set($export_id, Settings::EXPORT_PROGRESS_BEFORE_DATA_LOADED);
        }
        $users = $em->getRepository('AppBundle:User')->findAll();
        $template = 'excel-generator-list-user-with-contact.xlsx';

        $phpExcelObject = $excelObj->createPHPExcelObject($template_path . $template);
        $phpExcelObject->setActiveSheetIndex(0);
        $phpExcelObject->getActiveSheet()->setTitle('Users');

        if ($export_id) {
            $redis->set($export_id, Settings::EXPORT_PROGRESS_DATA_LAUNCH);
        }

        $i = 2;
        $arg = 1;
        $coeff = count($users) / Settings::EXPORT_PROGRESS_IN_LOOP;
        foreach ($users as $el_user) {
            $line = self::add_team_matrix_line_by_user_role($phpExcelObject, $i, $el_user);
            $i = $line['i'];
            $phpExcelObject = $line['phpExcelObject'];
            $progress = round($arg / $coeff) + Settings::EXPORT_PROGRESS_DATA_LAUNCH;
            if ($export_id) {
                $redis->set($export_id, $progress);
            }
            $arg++;
        }


        /*---------------------------------------------------------------------------------------*/
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        self::saveExcel($excelObj, $phpExcelObject, $path, $awsS3Uploader, $redis, $export_id);

        if ($export_id) {
            $redis->set($export_id, Settings::EXPORT_PROGRESS_MAX);
        }

        return true;
    }

    /**
     * add_team_matrix_line_by_user_role.
     *
     * @param $phpExcelObject
     * @param $i
     * @param $el_user
     * @param $role
     * @return mixed
     */
    public static function add_team_matrix_line_by_user_role($phpExcelObject, $i, $el_user)
    {
        $role = $el_user->getProperRole(true);
        if ($role == User::PROPER_ROLE_HQ || $role == User::PROPER_ROLE_MANAGEMENT) { // Management ou HQ : voit tout donc on ne mets pas la liste des innovations
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(0, $i, $el_user->getProperUsername());
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(1, $i, $el_user->getEmail());
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(2, $i, $role);
            $i++;
        } else {
            if ($role === 'NO ROLE') { // On ne le met qu'une seule fois, comme pour HQ et Management
                $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(0, $i, $el_user->getProperUsername());
                $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(1, $i, $el_user->getEmail());
                $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(2, $i, $role);
                $i++;
            } else {
                $user_innovations_rights = $el_user->getUserInnovationRights();
                $never_added = true;
                foreach ($user_innovations_rights as $innovations_right) {
                    $innovation = $innovations_right->getInnovation();
                    if ($innovation) {
                        $innovation_title = ($innovation) ? $innovation->getTitle() : '';
                        $innovation_entity_title = ($innovation && $innovation->getEntity()) ? $innovation->getEntity()->getTitle() : '';
                        $is_contact_owner = ($innovation->getContact() && $el_user->getId() == $innovation->getContact()->getId()) ? 'Contact Owner' : "";
                        $innovation_role = $innovations_right->getProperRole();
                        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(0, $i, $el_user->getProperUsername());
                        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(1, $i, $el_user->getEmail());
                        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(2, $i, $innovation_role);
                        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(3, $i, $innovation_title);
                        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(4, $i, $innovation_entity_title);
                        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(5, $i, ucfirst($innovations_right->getRight()));
                        $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(6, $i, $is_contact_owner);
                        $i++;
                        $never_added = false;
                    }
                }
                if ($never_added) { // si il n'a pas d'innovations liés, on ne le met qu'une seule fois
                    $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(0, $i, $el_user->getProperUsername());
                    $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(1, $i, $el_user->getEmail());
                    $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(2, $i, $role);
                    $i++;
                }
            }
        }
        return array(
            'phpExcelObject' => $phpExcelObject,
            'i' => $i,
        );
    }

    /**
     *
     * Generate the team matrix update export without duplicate
     *
     *
     * @param $em
     * @param $redis
     * @param $excelObj
     * @param $path
     * @param $template_path
     * @param null|int $export_id
     * @param $awsS3Uploader
     * @return bool
     */
    public static function team_matrix_update_no_duplicate($em, $redis, $excelObj, $path, $template_path, $export_id, $awsS3Uploader)
    {
        if ($export_id) {
            $redis->set($export_id, Settings::EXPORT_PROGRESS_BEFORE_DATA_LOADED);
        }
        $users = $em->getRepository('AppBundle:User')->findAll();
        $template = 'excel-generator-list-user.xlsx';

        $phpExcelObject = $excelObj->createPHPExcelObject($template_path . $template);
        $phpExcelObject->setActiveSheetIndex(0);
        $phpExcelObject->getActiveSheet()->setTitle('Users');

        if ($export_id) {
            $redis->set($export_id, Settings::EXPORT_PROGRESS_DATA_LAUNCH);
        }

        $i = 2;
        $arg = 1;
        $coeff = count($users) / Settings::EXPORT_PROGRESS_IN_LOOP;
        foreach ($users as $el_user) {
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(0, $i, 1);
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(1, $i, $el_user->getProperUsername());
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(2, $i, $el_user->getEmail());
            $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(3, $i, $el_user->getProperRole(true));
            $i++;

            $progress = round($arg / $coeff) + Settings::EXPORT_PROGRESS_DATA_LAUNCH;
            if ($export_id) {
                $redis->set($export_id, $progress);
            }
            $arg++;
        }

        /*---------------------------------------------------------------------------------------*/
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        self::saveExcel($excelObj, $phpExcelObject, $path, $awsS3Uploader, $redis, $export_id);

        if ($export_id) {
            $redis->set($export_id, Settings::EXPORT_PROGRESS_MAX);
        }

        return true;
    }


    /**
     * Generate the innovation export.
     *
     * @param $em
     * @param $redis
     * @param $excelObj
     * @param $path
     * @param $template_path
     * @param array $all_innovations
     * @param $user
     * @param null $export_id
     * @param $awsS3Uploader
     * @return bool
     */
    public static function innovations($em, $redis, $excelObj, $path, $template_path, $all_innovations = array(), $user, $export_id, $awsS3Uploader)
    {
        if ($export_id) {
            $redis->set($export_id, Settings::EXPORT_PROGRESS_BEFORE_DATA_LOADED);
        }
        $is_admin = ($user->hasAdminRights() || $user->hasManagementRights());
        $settings = $em->getRepository('AppBundle:Settings')->getCurrentSettings();
        $template = ($is_admin) ? 'excel-generator-project-hq.xlsx' : 'excel-generator-project.xlsx';

        $phpExcelObject = $excelObj->createPHPExcelObject($template_path . $template);
        $phpExcelObject->setActiveSheetIndex(0);

        if ($export_id) {
            $redis->set($export_id, Settings::EXPORT_PROGRESS_DATA_LAUNCH);
        }

        // Generate HEADERS
        $is_admin = true;
        $phpExcelObject = self::generateExcelHeaders($settings, $redis, $phpExcelObject, $settings->getCurrentFinancialDate(), $is_admin, $export_id);

        if ($export_id) {
            $redis->set($export_id, 20);
        }
        $phpExcelObject = self::generateExcelClassicPage($redis, $settings, $phpExcelObject, $all_innovations, null, 20, $is_admin, $export_id);

        if ($export_id) {
            $redis->set($export_id, 92);
        }
        /*---------------------------------------------------------------------------------------*/
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpExcelObject->getActiveSheet()->getStyle('B4:B' . $phpExcelObject->getActiveSheet()->getHighestRow())
            ->getAlignment()->setWrapText(true);
        self::saveExcel($excelObj, $phpExcelObject, $path, $awsS3Uploader, $redis, $export_id);

        if ($export_id) {
            $redis->set($export_id, Settings::EXPORT_PROGRESS_MAX);
        }

        return true;
    }




    /**
     * Generate excel headers.
     *
     * @param $redis
     * @param $objPHPExcel
     * @param $el_date
     * @param null|int $export_id
     * @return mixed
     */
    public static function generateExcelHeaders($settings, $redis, $objPHPExcel, $el_date, $is_admin = false, $export_id)
    {
        $style_jaune = array('alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER), 'font' => array('color' => array('rgb' => '000000')), 'fill' => array('type' => \PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'FEFF00')), 'borders' => array('allborders' => array('style' => \PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => '000000'))));
        $style_bleue_row_3 = array('alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER), 'font' => array('color' => array('rgb' => 'FFFFFF')), 'fill' => array('type' => \PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '0D3964')), 'borders' => array('allborders' => array('style' => \PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => 'FFFFFF'))));
        $style_bleue_turquoise_row_3 = array('alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER), 'font' => array('color' => array('rgb' => 'FFFFFF')), 'fill' => array('type' => \PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '31869C')), 'borders' => array('allborders' => array('style' => \PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => 'FFFFFF'))));

        if ($export_id) {
            $redis->set($export_id, 15);
        }

        $all_dates = $settings->getAllFinancialDateLibellesForData($el_date, true);
        $row_index = 3;
        $row_index_jaune = 1;
        $column_index = 15;
        if ($is_admin) {
            ++$column_index;
        }
        $last_column_index = $column_index;

        $last_column_indexes = self::get_le_columns_indexes($column_index, $all_dates, $settings->getLibelleBudgetNextYear(null, true));

        $all_dates[] = 'A&P/NS';
        $all_dates[] = 'CM/NS';
        $all_dates[] = 'Total A&P';
        $all_dates[] = 'CAAP';

        //$all_dates[] = 'Innovation Type';
        foreach ($all_dates as $key) {
            $style_array = ($key == 'VsLY') ? $style_bleue_turquoise_row_3 : $style_bleue_row_3;
            $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($column_index, $row_index)->applyFromArray($style_array);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($column_index, $row_index, $key);
            $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($column_index)->setWidth(self::get_correct_width_by_key($el_date, $key, $settings));

            // Niveau 1, jaune
            if ($key != 'Innovation Type') {
                $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($column_index, $row_index_jaune)->applyFromArray($style_jaune);
                $value = self::get_calcul_by_key($el_date, $key, $column_index, $settings, $last_column_indexes);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($column_index, $row_index_jaune, $value);
                if ($key == 'VsLY' || $key == 'A&P/NS' || $key == 'CM/NS') {
                    $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($column_index, $row_index_jaune)->getNumberFormat()->applyFromArray(
                        array(
                            'code' => \PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE
                        )
                    );
                }
            }
            $column_index++;
            $last_column_index = $column_index;
        }


        $all_dates = $settings->getAllFinancialDateLibellesForData($el_date, false);
        $last_date = $all_dates[(count($all_dates) - 1)];
        $financials_libelles = array('Volumes in k9Lcs', 'Net sales in K€', 'Contributive Margin in K€', 'A&P in k€', 'Central investment in k€', 'TOTAL A&P  in k€
(A&P + Central invest)', 'CAAP in K€', 'A&P/NS', 'CM/NS', 'Cumul since A15 (' . $last_date . ')');
        $row_index = 2;
        $column_index = 15;
        if ($is_admin) {
            ++$column_index;
        }
        foreach ($financials_libelles as $financials_libelle) {
            $nb_columns_to_merge = self::get_nb_column_to_merge_by_financial_libelle($el_date, $financials_libelle, $all_dates);
            $column_merge_index = ($column_index + $nb_columns_to_merge);
            $style_array = self::get_style_array_by_financial_libelle($financials_libelle);
            $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($column_index, $row_index)->applyFromArray($style_array);
            $objPHPExcel->getActiveSheet()->mergeCellsByColumnAndRow($column_index, $row_index, $column_merge_index, $row_index);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($column_index, $row_index, $financials_libelle);
            $column_index = $column_merge_index + 1;
        }
        $last_column_index -= 1;
        $objPHPExcel->getActiveSheet()->setAutoFilter('A3:' . self::get_column_letter_by_column_index($last_column_index) . '3');

        return $objPHPExcel;
    }


    /**
     * Generate excel headers financials.
     *
     * @param $redis
     * @param $objPHPExcel
     * @param $el_date
     * @param null|int $export_id
     * @return mixed
     */
    public static function generateExcelHeadersFinancials($settings, $redis, $objPHPExcel, $el_date, $export_id)
    {
        $base_column_index = 44;
        $style_jaune = array('alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER), 'font' => array('color' => array('rgb' => '000000')), 'fill' => array('type' => \PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'FEFF00')), 'borders' => array('allborders' => array('style' => \PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => '000000'))));
        $style_bleue_row_3 = array('alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER), 'font' => array('color' => array('rgb' => 'FFFFFF')), 'fill' => array('type' => \PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '0D3964')), 'borders' => array('allborders' => array('style' => \PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => 'FFFFFF'))));
        $style_bleue_turquoise_row_3 = array('alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER), 'font' => array('color' => array('rgb' => 'FFFFFF')), 'fill' => array('type' => \PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '31869C')), 'borders' => array('allborders' => array('style' => \PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => 'FFFFFF'))));

        if ($export_id) {
            $redis->set($export_id, 15);
        }

        $all_dates = $settings->getAllFinancialDateLibellesForData($el_date, true);
        $row_index = 3;
        $row_index_jaune = 1;
        $column_index = $base_column_index;
        $last_column_index = $column_index;

        $last_column_indexes = self::get_le_columns_indexes($column_index, $all_dates, $settings->getLibelleBudgetNextYear(null, true));

        $all_dates[] = 'A&P/NS';
        $all_dates[] = 'CM/NS';
        $all_dates[] = 'Total A&P';
        $all_dates[] = 'CAAP';
        //$all_dates[] = 'Innovation Type';
        foreach ($all_dates as $key) {
            $style_array = ($key == 'VsLY') ? $style_bleue_turquoise_row_3 : $style_bleue_row_3;
            $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($column_index, $row_index)->applyFromArray($style_array);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($column_index, $row_index, $key);
            $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($column_index)->setWidth(self::get_correct_width_by_key($el_date, $key, $settings));

            // Niveau 1, jaune
            if ($key != 'Innovation Type') {
                $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($column_index, $row_index_jaune)->applyFromArray($style_jaune);
                $value = self::get_calcul_by_key($el_date, $key, $column_index, $settings, $last_column_indexes);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($column_index, $row_index_jaune, $value);
                if ($key == 'VsLY' || $key == 'A&P/NS' || $key == 'CM/NS') {
                    $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($column_index, $row_index_jaune)->getNumberFormat()->applyFromArray(
                        array(
                            'code' => \PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE
                        )
                    );
                }
            }
            $column_index++;
            $last_column_index = $column_index;
        }


        $all_dates = $settings->getAllFinancialDateLibellesForData($el_date, false);
        $last_date = $all_dates[(count($all_dates) - 1)];
        $financials_libelles = array('Volumes in k9Lcs', 'Net sales in K€', 'Contributive Margin in K€', 'A&P in k€', 'Central investment in k€', 'TOTAL A&P  in k€
(A&P + Central invest)', 'CAAP in K€', 'A&P/NS', 'CM/NS', 'Cumul since A15 (' . $last_date . ')');
        $row_index = 2;
        $column_index = $base_column_index;
        foreach ($financials_libelles as $financials_libelle) {
            $nb_columns_to_merge = self::get_nb_column_to_merge_by_financial_libelle($el_date, $financials_libelle, $all_dates);
            $column_merge_index = ($column_index + $nb_columns_to_merge);
            $style_array = self::get_style_array_by_financial_libelle($financials_libelle);
            $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($column_index, $row_index)->applyFromArray($style_array);
            $objPHPExcel->getActiveSheet()->mergeCellsByColumnAndRow($column_index, $row_index, $column_merge_index, $row_index);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($column_index, $row_index, $financials_libelle);
            $column_index = $column_merge_index + 1;
        }
        $last_column_index -= 1;
        $objPHPExcel->getActiveSheet()->setAutoFilter('A3:' . self::get_column_letter_by_column_index($last_column_index) . '3');

        return $objPHPExcel;
    }


    /**
     * get_le_columns_indexes
     * @param $base_index
     * @param $all_dates
     * @param $le_libelle
     * @return array
     */
    public static function get_le_columns_indexes($base_index, $all_dates, $le_libelle){
        $last_column_indexes = array();
        $index = $base_index;
        $pre_libelles = array('Vol', 'NS', 'CM', 'A&P', 'CI', '', 'CAAP');
        foreach ($all_dates as $date) {
            foreach ($pre_libelles as $pre_libelle){
                if($date == $pre_libelle.' '.$le_libelle){
                    $key = ($pre_libelle != '') ? $pre_libelle : 'TOTAL_A&P';
                    $last_column_indexes[$key] =  $index;
                }
            }
            $index++;
        }

        return $last_column_indexes;
    }


    // Ici je met tous les libellés des dates de niveau 3 + les jaunes de niveau 1
    public static function get_correct_width_by_key($el_date, $key, $settings)
    {
        $last_b = $settings->getLibelleLastBForExcelExport($el_date);
        if ($key == $last_b) {
            return 8;
        } else {
            return 12;
        }
    }

    public static function get_calcul_by_key($el_date, $key, $column_index, $settings, $last_column_indexes = array())
    {
        $last_b = $settings->getLibelleLastBForExcelExport($el_date);
        $letter = self::get_column_letter_by_column_index($column_index);
        if ($key == 'VsLY') {
            $index_1 = $column_index - 1;
            $index_2 = $column_index - 3;
            $letter_1 = self::get_column_letter_by_column_index($index_1);
            $letter_2 = self::get_column_letter_by_column_index($index_2);
            return '=((' . $letter_1 . '1-' . $letter_2 . '1)/' . $letter_2 . '1)';
        } elseif ($key == $last_b || $key == 'Innovation Type') {
            return '';
        } elseif ($key == 'A&P/NS') {
            if(!array_key_exists('TOTAL_A&P', $last_column_indexes) || !array_key_exists('NS', $last_column_indexes)){
                return '';
            }
            $total_ap_letter = self::get_column_letter_by_column_index($last_column_indexes['TOTAL_A&P']);
            $ns_letter = self::get_column_letter_by_column_index($last_column_indexes['NS']);
            return '=(ABS('.$total_ap_letter.'1)/'.$ns_letter.'1)'; // ABS TOTAL AP/NS
        } elseif ($key == 'CM/NS') {
            if(!array_key_exists('CM', $last_column_indexes) || !array_key_exists('NS', $last_column_indexes)){
                return '';
            }
            $cm_letter = self::get_column_letter_by_column_index($last_column_indexes['CM']);
            $ns_letter = self::get_column_letter_by_column_index($last_column_indexes['NS']);
            return '=('.$cm_letter.'1/'.$ns_letter.'1)'; // CM / NS
        }
        return '=SUBTOTAL(109,' . $letter . '4:' . $letter . '2000)';
    }

    // Ici je met tous les libellés des dates de niveau 2
    public static function get_nb_column_to_merge_by_financial_libelle($el_date, $financial_libelle, $fake_all_dates)
    {
        $all_dates = array();
        foreach ($fake_all_dates as $all_date) {
            if ($all_date != "N/A") {
                $all_dates[] = $all_date;
            }
        }
        $last_date = $all_dates[(count($all_dates) - 1)];
        $only_one = array('A&P/NS', 'CM/NS');
        $only_two = array('Cumul since A15 (' . $last_date . ')');
        $only_nb = array('A&P in k€', 'Central investment in k€');
        $nb = count($all_dates);
        if (in_array($financial_libelle, $only_one)) {
            return 0;
        } elseif (in_array($financial_libelle, $only_two)) {
            return 1;
        } elseif (in_array($financial_libelle, $only_nb)) {
            return $nb - 1;
        }
        return $nb;
    }

    /**
     * @param $financial_libelle
     * @return array
     */
    public static function get_style_array_by_financial_libelle($financial_libelle)
    {
        $style_bleue_row_2 = array('alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER), 'font' => array('color' => array('rgb' => 'FFFFFF')), 'fill' => array('type' => \PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '416395')), 'borders' => array('allborders' => array('style' => \PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => '000000'))));
        $styleVertMoche = array('alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER), 'font' => array('color' => array('rgb' => '000000')), 'fill' => array('type' => \PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'C4D79B')), 'borders' => array('allborders' => array('style' => \PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => '000000'))));
        $only_one = array('A&P/NS', 'CM/NS');
        if (in_array($financial_libelle, $only_one)) {
            return $styleVertMoche;
        }
        return $style_bleue_row_2;
    }


    /**
     * get_column_letter_by_column_index
     * @param $column_index
     * @return string
     */
    public static function get_column_letter_by_column_index($column_index)
    {
        $letters = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
        $first_letter = '';
        if ($column_index >= count($letters)) {
            $index_first_letter = floor($column_index / count($letters)) - 1;
            $index_second_letter = $column_index % count($letters);
            $first_letter = $letters[$index_first_letter];
            $second_letter = $letters[$index_second_letter];
        } else {
            $second_letter = $letters[$column_index];
        }
        return $first_letter . $second_letter;
    }

    /**
     * Generate excel classic page.
     *
     * @param $em
     * @param $settings
     * @param $objPHPExcel
     * @param $data
     * @param $el_date
     * @param int $progress
     * @param null|int $export_id
     * @return mixed
     */
    public static function generateExcelClassicPage($redis, $settings, $objPHPExcel, $data, $el_date, $progress, $is_admin = false, $export_id)
    {
        $row_index = 4;
        $all_dates = $settings->getAllFinancialDateLibellesForData($el_date, true);
        $all_libelles = $settings->getAllFinancialDateLibellesForData($el_date, false);
        $last_b = $settings->getLibelleLastBForExcelExport($el_date);
        $last_libelle = $all_libelles[(count($all_libelles) - 1)];
        $all_dates[] = $last_b;
        $all_dates[] = $last_b;
        $all_dates[] = 'Total A&P';
        $all_dates[] = 'CAAP';
        //$all_dates[] = 'Innovation Type';
        $argh = 1;
        $coeff = count($data) / (Settings::EXPORT_PROGRESS_IN_LOOP - 20);
        foreach ($data as $product) {
            $objPHPExcel = self::addProductToExcel($settings, $objPHPExcel, $product, $row_index, $el_date, $all_dates, $last_libelle, $last_b, $is_admin);
            $row_index++;
            $the_progress = round($argh / $coeff) + $progress;

            if ($export_id) {
                $redis->set($export_id, $the_progress);
            }
            $argh++;
        }
        return $objPHPExcel;
    }


    /**
     * pri_add_product_to_excel : DONE
     * @param $objPHPExcel
     * @param $product
     * @param $row_index
     * @param $el_date
     * @return mixed
     */
    public static function addProductToExcel($settings, $objPHPExcel, $product, $row_index, $el_date, $all_dates, $last_libelle, $last_b, $is_admin = false)
    {

        $style_classic = array('font' => array('color' => array('rgb' => '000000')), 'fill' => array('type' => \PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'FFFFFF')), 'borders' => array('allborders' => array('style' => \PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => 'CECECE'))));
        $style_border_right = array('font' => array('color' => array('rgb' => '000000')), 'fill' => array('type' => \PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'FFFFFF')), 'borders' => array('allborders' => array('style' => \PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => 'CECECE')), 'right' => array('style' => \PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => '000000'))));

        $column_index = 0;
        $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow($column_index, $row_index, $product['status']);
        if ($is_admin) {
            $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(++$column_index, $row_index, $product['in_prisma']);
        }
        $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(++$column_index, $row_index, $product['name']);
        $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(++$column_index, $row_index, $product['current_stage']);
        $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(++$column_index, $row_index, $product['brand']);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(++$column_index, $row_index, $product['in_market_date']);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(++$column_index, $row_index, $product['years_since_launch']);
        $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(++$column_index, $row_index, $product['growth_model']);
        $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(++$column_index, $row_index, $product['entity']);
        $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(++$column_index, $row_index, $product['innovation_type']);
        $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(++$column_index, $row_index, $product['classification_type']);
        $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(++$column_index, $row_index, $product['category']);
        $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(++$column_index, $row_index, $product['consumer_opportunity']);
        $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(++$column_index, $row_index, $product['moc']);
        $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(++$column_index, $row_index, $product['business_drivers']);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(++$column_index, $row_index, 1);
        $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($column_index, $row_index)->applyFromArray($style_border_right); // Style de fin des données simples

        ++$column_index;
        $right_border_keys = array('VsLY', 'CAAP', 'A&P ' . $last_libelle, 'CI ' . $last_libelle, $last_b);
        $first_last_b = true;

        $last_a = $settings->getLibelleLastA($el_date, true);
        $last_le = str_replace('_', ' ', $settings->getLibelleLastEstimateNextYear($el_date));
        $last_le = str_replace(' final', '', $last_le);

        $last_a_column_index = $column_index;
        $last_le_column_index = $column_index;

        foreach ($all_dates as $key) {
            $style_array = (in_array($key, $right_border_keys)) ? $style_border_right : $style_classic;
            $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($column_index, $row_index)->applyFromArray($style_array);
            if ($key == $last_b || $key == 'VsLY') {
                $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($column_index, $row_index)->getNumberFormat()->applyFromArray(
                    array(
                        'code' => \PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE
                    )
                );
            }
            if ($product['is_early_stage'] && strpos($key, 'CAAP') !== false) {
                $value = '';
            } else {
                $value = (array_key_exists($key, $product)) ? $product[$key] : '';
            }
            if ($key == $last_b) {
                if ($first_last_b) {
                    $first_last_b = false;
                    $value = $product['A&P/NS'];
                } else {
                    $value = $product['CM/NS'];
                }
            } elseif ($key == 'Innovation Type') {
                $value = $product['innovation_type'];
            } elseif ($key == 'VsLY') {
                $last_a_column_letter = self::get_column_letter_by_column_index($last_a_column_index);
                $last_le_column_letter = self::get_column_letter_by_column_index($last_le_column_index);
                $value = '=((' . $last_le_column_letter . $row_index . '-' . $last_a_column_letter . $row_index . ')/' . $last_a_column_letter . $row_index . ')';
            }
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($column_index, $row_index, $value);
            if (strpos($key, $last_a) !== false) {
                $last_a_column_index = $column_index;
            } elseif (strpos($key, $last_le) !== false) {
                $last_le_column_index = $column_index;
            }
            $column_index++;
        }

        return $objPHPExcel;
    }


    /**
     * Generate the innovation export.
     *
     * @param $em
     * @param $redis
     * @param $excelObj
     * @param $path
     * @param $template_path
     * @param array $all_innovations
     * @param array $all_innovations_excel
     * @param $user
     * @param null $export_id
     * @param $awsS3Uploader
     * @return bool
     */
    public static function complete($em, $redis, $excelObj, $path, $template_path, $all_innovations = array(), $all_innovations_excel = array(), $user, $export_id, $awsS3Uploader)
    {
        if ($export_id) {
            $redis->set($export_id, Settings::EXPORT_PROGRESS_BEFORE_DATA_LOADED);
        }
        $settings = $em->getRepository('AppBundle:Settings')->getCurrentSettings();
        $template = 'excel-generator-complete.xlsx';

        $phpExcelObject = $excelObj->createPHPExcelObject($template_path . $template);
        $phpExcelObject->setActiveSheetIndex(0);

        if ($export_id) {
            $redis->set($export_id, Settings::EXPORT_PROGRESS_DATA_LAUNCH);
        }

        $data = array(
            'innovations' => $all_innovations,
            'innovations_excel' => $all_innovations_excel,
        );

        // Generate Financials HEADERS
        $phpExcelObject = self::generateExcelHeadersFinancials($settings, $redis, $phpExcelObject, $settings->getCurrentFinancialDate(), $export_id);

        if ($export_id) {
            $redis->set($export_id, 20);
        }
        $phpExcelObject = self::generateExcelCompletePage($redis, $settings, $phpExcelObject, $data, null, 20, $export_id);

        if ($export_id) {
            $redis->set($export_id, 92);
        }
        /*---------------------------------------------------------------------------------------*/
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpExcelObject->getActiveSheet()->getStyle('B4:B' . $phpExcelObject->getActiveSheet()->getHighestRow())
            ->getAlignment()->setWrapText(true);
        var_dump($path);
        self::saveExcel($excelObj, $phpExcelObject, $path, $awsS3Uploader, $redis, $export_id);

        if ($export_id) {
            $redis->set($export_id, Settings::EXPORT_PROGRESS_MAX);
        }

        return true;
    }


    /**
     * Generate excel classic page.
     *
     * @param $em
     * @param $settings
     * @param $objPHPExcel
     * @param $data
     * @param $el_date
     * @param int $progress
     * @param null|int $export_id
     * @return mixed
     */
    public static function generateExcelCompletePage($redis, $settings, $objPHPExcel, $data, $el_date, $progress, $export_id)
    {
        $row_index = 4;
        $all_dates = $settings->getAllFinancialDateLibellesForData($el_date, true);
        $all_libelles = $settings->getAllFinancialDateLibellesForData($el_date, false);
        $last_b = $settings->getLibelleLastBForExcelExport($el_date);
        $last_libelle = $all_libelles[(count($all_libelles) - 1)];
        $all_dates[] = $last_b;
        $all_dates[] = $last_b;
        $all_dates[] = 'Total A&P';
        $all_dates[] = 'CAAP';

        //$all_dates[] = 'Innovation Type';
        $argh = 1;
        $coeff = count($data['innovations']) / (Settings::EXPORT_PROGRESS_IN_LOOP - 20);
        foreach ($data['innovations'] as $product) {
            $product_excel = self::getExcelProduct($data['innovations_excel'], $product['id']);
            $objPHPExcel = self::addProductToCompleteExcel($settings, $objPHPExcel, $product, $product_excel, $row_index, $el_date, $all_dates, $last_libelle, $last_b);
            $row_index++;
            $the_progress = round($argh / $coeff) + $progress;

            if ($export_id) {
                $redis->set($export_id, $the_progress);
            }
            $argh++;
        }
        return $objPHPExcel;
    }

    public static function getExcelProduct($innovations, $id){
        foreach ($innovations as $innovation){
            if($innovation['id'] == $id){
                return $innovation;
            }
        }
        return null;
    }
    /**
     * pri_add_product_to_excel : DONE
     * @param $objPHPExcel
     * @param $product
     * @param $row_index
     * @param $el_date
     * @return mixed
     */
    public static function addProductToCompleteExcel($settings, $objPHPExcel, $product, $product_excel, $row_index, $el_date, $all_dates, $last_libelle, $last_b)
    {

        $style_classic = array('font' => array('color' => array('rgb' => '000000')), 'fill' => array('type' => \PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'FFFFFF')), 'borders' => array('allborders' => array('style' => \PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => 'CECECE'))));
        $style_border_right = array('font' => array('color' => array('rgb' => '000000')), 'fill' => array('type' => \PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'FFFFFF')), 'borders' => array('allborders' => array('style' => \PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => 'CECECE')), 'right' => array('style' => \PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => '000000'))));

        $stage_id = $product['current_stage_id'];
        $innovation_is_a_service = Innovation::innovationArrayIsAService($product);
        $column_index = -1;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(++$column_index, $row_index, 1);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(++$column_index, $row_index, $product['id']);
        $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(++$column_index, $row_index, $product['title']);
        $stage = ($product_excel) ? $product_excel['current_stage'] : '';
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(++$column_index, $row_index, $stage);
        $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(++$column_index, $row_index, $product['entity']['title']);
        $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(++$column_index, $row_index, $product['brand']['title']);
        $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(++$column_index, $row_index, $product['contact']['username']);
        $start_date = ($product['start_date']) ? gmdate("m/Y", $product['start_date'] ) : '';
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(++$column_index, $row_index, $start_date);
        $in_market_date = ($product['in_market_date']) ? gmdate("m/Y", $product['in_market_date'] ) : '';
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(++$column_index, $row_index, $in_market_date);
        $value = (!$innovation_is_a_service) ? $product['consumer_opportunity_title'] : '';
        $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(++$column_index, $row_index, $value);
        $value = (!$innovation_is_a_service) ? $product['category'] : '';
        $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(++$column_index, $row_index, $value);
        if($innovation_is_a_service || !$product['growth_model']){
            $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(++$column_index, $row_index, "");
        }else{
            $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(++$column_index, $row_index, (($product['growth_model'] == 'fast_growth') ? 'Fast Growth' : 'Slow Build'));
        }
        $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(++$column_index, $row_index, $product['innovation_type']);
        $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(++$column_index, $row_index, $product['classification_type']);
        if($innovation_is_a_service){
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(++$column_index, $row_index, "");
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(++$column_index, $row_index, "");
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(++$column_index, $row_index, "");
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(++$column_index, $row_index, "");
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(++$column_index, $row_index, "");
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(++$column_index, $row_index, "");
        }else{
            $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(++$column_index, $row_index, (($product['replace_existing_product'] == '1') ? 'YES' : 'NO'));
            $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(++$column_index, $row_index, $product['existing_product']);
            $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(++$column_index, $row_index, $product['moc']);
            $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(++$column_index, $row_index, $product['business_drivers']);
            $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(++$column_index, $row_index, $product['growth_strategy']);
            $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(++$column_index, $row_index, $product['abv']);
        }
        $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(++$column_index, $row_index, (($product['in_prisma'] == '1') ? 'YES' : 'NO'));

        // ELEVATOR PITCH
        $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(++$column_index, $row_index, $product['why_invest_in_this_innovation']);
        $value = (!$innovation_is_a_service) ? $product['story'] : '';
        $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(++$column_index, $row_index, $value);
        $value = (!$innovation_is_a_service) ? $product['value_proposition'] : '';
        $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(++$column_index, $row_index, $value);
        $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(++$column_index, $row_index, $product['consumer_insight']);
        $value = (!$innovation_is_a_service && !in_array($stage_id, [Stage::STAGE_ID_DISCOVER, Stage::STAGE_ID_IDEATE])) ? $product['early_adopter_persona'] : '';
        $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(++$column_index, $row_index, $value);
        $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(++$column_index, $row_index, $product['source_of_business']);
        if(!$innovation_is_a_service && !in_array($stage_id, [Stage::STAGE_ID_DISCOVER, Stage::STAGE_ID_IDEATE])){
            $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(++$column_index, $row_index, $product['universal_key_information_1']);
            $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(++$column_index, $row_index, $product['universal_key_information_2']);
            $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(++$column_index, $row_index, $product['universal_key_information_4']);
            $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(++$column_index, $row_index, $product['universal_key_information_4_vs']);
            if($product['new_to_the_world']){
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(++$column_index, $row_index, "");
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(++$column_index, $row_index, "");
            }else{
                $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(++$column_index, $row_index, $product['universal_key_information_3']);
                $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(++$column_index, $row_index, $product['universal_key_information_3_vs']);
            }
            $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(++$column_index, $row_index, $product['universal_key_information_5']);

            $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(++$column_index, $row_index, $product['universal_key_learning_so_far']);
            $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(++$column_index, $row_index, $product['universal_next_steps']);
        }else{
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(++$column_index, $row_index, "");
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(++$column_index, $row_index, "");
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(++$column_index, $row_index, "");
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(++$column_index, $row_index, "");
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(++$column_index, $row_index, "");
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(++$column_index, $row_index, "");
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(++$column_index, $row_index, "");

            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(++$column_index, $row_index, "");
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(++$column_index, $row_index, "");
        }

        // ASSETS
        if(!$innovation_is_a_service && !in_array($stage_id, [Stage::STAGE_ID_DISCOVER, Stage::STAGE_ID_IDEATE])){
            $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(++$column_index, $row_index, $product['video_link']);
            $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(++$column_index, $row_index, $product['video_password']);
        }else{
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(++$column_index, $row_index, "");
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(++$column_index, $row_index, "");
        }
        $value = (!$innovation_is_a_service) ? $product['ibp_link'] : '';
        $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(++$column_index, $row_index, $value);
        $value = (!$innovation_is_a_service) ? $product['website_url'] : '';
        $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(++$column_index, $row_index, $value);
        $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(++$column_index, $row_index, $product['press_release_link']);

        // Markets
        $markets = "";
        if(!$innovation_is_a_service && !in_array($stage_id, [Stage::STAGE_ID_DISCOVER, Stage::STAGE_ID_IDEATE, Stage::STAGE_ID_EXPERIMENT])) {
            foreach ($product['markets_in_array'] as $market_key) {
                $markets .= UtilsCountry::getCountryNameByCode($market_key) . ', ';
            }
        }
        $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(++$column_index, $row_index, $markets);

        // Services / Experiences fields
        if($innovation_is_a_service){
            $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(++$column_index, $row_index, (($product['is_multi_brand'] == '1') ? 'YES' : 'NO'));
            $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(++$column_index, $row_index, $product['unique_experience']);
            $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(++$column_index, $row_index, $product['website_url']);
            $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(++$column_index, $row_index, (($product['have_earned_any_money_yet'] == '1') ? 'YES' : 'NO'));
            $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow(++$column_index, $row_index, $product['plan_to_make_money']);
        }else{
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(++$column_index, $row_index, "");
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(++$column_index, $row_index, "");
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(++$column_index, $row_index, "");
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(++$column_index, $row_index, "");
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(++$column_index, $row_index, "");
        }
        $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($column_index, $row_index)->applyFromArray($style_border_right); // Style de fin des données simples

        // FINANCIAL DATA
        ++$column_index;
        $right_border_keys = array('VsLY', 'CAAP', 'A&P ' . $last_libelle, 'CI ' . $last_libelle, $last_b);
        $first_last_b = true;

        $last_a = $settings->getLibelleLastA($el_date, true);
        $last_le = str_replace('_', ' ', $settings->getLibelleLastEstimateNextYear($el_date));
        $last_le = str_replace(' final', '', $last_le);

        $last_a_column_index = $column_index;
        $last_le_column_index = $column_index;

        foreach ($all_dates as $key) {
            $style_array = (in_array($key, $right_border_keys)) ? $style_border_right : $style_classic;
            $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($column_index, $row_index)->applyFromArray($style_array);
            if ($key == $last_b || $key == 'VsLY') {
                $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($column_index, $row_index)->getNumberFormat()->applyFromArray(
                    array(
                        'code' => \PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE
                    )
                );
            }
            if ($product_excel['is_early_stage'] && strpos($key, 'CAAP') !== false) {
                $value = '';
            } else {
                $value = (array_key_exists($key, $product_excel)) ? $product_excel[$key] : '';
            }
            if ($key == $last_b) {
                if ($first_last_b) {
                    $first_last_b = false;
                    $value = $product_excel['A&P/NS'];
                } else {
                    $value = $product_excel['CM/NS'];
                }
            } elseif ($key == 'Innovation Type') {
                $value = $product_excel['innovation_type'];
            } elseif ($key == 'VsLY') {
                $last_a_column_letter = self::get_column_letter_by_column_index($last_a_column_index);
                $last_le_column_letter = self::get_column_letter_by_column_index($last_le_column_index);
                $value = '=((' . $last_le_column_letter . $row_index . '-' . $last_a_column_letter . $row_index . ')/' . $last_a_column_letter . $row_index . ')';
            }
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($column_index, $row_index, $value);
            if (strpos($key, $last_a) !== false) {
                $last_a_column_index = $column_index;
            } elseif (strpos($key, $last_le) !== false) {
                $last_le_column_index = $column_index;
            }
            $column_index++;
        }

        return $objPHPExcel;
    }
}