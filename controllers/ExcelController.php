<?php 
namespace controllers;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
// use models\Blog;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class ExcelController {
    public function testExc()
    {
        // 数据库中取出数据
        $blog = new \models\Blog;
        $data = $blog->getNew();
    
        // 获取当前标签页
        $spreadsheet = new Spreadsheet();
        // 获取当前工作
        $sheet = $spreadsheet->getActiveSheet();
    
        // 设置第1行内容
        $sheet->setCellValue('A1', '标题');
        $sheet->setCellValue('B1', '内容');
        $sheet->setCellValue('C1', '发表时间');
        $sheet->setCellValue('D1', '是发公开');
    
        // 从第2行写入数据
        $i = 2;
        foreach($data as $v)
        {
            $sheet->setCellValue('A'.$i, $v['title']);
            $sheet->setCellValue('B'.$i, $v['content']);
            $sheet->setCellValue('C'.$i, $v['created_at']);
            $sheet->setCellValue('D'.$i, $v['is_show']==1?'公开':'私有');
    
            $i++;
        }
        
        // 生成 Excel 文件
        $writer = new Xlsx($spreadsheet);
        $writer->save(ROOT . 'hello-world.xlsx');
    }
}