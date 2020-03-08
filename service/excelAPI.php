<?php

/**
 * phpexcel导出管理
 */
class excelAPI {

    protected static $_excel = null;

    /**
     * 引入phpexcel并实例化
     */
    public static function init() {
        if (self::$_excel === null) {
            self::$_excel = Load::lib('PHPExcel');
        }
    }

    /**
     * 文件保存格式:HTML、CSV、PDF、Excel2007、Excel5
     */
    public static function export_save($type, $file_name) {
        self::init();
        /* //excel2007
          header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
          header("Content-Disposition: attachment;filename=" . $file_name . ".xlsx");
          header('Cache-Control: max-age=0'); */
        header("Content-type:application/vnd.ms-excel;charset=UTF-8");
        header('Cache-Control: max-age=0');
        header("Content-Disposition: attachment;filename=" . $file_name . ".xls");
        $objWriter = PHPExcel_IOFactory:: createWriter(self::$_excel, $type);
        return $objWriter;
    }

    /**
     * 实例化添加图片
     */
    public static function new_draw() {
        self::init();
        $objDrawing = new PHPExcel_Worksheet_Drawing(self::$_excel);
        return $objDrawing;
    }

    /**
     * 设置excel的属性
     */
    public static function set_property() {
        self::init();
        return self::$_excel->getProperties();
    }

    /**
     * 设置当前的sheet
     */
    public static function set_sheet() {
        self::init();
        return self::$_excel->getActiveSheet();
    }

}

?>