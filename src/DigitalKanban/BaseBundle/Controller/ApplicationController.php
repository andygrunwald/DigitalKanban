<?php

namespace DigitalKanban\BaseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use DigitalKanban\BaseBundle\Entity\User;

/**
 * Application Controller
 */
class ApplicationController extends Controller {

	/**
	 * Index action of application controller
	 *
	 * @return \Symfony\Bundle\FrameworkBundle\Controller\Response
	 */
	public function indexAction() {
		$user = $this->get('security.context')->getToken()->getUser();
		$templateData = array(
			'statistics' => $this->getStatisticsOfUser($user),
		);

		return $this->render('DigitalKanbanBaseBundle:Application:index.html.twig', $templateData);
	}

	/**
	 * Calculates the statistics
	 * The returned array will contain various statistics about the available boards, columns
	 * and tickets of the incoming user.
	 *
	 * @param \DigitalKanban\BaseBundle\Entity\User $userLoggedIn
	 * @return array
	 */
	protected function getStatisticsOfUser(User $userLoggedIn) {
		$boardCount = $columnCount = $ticketCount = $userCount = 0;
		$countedUser = array();
		$userLoggedInId = $userLoggedIn->getId();

		$boards = $userLoggedIn->getBoards();
		$boardCount = count($boards);

		foreach($boards as $board) {
			$columns = $board->getColumns();
			$columnCount += count($columns);

				// We must save the counted users, because if we only sum the user
				// it is possible to get a higher number than users exists, because
				// one user could have more than one board assigned
			$users = $board->getUsers();
			foreach($users as $user) {
				$userId = $user->getId();
					// If the user is not counted yet AND the user is not the user which is logged in
				if(in_array($userId, $countedUser) === FALSE && $userLoggedInId !== $userId) {
					$countedUser[] = $userId;
					$userCount++;
				}
			}

			foreach($columns as $column) {
				$tickets = $column->getIssues();
				$ticketCount += count($tickets);
			}
		}

		$statistics = array(
			'boards' => $this->formatNumberForStatistics($boardCount),
			'columns' => $this->formatNumberForStatistics($columnCount),
			'columnsPerBoardAVG' => $this->formatNumberForStatistics((($boardCount > 0) ? ($columnCount / $boardCount): 0)),
			'tickets' => $this->formatNumberForStatistics($ticketCount),
			'ticketsPerBoardAVG' => $this->formatNumberForStatistics((($boardCount > 0) ? ($ticketCount / $boardCount): 0)),
			'ticketsPerColumnAVG' => $this->formatNumberForStatistics((($columnCount > 0) ? ($ticketCount / $columnCount): 0)),
			'users' => $this->formatNumberForStatistics($userCount),
			'usersPerBoardAVG' => $this->formatNumberForStatistics((($boardCount > 0) ? ($userCount / $boardCount): 0)),
		);

		return $statistics;
	}

	/**
	 * Formats a number.
	 * If the number has decimal places, this will be cropped.
	 * If the number is an integer, there will be no decimal places.
	 *
	 * @param $number
	 * @return string
	 */
	protected function formatNumberForStatistics($number) {

			// Check if this number has decimal places
		$intNumber = (int) $number;
		if(($number - $intNumber) > 0) {
			return number_format($number, 2, ',', '.');
		}

		return $number;
	}
}
