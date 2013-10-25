<?php

namespace Nico\MailchimpBundle\Listener;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Nico\MailchimpBundle\Event\SaveFormEvent;
use Nico\MailchimpBundle\Lib\MCAPI;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MailchimpListener
{
	protected $container;
	protected $fieldListId;
	protected $fieldEmail;
	public function __construct(ContainerInterface $container){
		$this->container = $container;
		$this->fieldListId = "listeId";
		$this->fieldEmail = "email";
	}

	public function onSaveToMailchimp(SaveFormEvent $formMailchimpEvent)
	{
		
		$data = $formMailchimpEvent->getData();
		$listId = $this->retrieveListId($data);
		$key = $this->container->get('nico.mailchimp.config')->getKey();
		$email = $this->retrieve($data,$this->fieldEmail);
		$dataMerged = $this->bindDataToMailchimp($data);
		$mailChimp = new MCAPI($key);
		$mailChimp->listSubscribe( $listId, $email,$dataMerged, 'html', false, false, true, false );


		if ($mailChimp->errorCode){
			$msgErreur = "Vous êtes déjà inscrit à notre newsletter.";
			$msg = "\tCode=".$mailChimp->errorCode."\n";
			$msg .= "\tMsg=".$mailChimp->errorMessage."\n";
			if(in_array($this->container->get('kernel')->getEnvironment(), array('test', 'dev'))) {
				$msgErreur.= $msg;
			}
			$this->container->get('nico.error.message')->error($msgErreur);

		} 
	}

	protected function bindDataToMailchimp($data){
		$mailchimpFieldsBind = $this->retrieve($data,'mailchimpFieldsBind');
		$fieldsBind = new $mailchimpFieldsBind;
		return $fieldsBind->getFieldsBind($data);
	}

	protected function retrieveListId($data)
	{
		return $this->retrieve($data,$this->fieldListId);
	}

	private function retrieve($data,$field)
	{
		foreach($data as $key => $val){
			if(is_array($val)){
				$this->retrieveListId($val);
			}else{
				if($key == $field){
					return $val;
				}
			}
		}
		return null;
	}
}