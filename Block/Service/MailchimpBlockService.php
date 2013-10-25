<?php
namespace Nico\MailchimpBundle\Block\Service;

use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Validator\ErrorElement;

use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\BlockBundle\Block\BaseBlockService;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Templating\EngineInterface;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
// use Symfony\Component\Routing\Route;

class MailchimpBlockService extends BaseBlockService{
	protected $container;
	public function __construct($name, EngineInterface $templating,ContainerInterface $container)
	{
		parent::__construct($name, $templating);

		$this->container    = $container;
	}

    /**
	* Valid the settings data
    */
	function validateBlock(ErrorElement $errorElement, BlockInterface $block)
	{
		$errorElement
		->with('settings.liste')
		->assertNotNull(array())
		->assertNotBlank()
		->end()
		->with('template')
		->assertNotNull(array())
		->assertNotBlank()
		->end();
	}

	/**
     * {@inheritdoc}
     */
	public function execute(BlockContextInterface $blockContext, Response $response = null)
	{
	    // merge settings
		$settings =  $blockContext->getSettings();

		$request = $this->container->get('request');

		//when you create a controller you need to bind a ContainerAware Object
		$controller = new Controller();
		$controller->setContainer($this->container);

		$formBuilder = $controller->createFormBuilder();

		$formBuilder
		->add('email','email')
		->add('listeMailchimp','hidden',array(
			'data'=>$settings['liste'],
			));
		$form = $formBuilder->getForm();

		return $this->renderResponse($blockContext->getTemplate(), array(
			'block'     => $blockContext->getBlock(),
			'settings'  => $blockContext->getSettings(),
			'form__mailchimp'      => $form->createView(),
			), $response);
	}


    /**
     * {@inheritdoc}
     * The form that will be displayed in the Admin
     */
    public function buildEditForm(FormMapper $formMapper, BlockInterface $block)
    {
    	$formMapper->add('settings', 'sonata_type_immutable_array', array(
    		'keys' => array(
    			array('liste', 'text', array()),
    			array('template', 'text', array()),
    			)
    		));
    }

    /**
     * {@inheritdoc}
     * The name of the Bloc for the Admin section
     */
    public function getName()
    {
    	return 'Mailchimp (nico)';
    }

    /**
     * {@inheritdoc}
     * Set default value for the settings
     */
    public function setDefaultSettings(OptionsResolverInterface $resolver)
    {
    	$resolver->setDefaults(array(
    		'template' => 'NicoMailchimpBundle:Block:form.html.twig',
    		'liste' => 'XXX',
    		));
    }

}		