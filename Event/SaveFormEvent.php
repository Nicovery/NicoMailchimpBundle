<?php
namespace Nico\MailchimpBundle\Event;
use Symfony\Component\EventDispatcher\Event;
 
class SaveFormEvent extends Event
{
	protected $data;
	public function __construct($data){
		$this->data = $data;
	}

	public function getData(){
		//the data receveid could be like :
		 // array('formName'=>array(
		 // 	'lastname'=>'value',
		 // 	'firstname'=>'value',
		 // 	....
		 // 	))
		 // We can't now the name of the word so we have to iterate 
		foreach($this->data as $formName => $value){
			if(is_array($value)){
				return $value;
			}else{
				return $this->data;
			}
		}
	}

}