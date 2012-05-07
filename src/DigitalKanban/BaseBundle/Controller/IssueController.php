<?php

namespace DigitalKanban\BaseBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Response;
use DigitalKanban\BaseBundle\Entity\Issue;
use DigitalKanban\BaseBundle\Entity\Board;
use DigitalKanban\BaseBundle\Form\Type\ArchiveFilterType;

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

        $created = $issue->getCreated()
                         ->format("Y/m/d");

        // Build JSON response data
        $responseData = array(
            'id' => $issue->getId(), 'name' => $issue->getName(), 'created' => $created, 'rotation' => $issue->getRandomRotation(), 'userIsAdmin' => $user->isAdmin()
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

        $created = $issue->getCreated()
                         ->format("Y/m/d");

        // Build JSON response data
        $responseData = array(
                'id' => $issue->getId(), 'name' => $issue->getName(), 'created' => $created, 'rotation' => $issue->getRandomRotation(), 'userIsAdmin' => $user->isAdmin(),
                'duration' => $issue->getDuration()
        );
        $response = new Response(json_encode($responseData), 200);
        $response->headers
                 ->set('Content-Type', 'application/json');

        return $response;

    }

    /**
     * return listing of archived issues
     */
    public function listarchiveAction()
    {

        $request = $this->getRequest();

        $session = $request->getSession();

        $em = $this->getDoctrine()
                   ->getEntityManager();

        // If it is not a ajax request OR the user is not an admin
        // exit here and send a HTTP header 403 Forbidden
        $user = $this->get('security.context')
                     ->getToken()
                     ->getUser();

        if ($request->isXmlHttpRequest() === FALSE || $user->isAdmin() === FALSE) {
            return new Response(NULL, 403);
        }

        // form filtre
        $arch_filtre = $this->getFiltresOptions();

        $form_filtre = $this->getFiltersForm($arch_filtre, $em, $user);

        if ($request->getMethod() == 'POST') {

            $form_filtre->bindRequest($request);

            if ($form_filtre->isValid()) {
                $postdata = $form_filtre->getData();
                foreach ($arch_filtre as $k => $v) {
                    if (array_key_exists($k, $postdata)) {
                        $arch_filtre[$k] = $postdata[$k];
                    }
                }
            }

        }

        $session->set('sess_archive_filters', $arch_filtre);

        $archives = $em->getRepository('DigitalKanbanBaseBundle:Archive')
                       ->getFiltered($user, $arch_filtre);

        $templateData = array(
            'archives' => $archives, 'form_filter' => $form_filtre,
        );

        return $this->render('DigitalKanbanBaseBundle:Board:_archivesdetails.html.twig', $templateData);

    }

    public function exportarchivesAction()
    {

        $request = $this->getRequest();

        $session = $request->getSession();

        $em = $this->getDoctrine()
                   ->getEntityManager();

        // If it is not a ajax request OR the user is not an admin
        // exit here and send a HTTP header 403 Forbidden
        $user = $this->get('security.context')
                     ->getToken()
                     ->getUser();

        if ($user->isAdmin() === FALSE) {
            return new Response(NULL, 403);
        }

        // form filtre
        $arch_filtre = $this->getFiltresOptions();

        $archives = $em->getRepository('DigitalKanbanBaseBundle:Archive')
                       ->getFiltered($user, $arch_filtre);

        $handle = fopen('php://memory', 'r+');
        $header = array(
            'ID', 'Board name', 'Name', 'Group1', 'Group2', 'Group3', 'Duration', 'Archived', 'User'
        );

        fputcsv($handle, $header);

        foreach ($archives as $a) {
            $ret = array();

            $ret[] = $a->getId();
            $ret[] = $a->getBoard()
                       ->getName();

            $tabstr = explode('#', $a->getName());

            if (count($tabstr) > 1) {
                $name = $tabstr[count($tabstr) - 1];
            } else {
                $name = $a->getName();
            }

            $ret[] = $name;
            $ret[] = $a->getGroup1();
            $ret[] = $a->getGroup2();
            $ret[] = $a->getGroup3();
            $ret[] = $a->getDuration();
            $ret[] = $a->getArchived()
                       ->format('Y-m-d H:i:s');
            $ret[] = $a->getArchivedUser();

            fputcsv($handle, $ret);
        }

        rewind($handle);
        $content = stream_get_contents($handle);

        fclose($handle);

        $response = new Response();
        $response->setContent($content);
        $response->headers
                 ->set('Content-Type', 'application/force-download');
        $response->headers
                 ->set('Content-Disposition', 'attachment; filename="export.csv"');

        return $response;
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

    private function getFiltresOptions()
    {
        $options = array(
            'datedeb' => new \DateTime("-30 day"), 'datefin' => new \DateTime(), 'myarchive' => true, 'board' => false,
        );

        // surcharge avec opt de session
        $session = $this->getRequest()
                        ->getSession();

        $sess_archive_filters = $session->get('sess_archive_filters', array());

        foreach ($options as $k => $v) {
            if (array_key_exists($k, $sess_archive_filters)) {
                $options[$k] = $sess_archive_filters[$k];
            }
        }

        return $options;
    }

    private function getFiltersForm($form_filtre_vals, $em, $user)
    {
        return $this->createForm(new ArchiveFilterType(), $form_filtre_vals, array('em' => $em, 'user' => $user));
    }

    /**
     * 
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function filterAction()
    {
        $em = $this->getDoctrine()
                   ->getEntityManager();

        $user = $this->get('security.context')
                     ->getToken()
                     ->getUser();

        // form filtre
        $form_filtre_vals = $this->getFiltresOptions();

        $form_filtre = $this->getFiltersForm($form_filtre_vals, $em, $user);

        return $this->render('DigitalKanbanBaseBundle:Board:showarchive.html.twig', array('form_filter' => $form_filtre->createView(), 'form_filter_vals' => $form_filtre_vals,));

    }

}
