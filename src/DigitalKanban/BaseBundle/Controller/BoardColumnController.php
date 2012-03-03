<?php

namespace DigitalKanban\BaseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Response;
use \DigitalKanban\BaseBundle\Entity\User;
use \DigitalKanban\BaseBundle\Entity\Board;
use \DigitalKanban\BaseBundle\Entity\BoardColumn;

/**
 * Board column controller
 */
class BoardColumnController extends Controller {

	/**
	 * Add action of board column controller
	 *
	 * Insert a new board column to a single kanban board.
	 * This method is called if the user edit a kanban board and add a new column.
	 *
	 * This is only callable as an ajax request.
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function addAction() {
		$request = $this->getRequest();

			// Get Request data
		$boardId = intval($request->request->get('board'));
		$columnData = $request->request->get('column');

			// If it is not a ajax request
			// exit here and send a HTTP header 403 Forbidden
			// We don`t have to secure this action to only admins, because this is done
			// by the routing mechanism anyway
		if($request->isXmlHttpRequest() === FALSE) {
			return new Response(NULL, 403);
		}

			// Get specific Board by request data board id from database
		$entityManager = $this->getDoctrine()->getEntityManager();
		$board = $entityManager->getRepository('DigitalKanbanBaseBundle:Board')->findOneById($boardId);

			// If there is no board with submitted board id
			// exit here with an error
		if(($board instanceof Board) === FALSE) {
				// It would be better, if we throw an '422 Unprocessable Entity'
				// but this code is an HTTP extension by WebDAV :(
				// With this code we want to say 'Hey, wrong ID'
			return new Response(NULL, 400);
		}

			// Get all columns of this board to raise the sorting
			// This is necessary, because the new column will be inserted at first column
		$columns = $entityManager->getRepository('DigitalKanbanBaseBundle:BoardColumn')->findByBoard($boardId);
        $user_group = $entityManager->getRepository('DigitalKanbanBaseBundle:UserGroup')->findOneById($columnData['usergroup']);

        $this->raiseColumnSorting($columns, $board, -10);

			// Create the new column
		$column = new BoardColumn();
		$column->setName($columnData['name']);
		$column->setMaxIssues($columnData['limit']);
		$column->setSorting(10);
		$column->setBoard($board);
        $column->setUserGroup($user_group);


		$entityManager->persist($column);
		$entityManager->flush();

			// Build and send the response data
		$responseData = array(
			'id' => $column->getId(),
			'name' => $column->getName(),
			'limit' => $column->getMaxIssues(),
            'usergroup' => $column->getUserGroup()->getName(),
		);
		$response = new Response(json_encode($responseData), 200);
		$response->headers->set('Content-Type', 'application/json');

		return $response;
	}

	/**
	 * Update action of board column controller.
	 *
	 * This method is called, if a column was moved.
	 * Columns are movable if the user edit the columns of a kanban board.
	 *
	 * This is only callable as an ajax request.
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function updateAction() {
		$request = $this->getRequest();

			// Get request data
		$boardId = intval($request->request->get('board'));
		$columnIds = explode(',', $request->request->get('columns'));

			// If it is not a ajax request
			// exit here and send a HTTP header 403 Forbidden
			// We don` have to secure this action to only admins, because this is done
			// by the routing mechanism anyway
		if($request->isXmlHttpRequest() === FALSE) {
			return new Response(NULL, 403);
		}

			// Get specific Board by request data board id from database
		$entityManager = $this->getDoctrine()->getEntityManager();
		$board = $entityManager->getRepository('DigitalKanbanBaseBundle:Board')->findOneById($boardId);

			// If there is no board with submitted board id
			// exit here with an error
		if(($board instanceof Board) === FALSE) {
				// It would be better, if we throw an '422 Unprocessable Entity'
				// but this code is an HTTP extension by WebDAV :(
				// With this code we want to say 'Hey, wrong ID'
			return new Response(NULL, 400);
		}

			// Get unsorted columns by column id list from database
		$unsortedColumns = $entityManager->getRepository('DigitalKanbanBaseBundle:BoardColumn')->findById($columnIds);

			// Sort columns from database like columns from request data ($columnIds)
			// It is necessary that the columns have the correct order, because in the next step
			// we will correct the sorting of _ALL_ columns from this board. Why?
			// Because one column was moved and we want clean sorting data in database!
		$sortedColumns = array();
		foreach($columnIds as $columnId) {
			foreach($unsortedColumns as $column) {
				if($column->getId() == $columnId) {
					array_push($sortedColumns, $column);
				}
			}
		}

			// Correct sorting of sorted columns :) and store them back
		$this->raiseColumnSorting($sortedColumns, $board, 0);
		$entityManager->flush();

		return new Response(NULL, 200);
	}

	/**
	 * Delete action of board column controller
	 *
	 * This method is called, if a column was removed.
	 * Columns are removable if the user edit the columns of a kanban board.
	 *
	 * This is only callable as an ajax request.
	 *
	 * @param integer $columnId Database id of column
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function deleteAction($columnId) {
		$request = $this->getRequest();
		$columnId = (int) $columnId;

			// If it is not a ajax request
			// exit here and send a HTTP header 403 Forbidden
			// We don` have to secure this action to only admins, because this is done
			// by the routing mechanism anyway
		if($request->isXmlHttpRequest() === FALSE ) {
			return new Response(NULL, 403);
		}

			// Get specific issue by post data from database
		$entityManager = $this->getDoctrine()->getEntityManager();
		$column = $entityManager->getRepository('DigitalKanbanBaseBundle:BoardColumn')->findOneById($columnId);

			// If there is no BoardColumn with submitted id
			// exit here with an error
		if(($column instanceof BoardColumn) === FALSE) {
				// It would be better, if we throw an '422 Unprocessable Entity'
				// but this code is an HTTP extension by WebDAV :(
				// With this code we want to say 'Hey, wrong ID'
			return new Response(NULL, 400);
		}

			// Delete board column
		$entityManager->remove($column);
		$entityManager->flush();

		return new Response(NULL, 200);
	}

	/**
	 * This method raised the sorting attribute of every board column.
	 * This is called if a new column was created or one column was moved.
	 *
	 * @param array $columns Affected columns
	 * @param Board $board Affected board
	 * @param integer $initialSorting Start sorting value. If the value is negative, the sorting of every columns will used
	 * @return array
	 */
	protected function raiseColumnSorting(array &$columns, Board $board, $initialSorting = 0) {
		$sorting = $initialSorting;
		foreach($columns as $column) {
				// If the sorting is negative, use column sorting
			if($initialSorting < 0) {
				$sorting = $column->getSorting();
			}
			$sorting += 10;
			$column->setBoard($board);
			$column->setSorting($sorting);
		}

		return $columns;
	}

