<?php

namespace DigitalKanban\BaseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Request;
use DigitalKanban\BaseBundle\Form\Type\UserType;
use DigitalKanban\BaseBundle\Entity\User;

/**
 * User controller
 */
class UserController extends Controller {

	/**
	 * New action of user controller.
	 *
	 * This method is called if a user created a new user.
	 *
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @return \Symfony\Bundle\FrameworkBundle\Controller\RedirectResponse|\Symfony\Bundle\FrameworkBundle\Controller\Response
	 */
	public function newAction(Request $request) {
			// Build form in base of an object
		$form = $this->createForm(new UserType(), new User(), array('mode' => 'new'));

			// If the form was submitted with the HTTP method POST
		if ($request->getMethod() == 'POST') {

			$user = $form->getData();
				// Set password salt BEFORE binding this request to form,
				// because salt is in user entity required and must be set  before the validation starts.
				// Otherwise the validation will failed every time.
			$user->setSalt(md5(uniqid(microtime())));

				// Check if the form was submitted correctly
			$form->bindRequest($request);
			if ($form->isValid()) {
				$entityManager = $this->getDoctrine()->getEntityManager();

					// Add a role to user (Admin or User)
					// This is a special case here, because in formular, the role is a single checkbox
					// but in technical it is a ManyToMany relation
				$requestData = $request->request->get($form->getName());
				if(isset($requestData['admin']) === TRUE && intval($requestData['admin']) === 1) {
					$role = $entityManager->getRepository('DigitalKanbanBaseBundle:Role')->findOneByName('ROLE_ADMIN');

				} else {
					$role = $entityManager->getRepository('DigitalKanbanBaseBundle:Role')->findOneByName('ROLE_USER');
				}
				$user->addRole($role);

				$entityManager->persist($user);
				$entityManager->flush();

					// Build success flash message and redirect to user list
				$flashMessageData = array(
					'title' => 'Creation successful',
					'message' => 'User "' . $user->getFirstName() . ' ' . $user->getLastName() . '" <' . $user->getEmail() . '> was created!',
				);
				$this->get('session')->setFlash('success', $flashMessageData);

				return $this->redirect($this->generateUrl('application_user_list'));
			}
		}

			// Assign form to template
		$templateData = array(
			'form' => $form->createView(),
		);
		return $this->render('DigitalKanbanBaseBundle:User:new.html.twig', $templateData);
	}

	/**
	 * List action of user controller.
	 *
	 * This method is called if the user select the 'User'-section in the frontend.
	 *
	 * @return \Symfony\Bundle\FrameworkBundle\Controller\Response
	 */
	public function listAction() {
			// Get all users and assign them to the template
		$entityManager = $this->getDoctrine()->getEntityManager();
		$users = $entityManager->getRepository('DigitalKanbanBaseBundle:User')->findAll();

		$templateData = array(
			'users' => $users,
		);
		return $this->render('DigitalKanbanBaseBundle:User:list.html.twig', $templateData);
	}

	/**
	 * Edit action of User controller.
	 *
	 * This method is called if the user will edit another user.
	 *
	 * @param integer $id
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @return \Symfony\Bundle\FrameworkBundle\Controller\RedirectResponse|\Symfony\Bundle\FrameworkBundle\Controller\Response
	 */
	public function editAction($id, Request $request) {
		$id = (int) $id;
		$entityManager = $this->getDoctrine()->getEntityManager();
		$user = $entityManager->getRepository('DigitalKanbanBaseBundle:User')->findOneById($id);

			// If there is no user, exit here with an error
		if($user === NULL) {
			$flashMessage = 'User with id "' . $id . '" does not exist. Sorry dude.';
			return $this->redirectToListViewWithError('Editing failed', $flashMessage);
		}

			// Build form
		$form = $this->createForm(new UserType(), $user, array('mode' => 'edit'));

			// If the form was submitted with the HTTP method POST
		if ($request->getMethod() == 'POST') {

				// Check if the form was submitted correctly
			$form->bindRequest($request);
			$user = $form->getData();
			if ($form->isValid()) {
				$entityManager->persist($user);

					// Add a role to user (Admin or User)
					// This is a special case here, because in formular, the role is a single checkbox
					// but in technical it is a ManyToMany relation
				$requestData = $request->request->get($form->getName());
					// Clear all roles before, because technical it is a ManyToMany relation
					// but logical it is an 1:n relation. Every user can has only ONE role.
					// But many user can be administrator OR user.
				$user->getRolesAsArrayCollection()->clear();

				if(isset($requestData['admin']) === TRUE && intval($requestData['admin']) === 1) {
					$role = $entityManager->getRepository('DigitalKanbanBaseBundle:Role')->findOneByName('ROLE_ADMIN');

				} else {
					$role = $entityManager->getRepository('DigitalKanbanBaseBundle:Role')->findOneByName('ROLE_USER');
				}
				$user->addRole($role);
				$entityManager->flush();

					// Build success flash message and redirect to list view
				$flashMessageData = array(
					'title' => 'Editing successful',
					'message' => 'User "' . $user->getFirstName() . ' ' . $user->getLastName() . '" <' . $user->getEmail() . '>  was edited!',
				);
				$this->get('session')->setFlash('success', $flashMessageData);

				return $this->redirect($this->generateUrl('application_user_list'));
			}
		}

			// Assign form data to template
		$templateData = array(
			'form' => $form->createView(),
			'user' => $user
		);
		return $this->render('DigitalKanbanBaseBundle:User:edit.html.twig', $templateData);
	}

	/**
	 * Delete action of user controller.
	 *
	 * This method is called if an user deletes another user.
	 *
	 * @param integer $id
	 * @return \Symfony\Bundle\FrameworkBundle\Controller\RedirectResponse
	 */
	public function deleteAction($id) {
		$id = (int) $id;
		$entityManager = $this->getDoctrine()->getEntityManager();
		$user = $entityManager->getRepository('DigitalKanbanBaseBundle:User')->findOneById($id);

			// If there is no user, exit here with an error
		if($user === NULL) {
			$flashMessage = 'User with id "' . $id . '" does not exist. Sorry dude.';
			return $this->redirectToListViewWithError('Deletion failed', $flashMessage);
		}

			// Build a success flash message
		$flashMessageData = array(
			'title' => 'Deletion successful',
			'message' => 'User "' . $user->getFirstName() . ' ' . $user->getLastName() . '" <' . $user->getEmail() . '>  was deleted!',
		);
		$this->get('session')->setFlash('success', $flashMessageData);

			// Delete the user
		$entityManager->remove($user);
		$entityManager->flush();

		return $this->redirect($this->generateUrl('application_user_list'));
	}

	/**
	 * Redirect the user to the user list view and saves an error flash message into session.
	 *
	 * @param string $title
	 * @param string $message
	 * @return \Symfony\Bundle\FrameworkBundle\Controller\RedirectResponse
	 */
	protected function redirectToListViewWithError($title, $message) {
		$flashMessageData = array(
			'title' => $title,
			'message' => $message,
		);
        $this->get('session')->getFlashBag->add('error', $flashMessageData);
		return $this->redirect($this->generateUrl('application_user_list'));
	}
}