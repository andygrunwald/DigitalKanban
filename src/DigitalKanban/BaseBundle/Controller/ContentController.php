<?php

namespace DigitalKanban\BaseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use \Symfony\Component\HttpFoundation\Response;

/**
 * Content controller
 */
class ContentController extends Controller {

	/**
	 * About action of content controller.
	 *
	 * This method is called if the user selected the "About"-section in frontend.
	 *
	 * @return \Symfony\Bundle\FrameworkBundle\Controller\Response
	 */
	public function aboutAction() {
		return $this->render('DigitalKanbanBaseBundle:Content:about.html.twig');
	}
}
