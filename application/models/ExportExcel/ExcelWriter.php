<?php

class Application_Model_ExcelWriter {
    private $excel;
    private $currentRow;
    
    const EXPORT_DIRECTORY = '../public/export/';
    private $columns = array(
        array('letter'  => 'A', 'dbcolumn' => 'NomVille',       'label' => 'Ville'),
        array('letter'  => 'B', 'dbcolumn' => 'CodePostal',     'label' => 'Code Postal'),
        array('letter'  => 'C', 'dbcolumn' => 'CodePostal2',    'label' => 'Code Postal2'),
        array('letter'  => 'D', 'dbcolumn' => 'CodeInsee',      'label' => 'Code INSEE'),
        array('letter'  => 'E', 'dbcolumn' => 'IdDepartement',  'label' => 'Département'),
        array('letter'  => 'F', 'dbcolumn' => 'NomCommAgglo',   'label' => 'Comm. d\'agglo'),
        array('letter'  => 'G', 'dbcolumn' => 'TypeZone',       'label' => 'Zone'),
        array('letter'  => 'H', 'dbcolumn' => 'Mode',           'label' => 'Mode de pilotage'),
        array('letter'  => 'I', 'dbcolumn' => 'NomUI',          'label' => 'UI'),
        array('letter'  => 'J', 'dbcolumn' => 'nomOperateur',   'label' => 'Opérateur'),
        array('letter'  => 'K', 'dbcolumn' => 'LancementLot1',  'label' => 'Lancement lot 1'),
        array('letter'  => 'L', 'dbcolumn' => 'dateImport',     'label' => 'Date import Optimum'),
        array('letter'  => 'M', 'dbcolumn' => 'dateTraiteUI',   'label' => 'Date ville traitée UI'),
        array('letter'  => 'N', 'dbcolumn' => 'InfosCplt',      'label' => 'Information complémentaires'),
        array('letter'  => 'O', 'dbcolumn' => 'NomCdP',         'label' => 'Chef de projet UI'),
        array('letter'  => 'P', 'dbcolumn' => 'NomCdPUPR',      'label' => 'Chef de projet UPR'),
        array('letter'  => 'Q', 'dbcolumn' => 'NumLot', 'multi' => 'multi',
            'label' => 'Numéro de lot'),
        array('letter'  => 'R', 'dbcolumn' => 'NomSsTrait', 'multi' => 'multi',
            'label' => 'Sous-traitant'),
        array('letter'  => 'S', 'dbcolumn' => 'Type', 'multi' => 'multi',
            'label' => 'Type de pilotage'),
        array('letter'  => 'T', 'dbcolumn' => 'Commentaire', 'multi' => 'multi',
            'label' => 'Commentaire lot'),
        array('letter'  => 'U', 'dbcolumn' => 'Caff', 'multi' => 'multi',
            'label' => 'CAFF')
    );
    
    /**
     * Constructor
     * initializes variables
     * set the properties of the PHP Excel
     * set the header
     */
    public function __construct(){
        require_once '../library/PHPExcel/PHPExcel.php';
        $this->excel = new PHPExcel();
        
        // Set properties
        $this->excel->getProperties()->setCreator("ID Communes");
        $this->excel->getProperties()->setLastModifiedBy("ID Communes");
        $this->excel->getProperties()->setTitle("Export_IDCommunes_".date('d-m-Y'));
        $this->excel->getProperties()->setSubject("Export ID Communes");
        $this->excel->setActiveSheetIndex(0);
        
        // creation of the header
        foreach($this->columns as $col){
            $this->excel->getActiveSheet()->getCell($col['letter'].'1')->setValue($col['label']);
            $this->excel->getActiveSheet()->getStyle($col['letter'].'1')->getFill()
                ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                ->getStartColor()->setARGB('ff8100');
        }
        
        $this->currentRow = 2;
    }
    
    /**
     * Add rows concerning a city in the PHP Excel
     * The array received by argument must be like :
     * Attribute name (key) => attribute value (value)
     * The attribute names are the name in the datebase (see dbname in the $columns variable
     * Multivalued attributes have their values concatenated with chr(0)
     * 
     * @param array $row informations to write in the PHP Excel
     */
    public function addRow(array $row){
        $maxOffset = 0;
        foreach($row as $col => $value){
            $offset = 0;
            // search the column number with the dbname
            foreach($this->columns as $exportCol){
                if($col == $exportCol['dbcolumn']){
                    if(empty($exportCol['multi'])){
                        //if the name of the column has been found, we copy the value in th right cell
                        $this->excel->getActiveSheet()->getCell($exportCol['letter'].$this->currentRow)
                            ->setValue(html_entity_decode($value, null,'UTF-8'));
                    }
                    else{
                        //if the attribute is multivalued, we put the values on different rows
                        $values = explode(chr(0), $value);
                        foreach($values as $v){
                            $this->excel->getActiveSheet()->getCell($exportCol['letter'].($this->currentRow+$offset))
                            ->setValue(html_entity_decode($v, null,'UTF-8'));
                            $offset++;
                        }
                        
                        $maxOffset = $offset > $maxOffset ? $offset : $maxOffset;
                        
                    }
                }
            }
        }
        // max offset is not incremented when the col is not multi
        $this->currentRow += $maxOffset == 0 ? 1 : $maxOffset;
        $this->excel->getActiveSheet()->getStyle('A'.$this->currentRow.':Z'.$this->currentRow)
        ->applyFromArray(array(
            'borders' => array(
                'top' => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
                    'color' => array('argb' => 'ff8100'),
                ),
            ),
        ));
    }
    
    /**
     * Format and save the PHPExcel object in a file (.xlsx)
     * 
     * @param string $fileName name of the file without extension
     * @return path to the file
     */
    public function saveXlsxFile($fileName){
        //auto fit
        $col = 'A';
        while($this->excel->getActiveSheet()->getCell($col.'1')->getValue() != "") {
            $this->excel->getActiveSheet()
            ->getColumnDimension($col)
            ->setAutoSize(true);
            $col++;
        }
        
        //freeze first row and first column
        $this->excel->getActiveSheet()->freezePane('B2');
        
//         $objWriter = new PHPExcel_Writer_Excel2007($this->excel);
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        $objWriter->save(self::EXPORT_DIRECTORY.$fileName.'.xlsx');
        return self::EXPORT_DIRECTORY.$fileName.'.xlsx';
    }
    
}