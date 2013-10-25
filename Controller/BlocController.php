<?php

namespace Nico\MailchimpBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;

class BlocController extends Controller
{
	protected $container;
	protected $msgInscriptionOk;
	protected $msgInscriptionNotOk;


	public function __construct(ContainerInterface $container){
		$this->container = $container;
		$this->msgInscriptionOk = 'Votre inscription a été prise en compte.';
		$this->msgInscriptionNotOk = 'Erreur lors de l\'inscription.';
	} 


}
