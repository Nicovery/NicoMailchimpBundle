<?php

namespace Nico\MailchimpBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Nico\MailchimpBundle\Lib\MCAPI;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

class MailChimpController extends Controller
{
	protected $msgInscriptionOk;
	protected $msgInscriptionNotOk;

	public function __construct(){
		$this->msgInscriptionOk = 'Votre inscription a été prise en compte.';
		$this->msgInscriptionNotOk = 'Erreur lors de l\'inscription.';
	} 

	public function mailchimpAction(Request $request,$listId,$cle){
		if($request->getMethod()== 'POST'){
			$post = $request->request->all();
			$email = $post['form']['email'];
			$mailChimp = new MCAPI($cle);
			$mailChimp->listSubscribe( $listId, $email,array(), 'html', false, false, true, false );
			if ($mailChimp->errorCode){
				$msg = $this->msgInscriptionNotOk;
				$msg .= "\tCode=".$mailChimp->errorCode."\n";
				$msg .= "\tMsg=".$mailChimp->errorMessage."\n";
				$this->get('nico.error.message')->error($this->msgInscriptionNotOk);
			} else {
				$this->get('nico.error.message')->success($this->msgInscriptionOk);
			}


		}
	}

	public function newsletterBlockAction(Request $request)
	{
		$response = new Response();
		$response->setStatusCode(200);
		$response->headers->set('Content-Type', 'application/json');
		$responseMessage = array();
		if($request->isXmlHttpRequest()){
			$post = $request->request->all();
			$email = $post['form']['email'];
			$listId = $post['form']['listeMailchimp'];
			$key = $this->get('nico.mailchimp.config')->getKey();
			$mailChimp = new MCAPI($key);
			$mailChimp->listSubscribe( $listId, $email,array(), 'html', false, false, true, false );


			if ($mailChimp->errorCode){
				$msg = $this->msgInscriptionNotOk;
				$msg .= "\tCode=".$mailChimp->errorCode."\n";
				$msg .= "\tMsg=".$mailChimp->errorMessage."\n";
				$responseMessage = array("error"=>$mailChimp->errorMessage);

			} else {
				$responseMessage = array("success"=>'1');
			}
		}else{
			$responseMessage = array("error"=>'Not ajax request');
		}	

		$encoders = array( new JsonEncoder());
		$normalizers = array(new GetSetMethodNormalizer());
		$serializer = new Serializer($normalizers, $encoders);
		$json = $serializer->serialize($responseMessage, 'json');
		$response->setContent($json);

		return $response;
	}

	//save the data to a mailchimp list
	//$data : array. the data field to save and the correspondence ex: data['name'] => 'FNAME'
	public function dataToMailChimp($data,$listId,$cle){
		if(!empty($data)){

		}
	}
}
