<?php
require_once(FCPATH."procesos.php");
require_once('authorization.php');
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class GuiaTelefono extends MY_Controller {
 
    	public function __construct() {
        	parent::__construct();
    	}
	
	//Vista para el generador
    	public function consultar(){
		
		$this->verifyLogin();
		
		$json_ws = apcu_fetch('json_list_users_guia_telefono');
        	if (!$json_ws){
                
                	$url = urlapi . "users/list/small/phone";
                	$json_ws = $this->conectUrl($url);//json_decode($result);
                	apcu_add('json_list_users_guia_telefono',$json_ws,120);
        	}

      		$data['json_list_users'] = $json_ws;		
                $data['sidebar']='guia_telefono';
                $data['content'] = 'guiaTelefono/consultar';
                $this->load->view('template', $data);
	}
	
	public function email(){
        	$this->verifyLogin();	
		$data['sidebar'] ='guia_telefono_mail';
                $data['content'] = 'guiaTelefono/emailphone';
                $this->load->view('template', $data);
	}
	
	public function email_report(){
                
		$this->verifyLogin();

		$chbox = $this->input->get('checkBox');
		
		$email_ch="false";
            	$phone_ch="false";
            	$cont = count($chbox);
            	for($i = 0; $i <$cont ; $i++){
                	if($chbox[$i] == "e")
                        	$email_ch="true";
                	if($chbox[$i]=="p")
                        	$phone_ch="true";   
            	}
			
		$url = urlapi . "email/phone/report?email=".$email_ch."&phone=".$phone_ch;
		
		$json_ws = $this->conectUrl($url);
		
		$CI =& get_instance();
            	$CI->load->library('Excel');
            	$object = new PHPExcel();
	    	
		/** FIRST SHEET : COLABORADORES **/
	    	$objWorkSheet = $object->createSheet(0);
            	$objWorkSheet = $object->setActiveSheetIndex(0);
            	$objWorkSheet->setTitle("Colaboradores");
		
		$column = 0;
     	   
            	$table_columns = array("Rut","Nombre completo","Cargo","Localidad");
           	foreach($table_columns as $field){
                    $object->getActiveSheet()->setCellValueByColumnAndRow($column,1, $field);
                    $column++;
            	}
		
		if($email_ch=="true"){
			$object->getActiveSheet()->setCellValueByColumnAndRow($column,1, "Correo");
			$column++;
		}
		if($phone_ch=="true"){
                        $object->getActiveSheet()->setCellValueByColumnAndRow($column,1, "Codigo Area");
                        $column++;
			$object->getActiveSheet()->setCellValueByColumnAndRow($column,1, "Anexo");
			$column++;
                        $object->getActiveSheet()->setCellValueByColumnAndRow($column,1, "Celular");
                }


		$worker_list = $json_ws->listWorkers;
		$excel_row = 2;
		foreach ($worker_list  as $json){
			$rut    	= $json->rut;
			$full_name	= $json->lastName.' '.$json->name;			
			$position     	= $json->position;
			$location	= $json->location;
				
			$email		= ($email_ch=="true")?$json->email:"";
			$code		="";
			$anexo		="";
			$cellphone	="";
			if($phone_ch=="true"){
				$code = $json->areaCode;
				$anexo= $json->annexPhone;
				$cellphone= $json->phone;
			}
			
			$object->getActiveSheet()->setCellValueByColumnAndRow(0,$excel_row,$rut);
			$object->getActiveSheet()->setCellValueByColumnAndRow(1,$excel_row,$full_name);
			$object->getActiveSheet()->setCellValueByColumnAndRow(2,$excel_row,$position);
			$object->getActiveSheet()->setCellValueByColumnAndRow(3,$excel_row,$location);
			
			if($email_ch=="true")
				$object->getActiveSheet()->setCellValueByColumnAndRow(4,$excel_row,$email);
			if($email_ch=="true"){
				$object->getActiveSheet()->setCellValueByColumnAndRow(5,$excel_row,$code);
				$object->getActiveSheet()->setCellValueByColumnAndRow(6,$excel_row,$anexo);
				$object->getActiveSheet()->setCellValueByColumnAndRow(7,$excel_row,$cellphone);			
			}else{
				$object->getActiveSheet()->setCellValueByColumnAndRow(4,$excel_row,$code);
                                $object->getActiveSheet()->setCellValueByColumnAndRow(5,$excel_row,$anexo);
                                $object->getActiveSheet()->setCellValueByColumnAndRow(6,$excel_row,$cellphone);
			}

			$excel_row++;
		}
		
		$style_border = array(
                        'borders' => array(
                                'outline' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
			)
            	);


		//Style sheet 
		//Align titles
		$object->getActiveSheet()->getStyle('A1:Z1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		//BOLD
            	$object->getActiveSheet()->getStyle('A1:AZ1')->getFont()->setBold( true );
		
		$object->getActiveSheet()->getColumnDimension('A')->setWidth(13);
            	$object->getActiveSheet()->getColumnDimension('B')->setWidth(35);
            	$object->getActiveSheet()->getColumnDimension('C')->setWidth(50);
		$object->getActiveSheet()->getColumnDimension('D')->setWidth(27);		
		if($email_ch=="true")
			$object->getActiveSheet()->getColumnDimension('E')->setWidth(24);		
		$object->getActiveSheet()->getColumnDimension('G')->setWidth(12);
		$object->getActiveSheet()->getColumnDimension('H')->setWidth(12);		
		
		
		if($email_ch=="true"){
		
			/** SECOND SHEET : SERVICES **/
			$objWorkSheet = $object->createSheet(1);
            		$objWorkSheet = $object->setActiveSheetIndex(1);
            		$objWorkSheet->setTitle("Servicios");
	
			$object->getActiveSheet()->setCellValueByColumnAndRow(0,1, "Correo");

			$services_list = $json_ws->listServices;
			$excel_row = 2;
                	foreach ($services_list  as $json){
				$object->getActiveSheet()->setCellValueByColumnAndRow(0,$excel_row,$json);
				$excel_row++;
			}
			 //Align titles
                	$object->getActiveSheet()->getStyle('A1:Z1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                	//BOLD
                	$object->getActiveSheet()->getStyle('A1:AZ1')->getFont()->setBold( true );
		
                	$object->getActiveSheet()->getColumnDimension('A')->setWidth(27);		
		
			/** THIRD SHEET : GERENTES **/
                	$objWorkSheet = $object->createSheet(2);
                	$objWorkSheet = $object->setActiveSheetIndex(2);
                	$objWorkSheet->setTitle("Gerentes");
		
			$object->getActiveSheet()->setCellValueByColumnAndRow(0,1, "Nombre");
                	$object->getActiveSheet()->setCellValueByColumnAndRow(1,1, "Correo");

                	$managment_list = $json_ws->listManagment;
                	$excel_row = 2;
                	foreach ($managment_list  as $json){
				$name = $json->name;
				$email= $json->email;
			
				$object->getActiveSheet()->setCellValueByColumnAndRow(0,$excel_row,$name);
                        	$object->getActiveSheet()->setCellValueByColumnAndRow(1,$excel_row,$email);
                        	$excel_row++;
                	}
                	 //Align titles
                	$object->getActiveSheet()->getStyle('A1:Z1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                	//BOLD
                	$object->getActiveSheet()->getStyle('A1:AZ1')->getFont()->setBold( true );
		
			//SIZE	
			$object->getActiveSheet()->getColumnDimension('A')->setWidth(35);
                	$object->getActiveSheet()->getColumnDimension('B')->setWidth(27);

			/** FOURTH SHEET : DIRECTORES **/
                	$objWorkSheet = $object->createSheet(3);
                	$objWorkSheet = $object->setActiveSheetIndex(3);
                	$objWorkSheet->setTitle("Directores");

                	$object->getActiveSheet()->setCellValueByColumnAndRow(0,1, "Nombre");
                	$object->getActiveSheet()->setCellValueByColumnAndRow(1,1, "Correo");

                	$director_list = $json_ws->listDirector;
                	$excel_row = 2;
                	foreach ($director_list  as $json){
                        	$name = $json->name;
                        	$email= $json->email;

                        	$object->getActiveSheet()->setCellValueByColumnAndRow(0,$excel_row,$name);
                        	$object->getActiveSheet()->setCellValueByColumnAndRow(1,$excel_row,$email);
                        	$excel_row++;
                	}
                	//Align titles
                	$object->getActiveSheet()->getStyle('A1:Z1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                	//BOLD
                	$object->getActiveSheet()->getStyle('A1:AZ1')->getFont()->setBold( true );

                	//SIZE  
                	$object->getActiveSheet()->getColumnDimension('A')->setWidth(35);
                	$object->getActiveSheet()->getColumnDimension('B')->setWidth(27);
			

			//Delete Sheet default 
			$object->removeSheetByIndex(4);
		}
		else{
			//Delete Sheet default 
                        $object->removeSheetByIndex(1);
		}

		$title = date("d-m-Y");
            	$object_writer = PHPExcel_IOFactory::createWriter($object, 'Excel5');
            	header('Content-Type: application/vnd.ms-excel');
            	header('Content-Disposition: attachment;filename="reporte_email_'.$title.'".xls"');
            	$object_writer->save('php://output');
        }	


        public function verifyLogin(){
                //Verificamos que el usuario ya se haya logeado 
                if (!UsuarioSesion::usuario()->registrado) {
                        $this->session->set_flashdata('redirect', current_url());
                        redirect('tramites/disponibles');
                }
	}


	public function conectUrl($url){
		$oa = new Authorization();
        $token = $oa->getToken();
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "Authorization: Bearer ".$token
         ));
        $result=curl_exec($ch);
        curl_close($ch);
        return json_decode($result);
        }

}
