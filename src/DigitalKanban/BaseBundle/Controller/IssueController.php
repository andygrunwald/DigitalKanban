<?php

namespace DigitalKanban\BaseBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Response;
use DigitalKanban\BaseBundle\Entity\Issue;
use DigitalKanban\BaseBundle\Entity\Board;

/**
 * Issue controller
 */
class IssueController extends Controller
{

    /**
     * Update all issues of one column.
     * Renew the sorting of them.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */

    /**
     * Add action of issue controller.
     *
     * This action is called if the user creates a new issue.
     *
     * This is only callable as an ajax request.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAction()
    {
        $request = $this->getRequest();

        // Get request data
        $columnId = intval($request->request
                                   ->get('column'));
        $postIssueData = $request->request
                                 ->get('issue');

        // If it is not a ajax request OR
        // if the user is not allowed to edit this column
        // exit here and send a HTTP header 403 Forbidden
        $user = $this->get('security.context')
                     ->getToken()
                     ->getUser();
        $boardColumnController = new BoardColumnController();
        $entityManager = $this->getDoctrine()
                              ->getEntityManager();
        if ($request->isXmlHttpRequest() === FALSE || $boardColumnController->isUserAllowedToEditThisColumn($user, $columnId, $entityManager) === FALSE) {
            return new Response(NULL, 403);
        }

        // Get the highest sorting of a single issue at this column
        // This is necessary, because the new created issue will be inserted as last item
        $highestSorting = $this->getHighestSortingOfAColumn($columnId, $entityManager);

        // Get specific BoardColumn by request data column id from database
        $column = $entityManager->getRepository('DigitalKanbanBaseBundle:BoardColumn')
                                ->findOneById($columnId);

        // If there is no column with submitted column id
        // exit here with an error
        if ($column === NULL) {
            // It would be better, if we throw an '422 Unprocessable Entity'
            // but this code is an HTTP extension by WebDAV :(
            // With this code we want to say 'Hey, wrong ID'
            return new Response(NULL, 400);
        }

        // Create new issue and store it
        $issue = new Issue();
        $issue->setName($postIssueData['title']);

        //manage groups based on # separator
        $tabstr = explode('#', $postIssueData['title']);

        if (array_key_exists(1, $tabstr)) {
            $issue->setGroup1($tabstr[0]);
        }

        if (array_key_exists(2, $tabstr)) {
            $issue->setGroup2($tabstr[1]);
        }

        if (array_key_exists(3, $tabstr)) {
            $issue->setGroup1($tabstr[2]);
        }

        $issue->setSorting(($highestSorting + 10));
        $issue->setBoardColumn($column);
        $issue->setCreatedUser($user);
        $issue->setLastEditedUser($user);

        $entityManager->persist($issue);
        $entityManager->flush();

        // Build JSON response data
        $responseData = array(
            'id' => $issue->getId(), 'name' => $issue->getName(), 'rotation' => $issue->getRandomRotation(), 'userIsAdmin' => $user->isAdmin()
        );
        $response = new Response(json_encode($responseData), 200);
        $response->headers
                 ->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * Delete action of issue controller.
     *
     * This method is called if a user deletes an issue.
     *
     * This is only callable as an ajax request.
     *
     * @param $issueId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction($issueId)
    {
        $request = $this->getRequest();

        // If it is not a ajax request OR the user is not an admin
        // exit here and send a HTTP header 403 Forbidden
        $user = $this->get('security.context')
                     ->getToken()
                     ->getUser();
        if ($request->isXmlHttpRequest() === FALSE || $user->isAdmin() === FALSE) {
            return new Response(NULL, 403);
        }

        // Get specific issue by request data from database
        $entityManager = $this->getDoctrine()
                              ->getEntityManager();
        $issue = $entityManager->getRepository('DigitalKanbanBaseBundle:Issue')
                               ->findOneById($issueId);

        // The issue could be only deleted if there is one
        if ($issue) {
            $entityManager->remove($issue);
            $entityManager->flush();
        }

        return new Response(NULL, 200);
    }

    /**
     * Edit action of issue controller.
     *
     * This method is called if a user edits an issue.
     *
     * This is only callable as an ajax request.
     *
     * @param $issueId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction($issueId)
    {
        $request = $this->getRequest();

        // If it is not a ajax request OR the user is not an admin
        // exit here and send a HTTP header 403 Forbidden
        $user = $this->get('security.context')
                     ->getToken()
                     ->getUser();
        if ($request->isXmlHttpRequest() === FALSE || $user->isAdmin() === FALSE) {
            return new Response(NULL, 403);
        }

        // Get specific issue by request data from database
        $entityManager = $this->getDoctrine()
                              ->getEntityManager();

        $issue = $entityManager->getRepository('DigitalKanbanBaseBundle:Issue')
                               ->findOneById($issueId);

        return $this->render('DigitalKanbanBaseBundle:Board:issueedit.html.twig', array('issue' => $issue));

    }

    /**
     * Save action of issue controller.
     *
     * This method is called if a user edits an issue.
     *
     * This is only callable as an ajax request.
     *
     * @param $issueId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function saveAction($issueId)
    {
        $request = $this->getRequest();

        // If it is not a ajax request OR the user is not an admin
        // exit here and send a HTTP header 403 Forbidden
        $user = $this->get('security.context')
                     ->getToken()
                     ->getUser();
        if ($request->isXmlHttpRequest() === FALSE || $user->isAdmin() === FALSE) {
            return new Response(NULL, 403);
        }

        // Get specific issue by request data from database
        $entityManager = $this->getDoctrine()
                              ->getEntityManager();

        $issue = $entityManager->getRepository('DigitalKanbanBaseBundle:Issue')
                               ->findOneById($issueId);

        $postIssueData = $request->request
                                 ->get('issue');

        $issue->setName($postIssueData['title']);
        $issue->setDuration($postIssueData['duration']);

        $entityManager->persist($issue);
        $entityManager->flush();

        // Build JSON response data
        $responseData = array(
            'id' => $issue->getId(), 'name' => $issue->getName(), 'rotation' => $issue->getRandomRotation(), 'userIsAdmin' => $user->isAdmin(), 'duration' => $issue->getDuration()
        );
        $response = new Response(json_encode($responseData), 200);
        $response->headers
                 ->set('Content-Type', 'application/json');

        return $response;

    }

    /**
     * return listing of archived issues
     */
    public function listarchiveAction($boardId)
    {

        $request = $this->getRequest();

        // If it is not a ajax request OR the user is not an admin
        // exit here and send a HTTP header 403 Forbidden
        $user = $this->get('security.context')
                     ->getToken()
                     ->getUser();
        if ($request->isXmlHttpRequest() === FALSE || $user->isAdmin() === FALSE) {
            return new Response(NULL, 403);
        }

        $entityManager = $this->getDoctrine()
                              ->getEntityManager();

        $board = $entityManager->getRepository('DigitalKanbanBaseBundle:Board')
                               ->findOneById($boardId);

        // If there is no board with submitted board id
        // exit here with an error
        if (($board instanceof Board) === FALSE) {
            // It would be better, if we throw an '422 Unprocessable Entity'
            // but this code is an HTTP extension by WebDAV :(
            // With this code we want to say 'Hey, wrong ID'
            return new Response(NULL, 400);
        }

        $archives = $entityManager->getRepository('DigitalKanbanBaseBundle:Archive')
                                  ->findBy(array('board' => $board->getId()));

        $templateData = array(
            'archives' => $archives,
        );

        return $this->render('DigitalKanbanBaseBundle:Board:showarchive.html.twig', $templateData);
    }

    /**
     * Returns the highest sorting of all issues in one column.
     * For example, if there is a column of a kanban board with six issues and the database is very clean,
     * the highest sorting will be 60, because the sorting steps are 10.
     *
     * @param integer $columnId
     * @param $entityManager
     * @return integer
     */
    protected function getHighestSortingOfAColumn($columnId, $entityManager)
    {
        $highestSorting = 0;

        // Select a single issue with the highest sorting in a column
        $query = $entityManager->createQuery('SELECT issue
			FROM DigitalKanbanBaseBundle:Issue issue
			WHERE
				issue.boardColumn = :columnId
			ORDER BY issue.sorting DESC')
                               ->setParameters(array('columnId' => $columnId))
                               ->setMaxResults(1);

        try {
            // If there is no result, an exception will be thrown
            // and this means, there are no issues in this column
            $issue = $query->getSingleResult();
            $highestSorting = $issue->getSorting();

        } catch (\Doctrine\ORM\NoResultException $eNoResult) {
            // Nothing to do here, because $highestStorting is set to 0 already
        } catch (Exception $e) {
            // Nothing to do here, because $highestStorting is set to 0 already
        }

        return $highestSorting;
    }
}
