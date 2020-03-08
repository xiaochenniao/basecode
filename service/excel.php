<?php

class excel {

    protected static $_engine = null;
    protected static $_params = array();

    public static function set($key, $value = true) {
        if (is_array($key)) {
            foreach ($key as $_key => $_val) {
                self::$_params[$_key] = $_val;
            }
        } else {
            self::$_params[$key] = $value;
        }
    }

    public static function init() {
        if (self::$_engine === null) {
            require_once CORE_DIR . '/third/excel/PHPExcel.php';
            self::$_engine = new PHPExcel();
        }
        return self::$_engine;
    }

    public static function down($filename = null, $title = null) {
        if (!$filename) {
            $filename = 'export';
        }
        if (!$title) {
            $title = $filename;
        }
        require_once CORE_DIR . '/third/excel/PHPExcel.php';
        $objPHPExcel = new PHPExcel();
        //$objPHPExcel->getProperties()->setTitle($title.'_'.date("Y-m-d H:i:s"));
        $objPHPExcel->setActiveSheetIndex(0);
        foreach (self::$_params as $_key => $_val) {
            $objPHPExcel->getActiveSheet()->setCellValue($_key, $_val);
        }
        $objPHPExcel->getActiveSheet()->setTitle($title);
        header('Content-Type: application/vnd.ms-excel;charset=utf-8');
        header('Content-Disposition:attachment;filename=' . $filename . '_' . date("YmdHis") . '.xls');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }

}