	/**
	 * Checks if the incoming $user has access to the incoming $columnId
	 * to edit this board.
	 *
	 * @param User $user
	 * @param integer $columnId
	 * @param $entityManager
	 * @return bool
	 */
	public function isUserAllowedToEditThisColumn(User $user, $columnId, $entityManager) {
		$returnVal = FALSE;

			// An admin is allowed to edit every board
		if($user->isAdmin() === TRUE) {
			return TRUE;
		}

			// Ask the database if the user has access to this board
		$query = $entityManager->createQuery(
			'SELECT user
			FROM DigitalKanbanBaseBundle:User user
			INNER JOIN user.boards board
			INNER JOIN board.columns boardcolumn
			WHERE
				user.id = :userId
				AND boardcolumn.id = :columnId')
			->setParameters(array(
				'userId' => $user->getId(),
				'columnId' => $columnId
			));

		try {
				// If there is no result, an exception will be thrown
				// and this means, the user has no access to this board
			$query->getSingleResult();
			$returnVal = TRUE;

		} catch (\Doctrine\ORM\NoResultException $eNoResult) {
			// Nothing to do here, because $returnVal is set to FALSE already
		} catch (Exception $e) {
			// Nothing to do here, because $returnVal is set to FALSE already
		}

		return $returnVal;
	}
}