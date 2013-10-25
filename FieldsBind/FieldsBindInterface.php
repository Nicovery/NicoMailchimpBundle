<?php
namespace Nico\MailchimpBundle\FieldsBind;

/**
* 
*/
interface FieldsBindInterface
{
	/**
	* @return array
	*/
	public function getFieldsBind($data);
}