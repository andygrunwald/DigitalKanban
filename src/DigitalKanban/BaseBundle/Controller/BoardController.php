<?php

namespace DigitalKanban\BaseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use DigitalKanban\BaseBundle\Form\Type\BoardType;
use DigitalKanban\BaseBundle\Entity\Board;
use DigitalKanban\BaseBundle\Entity\BoardColumn;

/**
 * Board controller
 */
class BoardController extends Controller {

	/**
	 * Show action of board controller.
	 *
	 * Display a given kanban board to the user with the full function.
	 * The most of the functions are build with javascript.
	 *
	 * @param integer $id
	 * @return \Symfony\Bundle\FrameworkBundle\Controller\Response
	 */
	public function showAction($id) {
		$entityManager = $this->getDoctrine()->getEntityManager();
		$id = (int) $id;

			// Select all information in one query
			// Why in one query and not through the models with getter functions?
			// Because the models would be send about eight queries and this solution only one.
		$query = $entityManager->createQuery(
			'SELECT board, boardcolumn, issue
			FROM DigitalKanbanBaseBundle:Board board
			LEFT JOIN board.columns boardcolumn
			LEFT JOIN boardcolumn.issues issue
			WHERE board.id = :boardId
			ORDER BY boardcolumn.sorting ASC, issue.sorting ASC')
			->setParameter('boardId', $id);

		$templateData = array(
			'board' => $query->getSingleResult(),
		);
		return $this->render('DigitalKanbanBaseBundle:Board:show.html.twig', $templateData);
	}

	/**
	 * Edit column action of board controller.
	 *
	 * This method show a kanban board to edit the columns.
	 * The most of the functions are build with javascript.
	 *
	 * @param integer $id
	 * @return \Symfony\Bundle\FrameworkBundle\Controller\Response
	 */
	public function editColumnsAction($id) {
		$entityManager = $this->getDoctrine()->getEntityManager();
		$id = (int) $id;

			// Select all information in one query
			// Why in one query and not with the models? Because the models
			// would be send about 8 queries and this solution only one.
		$query = $entityManager->createQuery(
			'SELECT board, boardcolumn, issue
			FROM DigitalKanbanBaseBundle:Board board
			LEFT JOIN board.columns boardcolumn
			LEFT JOIN boardcolumn.issues issue
			WHERE board.id = :boardId
			ORDER BY boardcolumn.sorting ASC, issue.sorting ASC')
			->setParameter('boardId', $id);

		$templateData = array(
			'board' => $query->getSingleResult(),
		);
		return $this->render('DigitalKanbanBaseBundle:Board:editColumns.html.twig', $templateData);
	}

	/**
	 * Delete action of board controller.
	 *
	 * This method is called, if a board was deleted.
	 * If a board has columns with / without issues, there will be deleted, too.
	 * The delete process of columns and issues will be handled through the database.
	 *
	 * @param integer $id
	 * @return \Symfony\Bundle\FrameworkBundle\Controller\RedirectResponse
	 */
	public function deleteAction($id) {
		$entityManager = $this->getDoctrine()->getEntityManager();
		$id = (int) $id;
		$board = $entityManager->getRepository('DigitalKanbanBaseBundle:Board')->findOneById($id);

			// If there is no board to delete, return with an error to the list view.
		if($board === NULL) {
			$flashMessage = 'Kanban board with id "' . $id . '" does not exist. Sorry dude.';
			return $this->redirectToListViewWithError('Deletion failed', $flashMessage);
		}

			// Build the success message and save this int session for one request
		$flashMessageData = array(
			'title' => 'Deletion successful',
			'message' => 'Kanban board "' . $board->getName() . '" was deleted!',
		);
		$this->get('session')->setFlash('success', $flashMessageData);

			// Remove the board
		$entityManager->remove($board);
		$entityManager->flush();

		return $this->redirect($this->generateUrl('application_board_list'));
	}

	/**
	 * New action of board controller.
	 *
	 * This method is called if the user create a new kanban board.
	 *
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @return \Symfony\Bundle\FrameworkBundle\Controller\RedirectResponse|\Symfony\Bundle\FrameworkBundle\Controller\Response
	 */
	public function newAction(Request $request) {
			// Create form in base of an object
		$form = $this->createForm(new BoardType(), new Board());

			// If the form was submitted with the HTTP method POST
		if ($request->getMethod() == 'POST') {

				// Check if the form was submitted correctly
			$form->bindRequest($request);
			if ($form->isValid()) {
				$entityManager = $this->getDoctrine()->getEntityManager();
				$board = $form->getData();

					// Assign the selected users this board.
					// Why we don` add the users to the board? Why we must assign the board to the users?
					// This is because the way of the used ORM (Doctrine2) handles ManyToMany bidirectional relationships
					// For more information have a look at
					// http://www.doctrine-project.org/docs/orm/2.0/en/reference/association-mapping.html#owning-side-and-inverse-side
				$users = $board->getUsers();
				foreach($users as $user) {
					$user->getBoards()->add($board);
					$entityManager->persist($user);
				}

				$entityManager->persist($board);
				$entityManager->flush();

					// Build the success message for the user
				$flashMessageData = array(
					'title' => 'Creation successful',
					'message' => 'Kanban board "' . $board->getName() . '" was created!',
				);
				$this->get('session')->setFlash('success', $flashMessageData);

					// Redirect to list view
				return $this->redirect($this->generateUrl('application_board_list'));
			}
		}

		$templateData = array(
			'form' => $form->createView(),
		);
		return $this->render('DigitalKanbanBaseBundle:Board:new.html.twig', $templateData);
	}

	/**
	 * Edit action of the board controller.
	 *
	 * This method is called, if the user edit the name or description of a kanban board.
	 *
	 * @param integer $id
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @return \Symfony\Bundle\FrameworkBundle\Controller\RedirectResponse|\Symfony\Bundle\FrameworkBundle\Controller\Response
	 */
	public function editAction($id, Request $request) {
		$id = (int) $id;
		$entityManager = $this->getDoctrine()->getEntityManager();
		$board = $entityManager->getRepository('DigitalKanbanBaseBundle:Board')->findOneById($id);

			// If there is no board to edit, return with an error to the list view.
		if($board === NULL) {
			$flashMessage = 'Kanban board with id "' . $id . '" does not exist. Sorry dude.';
			return $this->redirectToListViewWithError('Editing failed', $flashMessage);
		}

			// Create form in base of an object
		$form = $this->createForm(new BoardType(), $board);

			// If the form was submitted with the HTTP method POST
		if ($request->getMethod() == 'POST') {

				// Check if the form was submitted correctly
			$form->bindRequest($request);
			if ($form->isValid()) {

					// Get all users which are assigned to the current board and delete this assignment.
					// This is necessary to correct the ManyToMany-Relation, because in the next step
					// we will assign the selected user (from form) to this board.
					// Is there a solution to solve this problem more clever?
					// If yes, let me (Andy, <andreas.grunwald@wmdb.de> or andygrunwald on GitHub) know. Thanks!
				$query = $entityManager->createQuery(
					'SELECT user
					FROM DigitalKanbanBaseBundle:User user
					INNER JOIN user.boards board
					WHERE
						board.id = :boardId')
					->setParameters(array(
						'boardId' => $board->getId()
					));
				$dbUsers = $query->getResult();

					// Loop over assigned user and remove relation
				foreach($dbUsers as $dbUser) {
					$dbUser->getBoards()->removeElement($board);
				}

					// Assign the selected user and create a new ManyToMany relationship
				$users = $board->getUsers();
				if(count($users) > 0) {
					foreach($users as $user) {
						$user->getBoards()->add($board);
						$entityManager->persist($user);
					}
				}

				$entityManager->persist($board);
				$entityManager->flush();

					// Build the success message for the user
				$flashMessageData = array(
					'title' => 'Editing successful',
					'message' => 'Kanban board "' . $board->getName() . '" was edited!',
				);
				$this->get('session')->setFlash('success', $flashMessageData);

					// Redirect to list view
				return $this->redirect($this->generateUrl('application_board_list'));
			}
		}

		$templateData = array(
			'form' => $form->createView(),
			'board' => $board
		);
		return $this->render('DigitalKanbanBaseBundle:Board:edit.html.twig', $templateData);
	}

	/**
	 * List action of board controller.
	 *
	 * This method is called if the user select the 'Board'-section in the frontend.
	 *
	 * @return \Symfony\Bundle\FrameworkBundle\Controller\Response
	 */
	public function listAction() {
			// Get all boards and assign them to the template
		$entityManager = $this->getDoctrine()->getEntityManager();
		$boards = $entityManager->getRepository('DigitalKanbanBaseBundle:Board')->findAll();

		$templateData = array(
			'boards' => $boards,
		);
		return $this->render('DigitalKanbanBaseBundle:Board:list.html.twig', $templateData);
	}

	/**
	 * Update action of board controller.
	 *
	 * This method is called, if a user moved an issue on a kanban board to another place like another column.
	 *
	 * This is only callable as an ajax request.
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function updateAction() {
		$request = $this->getRequest();

			// Get request data
		$columnId = intval($request->request->get('column'));
		$issueIds = explode(',', $request->request->get('issues'));

			// If it is not a ajax request OR
			// if the user is not allowed to edit this column
			// exit here and send a HTTP header 403 Forbidden
		$user = $this->get('security.context')->getToken()->getUser();
		$boardColumnController = new BoardColumnController();
		$entityManager = $this->getDoctrine()->getEntityManager();
		if($request->isXmlHttpRequest() === FALSE || $boardColumnController->isUserAllowedToEditThisColumn($user, $columnId, $entityManager) === FALSE) {
			return new Response(NULL, 403);
		}

			// Get specific BoardColumn by request data column id from database
		$column = $entityManager->getRepository('DigitalKanbanBaseBundle:BoardColumn')->findOneById($columnId);

			// If there is no BoardColumn with submitted id
			// exit here with an error
		if(($column instanceof BoardColumn) === FALSE) {
				// It would be better, if we throw an '422 Unprocessable Entity'
				// but this code is an HTTP extension by WebDAV :(
				// With this code we want to say 'Hey, wrong ID'
			return new Response(NULL, 400);
		}

			// Get unsorted issues by issue id list from database
		$unsortedIssues = $entityManager->getRepository('DigitalKanbanBaseBundle:Issue')->findById($issueIds);

			// Sort issues from database like issues from request data ($issueIds)
			// It is necessary that the issues have the correct order, because in the next step
			// we will correct the sorting of _ALL_ issues from this board. Why?
			// Because one issue was moved and we want clean sorting data in database!
		$sortedIssues = array();
		foreach($issueIds as $issueId) {
			foreach($unsortedIssues as $issue) {
				if($issue->getId() == $issueId) {
					array_push($sortedIssues, $issue);
				}
			}
		}

        // @todo last edited user!!!

			// Correct sorting of sorted issues :) and store them back
		$sorting = 0;
		foreach($sortedIssues as $issue) {
			$sorting += 10;
			$issue->setBoardColumn($column);
			$issue->setSorting($sorting);
		}
		$entityManager->flush();

		return new Response(NULL, 200);
	}

	/**
	 * Redirect the user to the kanban board list view and saves an error flash message into session.
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
		return $this->redirect($this->generateUrl('application_board_list'));
	}
}
