<?php
/**
 * Created by 25th-Floor
 * User: tsubera <ts@25th-floor.com>
 * Date: 30.04.13
 */

namespace ExceptionMailer;

use Zend\Config\Config;
use Zend\Log\Logger;
use Zend\Mail\Message;
use Zend\Mail\Transport\Sendmail;
use Zend\Mime\Mime;
use Zend\Mime\Part;
use Zend\ServiceManager\ServiceManager;
use Zend\View\Model\ViewModel;
use Zend\View\Renderer\PhpRenderer;

/**
 * Class ErrorHandlingService
 * @package ExceptionMailer
 * @see http://akrabat.com/zend-framework-2/simple-logging-of-zf2-exceptions/
 */
class ErrorHandlingService {

	/**
	 * @var array
	 */
	protected $config;

	/**
	 * @var ServiceManager
	 */
	protected $sm;

	function __construct(array $config)
	{
		$this->config = $config;
	}

	/**
	 * @param \Zend\ServiceManager\ServiceManager $sm
	 *
	 * @return ErrorHandlingService
	 */
	public function setServiceManager($sm)
	{
		$this->sm = $sm;
		return $this;
	}

	/**
	 * @return \Zend\ServiceManager\ServiceManager
	 */
	public function getServiceManager()
	{
		return $this->sm;
	}

	/**
	 * @param ViewModel $model
	 *
	 * @return \Zend\Mime\Message
	 */
	public function getHtmlBody(ViewModel $model)
	{
		/** @var PhpRenderer $view */
		$view = $this->getServiceManager()->get('ViewRenderer');

		$model = clone($model);
		$model->setTemplate($this->config['exception_mailer']['template']);

		$content = $view->render($model);

		$text = new Part('');
		$text->type = "text/plain";

		$html = new Part($content);
		$html->type = Mime::TYPE_HTML;

		$msg = new \Zend\Mime\Message();
		$msg->setParts(Array($text, $html));
		return $msg;
	}

	/**
	 * @param \Exception $e
	 * @param null $viewModel
	 */
	public function mailException(\Exception $e, $viewModel = null)
	{
		// Mail
		if (!$this->config['exception_mailer']['send']) {
			return;
		}

		$subject = $this->config['exception_mailer']['subject'];
		$sender = $this->config['exception_mailer']['sender'];
		$recipients = $this->config['exception_mailer']['recipients'];

		// no one to send it to
		if (empty($sender) || empty ($recipients)) {
			return;
		}

		$message = new Message();
		$message->addFrom($sender)
			->addTo($recipients)
			->setSubject($subject)
			->setEncoding('UTF-8');

		// check if we should use the template
		if ($this->getServiceManager() !== null
			&& $this->config['exception_mailer']['useTemplate'] == true
			&& $viewModel instanceof ViewModel)
		{
			$message->setBody($this->getHtmlBody($viewModel));
		} else {
			$message->setBody($e->getTraceAsString());
		}

		$transport = new Sendmail();
		$transport->send($message);
	}
}