<?php
namespace DigitalKanban\BaseBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use DigitalKanban\BaseBundle\Entity\Board;
use DigitalKanban\BaseBundle\Entity\BoardColumn;
use DigitalKanban\BaseBundle\Entity\Issue;
use DigitalKanban\BaseBundle\Entity\Role;
use DigitalKanban\BaseBundle\Entity\User;

/**
 * Fixture loader
 *
 * This class generate dummy data for this application.
 * How to execute this class and fill the database? Call the following command in your terminal:
 * 		php app/console doctrine:fixtures:load
 */
class FixtureLoader implements FixtureInterface {

	public function load(ObjectManager $manager) {

		/**
		 * ROLES
		 */

		// Create the ROLE_ADMIN role
		$adminRole = new Role();
		$adminRole->setName('ROLE_ADMIN');
		$adminRole->setDescription('Administration. This group is focused on system management.');

		$manager->persist($adminRole);

        // Create the ROLE_MANAGER role
		$managerRole = new Role();
		$managerRole->setName('ROLE_MANAGER');
		$managerRole->setDescription('Manager. This group is focused on system management within the boards they are assigned.');

		$manager->persist($managerRole);

		// Create the ROLE_USER role
		$userRole = new Role();
		$userRole->setName('ROLE_USER');
		$userRole->setDescription("Normal usergroup. For example employees.");

		$manager->persist($userRole);

		/**
		 * BOARDS
		 */

		// Create board 'Team E-Commerce'
		$board1 = new Board();
		$board1->setName('Team E-Commerce');
		$board1->setDescription('Kanban-board of e-commerce-team. Displays issues from topics online-shopping, electronic trading/commerce and development of websites like online shops. Systems which are used are for example Magento, Oxid or Terrashop.');

		$manager->persist($board1);

		// Create board 'Team Human Resources'
		$board2 = new Board();
		$board2->setName('Team Human Resources');
		$board2->setDescription('Kanban-board of staff department. Working and managing at issues for human resources. For example different topics of employees, payment of them, hiring new people, organize travel for team events and ask for working feedback.');

		$manager->persist($board2);

		// Create board 'Team Backend-Development'
		$board3 = new Board();
		$board3->setName('Team Backend-Development');
		$board3->setDescription('Kanban-board of backend-development-team. Tasks for connection different websites with third party systems like ERP or CRM.');

		$manager->persist($board3);

		/**
		 * USER
		 */

		// Create a admin user 'John Doe'
		$adminUser = new User();
		$adminUser->setFirstName('John');
		$adminUser->setLastName('Doe');
		$adminUser->setUsername('john');
		$adminUser->setEmail('john@example.com');
		$adminUser->getBoards()->add($board1);
		$adminUser->getBoards()->add($board3);
		$adminUser->setSalt(md5(uniqid(microtime())));
		$adminUser->setPassword('admin');
		$adminUser->addRole($adminRole);

		$manager->persist($adminUser);

        // Create a manager user 'Daniel K'
        $managerUser = new User();
        $managerUser->setFirstName('Daniel');
        $managerUser->setLastName('K');
        $managerUser->setUsername('daniel');
        $managerUser->setEmail('daniel@example.com');
        $managerUser->getBoards()->add($board1);
        $managerUser->setSalt(md5(uniqid(microtime())));
        $managerUser->setPassword('manager');
        $managerUser->addRole($managerRole);

        $manager->persist($managerUser);

		// Create a normal user 'Max Mustermann'
		$normalUser = new User();
		$normalUser->setFirstName('Max');
		$normalUser->setLastName('Mustermann');
		$normalUser->setUsername('max');
		$normalUser->setEmail('max@mustermann.de');
		$normalUser->getBoards()->add($board2);
		$normalUser->getBoards()->add($board3);
		$normalUser->setSalt(md5(uniqid(microtime())));
		$normalUser->setPassword('user');
		$normalUser->addRole($userRole);

		$manager->persist($normalUser);

		// Create a deactivated normal user 'Dieter Müller'
		$deactivatedNormalUser = new User();
		$deactivatedNormalUser->setFirstName('Dieter');
		$deactivatedNormalUser->setLastName('Müller');
		$deactivatedNormalUser->setUsername('dieter');
		$deactivatedNormalUser->setEmail('dieter@google.de');
		$deactivatedNormalUser->getBoards()->add($board2);
		$deactivatedNormalUser->getBoards()->add($board3);
		$deactivatedNormalUser->setSalt(md5(uniqid(microtime())));
		$deactivatedNormalUser->setPassword('user');
		$deactivatedNormalUser->setDisabled(TRUE);
		$deactivatedNormalUser->addRole($userRole);

		$manager->persist($deactivatedNormalUser);

		// Create a admin user 'Markus Ele'
		$anotherAdminUser = new User();
		$anotherAdminUser->setFirstName('Markus');
		$anotherAdminUser->setLastName('Ele');
		$anotherAdminUser->setUsername('markus');
		$anotherAdminUser->setEmail('markus@yahoo.de');
		$anotherAdminUser->getBoards()->add($board1);
		$anotherAdminUser->getBoards()->add($board2);
		$anotherAdminUser->getBoards()->add($board3);
		$anotherAdminUser->setSalt(md5(uniqid(microtime())));
		$anotherAdminUser->setPassword('user');
		$anotherAdminUser->setDisabled(FALSE);
		$anotherAdminUser->addRole($adminRole);

		$manager->persist($anotherAdminUser);

		// Create a normal user 'Daniel Schmi'
		$anotherNormalUser = new User();
		$anotherNormalUser->setFirstName('Daniel');
		$anotherNormalUser->setLastName('Schmi');
		$anotherNormalUser->setUsername('daniel');
		$anotherNormalUser->setEmail('daniel@web.de');
		$anotherNormalUser->getBoards()->add($board1);
		$anotherNormalUser->setSalt(md5(uniqid(microtime())));
		$anotherNormalUser->setPassword('user');
		$anotherNormalUser->setDisabled(FALSE);
		$anotherNormalUser->addRole($userRole);

		$manager->persist($anotherNormalUser);

		/**
		 * BOARD COLUMNS
		 */

		// Create first board column 'Backlog' for board 'Team E-Commerce'
		$board1Column1 = new BoardColumn();
		$board1Column1->setName('Backlog');
		$board1Column1->setMaxIssues(0);
		$board1Column1->setSorting(10);
		$board1Column1->setBoard($board1);

		$manager->persist($board1Column1);

		// Create second board column 'ToDo' for board 'Team E-Commerce'
		$board1Column2 = new BoardColumn();
		$board1Column2->setName('ToDo');
		$board1Column2->setMaxIssues(10);
		$board1Column2->setSorting(20);
		$board1Column2->setBoard($board1);

		$manager->persist($board1Column2);

		// Create third board column 'Analysis' for board 'Team E-Commerce'
		$board1Column3 = new BoardColumn();
		$board1Column3->setName('Analysis');
		$board1Column3->setMaxIssues(4);
		$board1Column3->setSorting(30);
		$board1Column3->setBoard($board1);

		$manager->persist($board1Column3);

		// Create fourth board column 'Development' for board 'Team E-Commerce'
		$board1Column4 = new BoardColumn();
		$board1Column4->setName('Development');
		$board1Column4->setMaxIssues(2);
		$board1Column4->setSorting(40);
		$board1Column4->setBoard($board1);

		$manager->persist($board1Column4);

		// Create fifth board column 'Approval' for board 'Team E-Commerce'
		$board1Column5 = new BoardColumn();
		$board1Column5->setName('Approval');
		$board1Column5->setMaxIssues(0);
		$board1Column5->setSorting(50);
		$board1Column5->setBoard($board1);

		$manager->persist($board1Column5);

		// Create sixth board column 'Deploy' for board 'Team E-Commerce'
		$board1Column6 = new BoardColumn();
		$board1Column6->setName('Deploy');
		$board1Column6->setMaxIssues(3);
		$board1Column6->setSorting(60);
		$board1Column6->setBoard($board1);

		$manager->persist($board1Column6);

		// Create seventh board column 'Done' for board 'Team E-Commerce'
		$board1Column7 = new BoardColumn();
		$board1Column7->setName('Done');
		$board1Column7->setMaxIssues(0);
		$board1Column7->setSorting(70);
		$board1Column7->setBoard($board1);

		$manager->persist($board1Column7);

		// Create first board column 'Job requests' for board 'Team Human Resources'
		$board2Column1 = new BoardColumn();
		$board2Column1->setName('Job requests');
		$board2Column1->setMaxIssues(15);
		$board2Column1->setSorting(10);
		$board2Column1->setBoard($board2);

		$manager->persist($board2Column1);

		// Create second board column 'Job meeting' for board 'Team Human Resources'
		$board2Column2 = new BoardColumn();
		$board2Column2->setName('Job meeting');
		$board2Column2->setMaxIssues(2);
		$board2Column2->setSorting(20);
		$board2Column2->setBoard($board2);

		$manager->persist($board2Column2);

		// Create third board column 'Done' for board 'Team Human Resources'
		$board2Column3 = new BoardColumn();
		$board2Column3->setName('Done');
		$board2Column3->setMaxIssues(0);
		$board2Column3->setSorting(30);
		$board2Column3->setBoard($board2);

		$manager->persist($board2Column3);

		/**
		 * ISSUES
		 */

		// Create first issue for column 'Backlog'
		$issue1 = new Issue();
		$issue1->setName('Evaluate Paypal module for Magento');
		$issue1->setSorting(30);
		$issue1->setBoardColumn($board1Column1);
		$issue1->setCreatedUser($adminUser);
		$issue1->setLastEditedUser($adminUser);

		$manager->persist($issue1);

		// Create second issue for column 'Backlog'
		$issue2 = new Issue();
		$issue2->setName('Evaluate Paypal module for Oxid');
		$issue2->setSorting(20);
		$issue2->setBoardColumn($board1Column1);
		$issue2->setCreatedUser($normalUser);
		$issue2->setLastEditedUser($normalUser);

		$manager->persist($issue2);

		// Create third issue for column 'Backlog'
		$issue3 = new Issue();
		$issue3->setName('Insert new design collection to Karl Lagerfelds onlineshop');
		$issue3->setSorting(10);
		$issue3->setBoardColumn($board1Column1);
		$issue3->setCreatedUser($adminUser);
		$issue3->setLastEditedUser($adminUser);

		$manager->persist($issue3);

		// Create first issue for column 'Analysis'
		$issue4 = new Issue();
		$issue4->setName('Hash user passwords in database for more security');
		$issue4->setSorting(10);
		$issue4->setBoardColumn($board1Column3);
		$issue4->setCreatedUser($deactivatedNormalUser);
		$issue4->setLastEditedUser($deactivatedNormalUser);

		$manager->persist($issue4);

		// Create fourth issue for column 'Backlog'
		$issue5 = new Issue();
		$issue5->setName('Write a new magento module to take A/B usability tests');
		$issue5->setSorting(40);
		$issue5->setBoardColumn($board1Column1);
		$issue5->setCreatedUser($normalUser);
		$issue5->setLastEditedUser($adminUser);

		$manager->persist($issue5);

		// Create first issue for column 'Development'
		$issue6 = new Issue();
		$issue6->setName('"Sign up" function for newsletter at Levis online shop');
		$issue6->setSorting(10);
		$issue6->setBoardColumn($board1Column4);
		$issue6->setCreatedUser($adminUser);
		$issue6->setLastEditedUser($normalUser);

		$manager->persist($issue6);

		// Create first issue for column 'Job requests'
		$issue7 = new Issue();
		$issue7->setName('Dennis Putta requested via Linked.in');
		$issue7->setSorting(10);
		$issue7->setBoardColumn($board2Column1);
		$issue7->setCreatedUser($adminUser);
		$issue7->setLastEditedUser($adminUser);

		$manager->persist($issue7);

		// Create first issue for column 'Done'
		$issue8 = new Issue();
		$issue8->setName('Hire a new java developer');
		$issue8->setSorting(10);
		$issue8->setBoardColumn($board2Column3);
		$issue8->setCreatedUser($adminUser);
		$issue8->setLastEditedUser($adminUser);

		$manager->persist($issue8);

		$manager->flush();
	}
}