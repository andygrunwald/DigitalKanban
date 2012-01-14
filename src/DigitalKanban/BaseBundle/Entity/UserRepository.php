<?php
namespace DigitalKanban\BaseBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

/**
 * User repository
 *
 * This is used to create a custom lookup for the login process.
 * In our example the user could use his username or his email to login.
 */
class UserRepository extends EntityRepository implements UserProviderInterface {

	/**
	 * Loads the user for the given username OR email.
	 *
	 * This method must throw UsernameNotFoundException if the user is not
	 * found.
	 *
	 * @throws UsernameNotFoundException if the user is not found
	 * @param string $username The username or email address
	 *
	 * @return UserInterface
	 */
	public function loadUserByUsername($username) {
		$entityManager = $this->getEntityManager();
		$query = $entityManager->createQuery('
			SELECT u
			FROM DigitalKanbanBaseBundle:User u
			WHERE
				u.disabled = 0
				AND (
					u.username = :username
					OR u.email = :username
				)
		');
		$query->setParameters(array('username' => $username));
		$user = $query->getOneOrNullResult();

		if($user === NULL) {
			throw new UsernameNotFoundException('Sorry dude, but the could not be found or is disabled.');
		}

		return $user;
	}

	/**
	 * Refreshes the user for the account interface.
	 *
	 * @param UserInterface $user
	 * @return UserInterface
	 */
	public function refreshUser(UserInterface $user) {
		$user = $this->loadUserByUsername($user->getUsername());

		return $user;
	}

	/**
	 * Whether this provider supports the given user class
	 *
	 * @param string $class
	 * @return Boolean
	 */
	public function supportsClass($class) {
		return $class === 'DigitalKanban\BaseBundle\Entity\User';
	}

}