<?php

namespace Nico\MailchimpBundle\Configuration;

/**
* 
*/
class MailchimpConfig 
{
    protected $key;

    public function __construct($key){
        $this->key = $key;
    }

    public function getKey(){
        return $this->key;
    }
	
}