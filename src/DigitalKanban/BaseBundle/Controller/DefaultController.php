<?php

namespace DigitalKanban\BaseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Response;

/**
 * Default controller
 */
class DefaultController extends Controller {

	/**
	 * Index action of default controller.
	 *
	 * This methiod is called if the user visit the root page (/).
	 *
	 * @return \Symfony\Bundle\FrameworkBundle\Controller\RedirectResponse
	 */
	public function indexAction() {
		$user = $this->get('security.context')->getToken()->getUser();

			// If there is no user logged in, redirect to login
		if(!is_object($user)) {
			$response = $this->redirect($this->generateUrl('login'));

			// Otherwise to the application start page
		} else {
			$response = $this->redirect($this->generateUrl('application'));
		}

		return $response;
	}

	/**
	 * Login action of default controller.
	 *
	 * This action is called after the login process by Symfony2 is proceeded.
	 *
	 * @return \Symfony\Bundle\FrameworkBundle\Controller\RedirectResponse|\Symfony\Bundle\FrameworkBundle\Controller\Response
	 */
	public function loginAction() {
		$user = $this->get('security.context')->getToken()->getUser();

			// If there always a user logged in, redirect this user to the application start page
		if(is_object($user)) {
			return $this->redirect($this->generateUrl('application'));
		}

		$request = $this->getRequest();
		$session = $request->getSession();

			// Get the login error if there is one
		if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
			$error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);

		} else {
			$error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
		}

			// Assign data to template
		$templateData = array(
			'last_username'	=> $session->get(SecurityContext::LAST_USERNAME),
			'error'			=> $error,
		);
		return $this->render('DigitalKanbanBaseBundle:Default:login.html.twig', $templateData);
	}
}
