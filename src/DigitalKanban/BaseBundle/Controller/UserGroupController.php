<?php
namespace DigitalKanban\BaseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Request;
use DigitalKanban\BaseBundle\Form\Type\UserGroupType;
use DigitalKanban\BaseBundle\Entity\UserGroup;

class UserGroupController extends Controller
{
    /**
     * New action of user group controller.
     *
     * This method is called if a user created a new user group.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Bundle\FrameworkBundle\Controller\RedirectResponse|\Symfony\Bundle\FrameworkBundle\Controller\Response
     */
    public function newAction(Request $request) {
            // Build form in base of an object
       	$form = $this->createForm(new UserGroupType(), new UserGroup(), array('mode' => 'new'));

        if ($request->getMethod() == 'POST') {

      				// Check if the form was submitted correctly
      			$form->bindRequest($request);
      			if ($form->isValid()) {
      				$entityManager = $this->getDoctrine()->getEntityManager();
      				$usergroup = $form->getData();

      					// Assign the selected users this group.
      					// Why we don` add the users to the group? Why we must assign the group to the users?
      					// This is because the way of the used ORM (Doctrine2) handles ManyToMany bidirectional relationships
      					// For more information have a look at
      					// http://www.doctrine-project.org/docs/orm/2.0/en/reference/association-mapping.html#owning-side-and-inverse-side
      				$users = $usergroup->getUsers();
      				foreach($users as $user) {
      					$user->getGroups()->add($usergroup);
      					$entityManager->persist($user);
      				}

      				$entityManager->persist($usergroup);
      				$entityManager->flush();

      					// Build the success message for the user
      				$flashMessageData = array(
      					'title' => 'Creation successful',
      					'message' => 'User group "' . $usergroup->getName() . '" was created!',
      				);
      				$this->get('session')->setFlash('success', $flashMessageData);

      					// Redirect to list view
      				return $this->redirect($this->generateUrl('application_usergroup_list'));
      			}
      		}
        // Assign form to template
        $templateData = array(
            'form' => $form->createView(),
        );
        return $this->render('DigitalKanbanBaseBundle:UserGroup:new.html.twig', $templateData);
    }
    /**
   	 * List action of user group controller.
   	 *
   	 * This method is called if the user select the 'User Groups'-section in the frontend.
   	 *
   	 * @return \Symfony\Bundle\FrameworkBundle\Controller\Response
   	 */
   	public function listAction() {
   			// Get all boards and assign them to the template
   		$entityManager = $this->getDoctrine()->getEntityManager();
   		$groups = $entityManager->getRepository('DigitalKanbanBaseBundle:UserGroup')->findAll();

   		$templateData = array(
   			'groups' => $groups,
   		);
   		return $this->render('DigitalKanbanBaseBundle:UserGroup:list.html.twig', $templateData);
   	}

    /**
     * Delete action of user group.
     *
     * This method is called, if a user group was deleted.
 *
     *
     * @param integer $id
     * @return \Symfony\Bundle\FrameworkBundle\Controller\RedirectResponse
     */
    public function deleteAction($id) {
        $entityManager = $this->getDoctrine()->getEntityManager();
        $id = (int) $id;
        $usergroup = $entityManager->getRepository('DigitalKanbanBaseBundle:UserGroup')->findOneById($id);

            // If there is no board to delete, return with an error to the list view.
        if($usergroup === NULL) {
            $flashMessage = 'User group with id "' . $id . '" does not exist. Sorry dude.';
            return $this->redirectToListViewWithError('Deletion failed', $flashMessage);
        }
        /*$users = $usergroup->getUsers();
        foreach($users as $user) {
            $user->getGroups()->remove($usergroup);
            $entityManager->persist($user);
        }*/
            // Build the success message and save this int session for one request
        $flashMessageData = array(
            'title' => 'Deletion successful',
            'message' => 'User group "' . $usergroup->getName() . '" was deleted!',
        );
        $this->get('session')->setFlash('success', $flashMessageData);

            // Remove the board
        $entityManager->remove($usergroup);
        $entityManager->flush();

        return $this->redirect($this->generateUrl('application_usergroup_list'));
    }
    /**
     * Edit action of User group controller.
     *
     * This method is called if the user will edit another user group.
     *
     * @param integer $id
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Bundle\FrameworkBundle\Controller\RedirectResponse|\Symfony\Bundle\FrameworkBundle\Controller\Response
     */
    public function editAction($id, Request $request) {
        $id = (int) $id;
        $entityManager = $this->getDoctrine()->getEntityManager();
        $usergroup = $entityManager->getRepository('DigitalKanbanBaseBundle:UserGroup')->find($id);

        // If there is no user, exit here with an error
        if($usergroup === NULL) {
            $flashMessage = 'User group with id "' . $id . '" does not exist. Sorry dude.';
            return $this->redirectToListViewWithError('Editing failed', $flashMessage);
        }

            // Build form
        $form = $this->createForm(new UserGroupType(), $usergroup, array('mode' => 'edit'));

            // If the form was submitted with the HTTP method POST
        if ($request->getMethod() == 'POST') {

                // Check if the form was submitted correctly
            $form->bindRequest($request);
            $usergroup = $form->getData();

            if ($form->isValid()) {
                // Get all users which are assigned to the current group and delete this assignment.
                // This is necessary to correct the ManyToMany-Relation, because in the next step
                // we will assign the selected user (from form) to this board.
                // Is there a solution to solve this problem more clever?
                $query = $entityManager->createQuery(
                    'SELECT user
					FROM DigitalKanbanBaseBundle:User user
					INNER JOIN user.groups usergroup
					WHERE
						usergroup.id = :usergroupId')
                    ->setParameters(array(
                    'usergroupId' => $usergroup->getId()
                ));
                $dbUsers = $query->getResult();

                // Loop over assigned user and remove relation
                foreach($dbUsers as $dbUser) {
                    $dbUser->getGroups()->removeElement($usergroup);
                }

                // Assign the selected user and create a new ManyToMany relationship
                $users = $usergroup->getUsers();
                if(count($users) > 0) {
                    foreach($users as $user) {
                        $user->getGroups()->add($usergroup);
                        $entityManager->persist($user);
                    }
                }
                $entityManager->persist($usergroup);
                $entityManager->flush();

                    // Build success flash message and redirect to list view
                $flashMessageData = array(
                    'title' => 'Editing successful',
                    'message' => 'User group ' . $usergroup->getName() . ' was edited!',
                );
                $this->get('session')->setFlash('success', $flashMessageData);

                return $this->redirect($this->generateUrl('application_usergroup_list'));
            }
        }

            // Assign form data to template
        $templateData = array(
            'form' => $form->createView(),
            'usergroup' => $usergroup
        );
        return $this->render('DigitalKanbanBaseBundle:UserGroup:edit.html.twig', $templateData);

    }
}
