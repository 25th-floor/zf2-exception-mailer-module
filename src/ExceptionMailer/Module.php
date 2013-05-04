<?php
namespace ExceptionMailer;

use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceManager;

class Module
{
	public function onBootstrap(MvcEvent $e)
	{
		// Exception Handling
		$services = $e->getApplication()->getServiceManager();
		$eventManager = $e->getApplication()->getEventManager();
		$eventManager->attach('dispatch.error', function($event) use ($services) {
			/** @var $event MvcEvent */
			if (!$event->isError()) {
				return;
			}
			$exception = $event->getResult()->exception;
			if (!$exception) {
				return;
			}
			$service = $services->get('ExceptionMailer\ErrorHandling');
			$service->mailException($exception, $event->getResult());
		});
	}

    public function getConfig()
    {
	    return include __DIR__ . '/../../config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

	public function getServiceConfig()
	{
		return array(
			'factories' => array(
				'ExceptionMailer\ErrorHandling' =>  function($sm) {
					/** @var ServiceManager $sm */
					$config = $sm->get('config');
					$service = new ErrorHandlingService($config);
					$service->setServiceManager($sm);
					return $service;
				},
			),
		);
	}
}
