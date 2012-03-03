<?php

namespace DigitalKanban\BaseBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use DigitalKanban\BaseBundle\Entity\Role;

/**
 * DigitalKanban\BaseBundle\Entity\User
 *
 * Model of an user
 *
 * But why the hell implements a normal model a UserInterface?
 * Because we implemented our own lookup method for our login formular.
 * So the implementation of the UserInterface is necessary. For more information have a look at UserRepository
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="DigitalKanban\BaseBundle\Entity\UserRepository")
 * @ORM\HasLifecycleCallbacks()
 * @DoctrineAssert\UniqueEntity(fields={"username"})
 * @DoctrineAssert\UniqueEntity(fields={"email"})
 */
class User implements UserInterface {

	/**
	 * @var integer $id
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
	 * @var string $username
	 *
	 * @Assert\NotBlank()
	 * @Assert\MinLength(3)
	 * @Assert\MaxLength(50)
	 * @ORM\Column(name="username", unique=true, type="string", length=50)
	 */
	protected $username;

	/**
	 * @var string $email
	 *
	 * @Assert\NotBlank()
	 * @Assert\MinLength(5)
	 * @Assert\MaxLength(100)
	 * @Assert\Email()
	 * @ORM\Column(name="email", unique=true, type="string", length=100)
	 */
	protected $email;

	/**
	 * @var string $password
	 *
	 * @Assert\NotBlank()
	 * @Assert\MinLength(5)
	 * @Assert\MaxLength(100)
	 * @ORM\Column(name="password", type="string", length=100)
	 */
	protected $password;

	/**
	 * @var string $first_name
	 *
	 * @Assert\NotBlank()
	 * @Assert\MaxLength(50)
	 * @ORM\Column(name="first_name", type="string", length=50)
	 */
	protected $first_name;

	/**
	 * @var string $last_name
	 *
	 * @Assert\NotBlank()
	 * @Assert\MaxLength(50)
	 * @ORM\Column(name="last_name", type="string", length=50)
	 */
	protected $last_name;

	/**
	 * @var datetime $created
	 *
	 * @ORM\Column(name="created", type="datetime")
	 */
	protected $created;

	/**
	 * @var datetime $edited
	 *
	 * @ORM\Column(name="edited", type="datetime")
	 */
	protected $edited;

	/**
	 * @var boolean $disabled
	 *
	 * @ORM\Column(name="disabled", type="boolean")
	 */
	protected $disabled = FALSE;

	/**
	 * @var string $salt
	 *
	 * @Assert\NotBlank()
	 * @Assert\MaxLength(50)
	 * @ORM\Column(name="salt", type="string", length=50)
	 */
	protected $salt;

	/**
	 * @var ArrayCollection $roles
	 *
	 * @ORM\ManyToMany(targetEntity="Role")
	 * @ORM\JoinTable(name="user_has_role",
	 *     joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
	 *     inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
	 * )
	 */
	protected $roles;

	/**
	 * @var ArrayCollection $boards
	 *
	 * @ORM\ManyToMany(targetEntity="Board")
	 * @ORM\JoinTable(name="user_has_board",
	 *     joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
	 *     inverseJoinColumns={@ORM\JoinColumn(name="board_id", referencedColumnName="id")}
	 * )
	 */
	protected $boards;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->roles = new ArrayCollection();
		$this->boards = new ArrayCollection();
	}

	/**
	 * Get id
	 *
	 * @return integer
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Set username
	 *
	 * @param string $username
	 * @return void
	 */
	public function setUsername($username) {
		$this->username = $username;
	}

	/**
	 * Get username
	 *
	 * @return string
	 */
	public function getUsername() {
		return $this->username;
	}

	/**
	 * Set email
	 *
	 * @param string $email
	 * @return void
	 */
	public function setEmail($email) {
		$this->email = $email;
	}

	/**
	 * Get email
	 *
	 * @return string
	 */
	public function getEmail() {
		return $this->email;
	}

	/**
	 * Set password
	 *
	 * @param string $password
	 * @return void
	 */
	public function setPassword($password) {
		$encoder = new MessageDigestPasswordEncoder('sha512', true, 10);
		$this->password = $encoder->encodePassword($password, $this->getSalt());
	}

	/**
	 * Get password
	 *
	 * @return string
	 */
	public function getPassword() {
		return $this->password;
	}

	/**
	 * Set first_name
	 *
	 * @param string $firstName
	 * @return void
	 */
	public function setFirstName($firstName) {
		$this->first_name = $firstName;
	}

	/**
	 * Get first_name
	 *
	 * @return string
	 */
	public function getFirstName() {
		return $this->first_name;
	}

	/**
	 * Set last_name
	 *
	 * @param string $lastName
	 * @return void
	 */
	public function setLastName($lastName) {
		$this->last_name = $lastName;
	}

	/**
	 * Get last_name
	 *
	 * @return string
	 */
	public function getLastName() {
		return $this->last_name;
	}

	/**
	 * Set created
	 *
	 * @ORM\prePersist
	 * @return void
	 */
	public function setCreated() {
		$this->created = new \DateTime();
	}

	/**
	 * Get created
	 *
	 * @return datetime
	 */
	public function getCreated() {
		return $this->created;
	}

	/**
	 * Set edited
	 *
	 * @ORM\prePersist
	 * @ORM\preUpdate
	 * @return void
	 */
	public function setEdited() {
		$this->edited = new \DateTime();
	}

	/**
	 * Get edited
	 *
	 * @return datetime
	 */
	public function getEdited() {
		return $this->edited;
	}

	/**
	 * Set disabled
	 *
	 * @param boolean $disabled
	 * @return void
	 */
	public function setDisabled($disabled) {
		$this->disabled = (($disabled) ? TRUE: FALSE);
	}

	/**
	 * Get disabled
	 *
	 * @return boolean
	 */
	public function getDisabled() {
		return $this->disabled;
	}

	/**
	 * @param Role $role
	 * @return void
	 */
	public function addRole(Role $role) {
		$this->roles->add($role);
	}

	/**
	 * Gets the boards
	 *
	 * @return ArrayCollection
	 */
	public function getBoards() {
		return $this->boards;
	}

	/**
	 * Get password salt
	 *
	 * @return string The salt
	 */
	public function getSalt() {
		return $this->salt;
	}

	/**
	 * Set salt
	 *
	 * @param $salt
	 * @return void
	 */
	public function setSalt($salt) {
		$this->salt = $salt;
	}

	/**
	 * Gets all roles, separated by , as readable string
	 *
	 * @return string
	 */
	public function getRolesAsReadableString() {
		$roleArray = $this->getRoles();
		$roleNames = array();

		foreach($roleArray as $role) {
			$roleName = $role->getName();
			$roleName = str_replace('ROLE_', '', $roleName);
			$roleName = strtolower($roleName);
			$roleNames[] = ucfirst($roleName);
		}

		return implode(', ', $roleNames);
	}

	/**
	 * Returns the roles granted to the user.
	 *
	 * @return Role[] The user roles
	 */
	public function getRoles() {
		return $this->roles->toArray();
	}

	/**
	 * Return roles as ArrayCollection
	 *
	 * @return ArrayCollection|\Doctrine\Common\Collections\ArrayCollection
	 */
	public function getRolesAsArrayCollection() {
		return $this->roles;
	}

	/**
	 * Checks if the user is an administrator
	 *
	 * @return bool
	 */
	public function isAdmin() {
		$role = $this->roles->first();
		$result = FALSE;

		if(($role instanceof Role) && $role->getName() === 'ROLE_ADMIN') {
			$result = TRUE;
		}

		return $result;
	}

    /**
   	 * Checks if the user is an manager
   	 *
   	 * @return bool
   	 */
   	public function isManager() {
   		$role = $this->roles->first();
   		$result = FALSE;

   		if(($role instanceof Role) && $role->getName() === 'ROLE_MANAGER') {
   			$result = TRUE;
   		}

   		return $result;
   	}

	/**
	 * Returns true, if the user is admin. False otherwise.
	 *
	 * ATTENTION: This is only a dummy method.
	 * Why we do this? We do this to display in DigitalKanban\BaseBundle\Form\Type\UserType a admin checkbox.
	 * This User-Model has no admin property. Yes, we know, there is a 'property_path' => FALSE, property for
	 * the checkbox. But if 'property_path' => FALSE, set, we found no way to set a checkbox as 'checked' (manually).
	 * If you know a way to handle a checkbox correctly without an property in your modell, please let me know :)
	 * You can find me on github <https://github.com/andygrunwald>
	 *
	 * @see DigitalKanban\BaseBundle\Form\Type\UserType
	 * @see DigitalKanban\BaseBundle\Controller\UserController
	 *
	 * @return bool
	 */
	public function getAdmin() {
		return $this->isAdmin();
	}

	/**
	 * ATTENTION: This is only a dummy method.
	 * Why we do this? We do this to display in DigitalKanban\BaseBundle\Form\Type\UserType a admin checkbox.
	 * This User-Model has no admin property. Yes, we know, there is a 'property_path' => FALSE, property for
	 * the checkbox. But if 'property_path' => FALSE, set, we found no way to set a checkbox as 'checked' (manually).
	 * If you know a way to handle a checkbox correctly without an property in your modell, please let me know :)
	 * You can find me on github <https://github.com/andygrunwald>
	 *
	 * @see DigitalKanban\BaseBundle\Form\Type\UserType
	 * @see DigitalKanban\BaseBundle\Controller\UserController
	 *
	 * @return void
	 */
	public function setAdmin() {

	}

    /**
   	 * Returns true, if the user is manager. False otherwise.
   	 *
   	 * ATTENTION: This is only a dummy method.
   	 * Why we do this? We do this to display in DigitalKanban\BaseBundle\Form\Type\UserType a manager checkbox.
   	 * This User-Model has no admin property. Yes, we know, there is a 'property_path' => FALSE, property for
   	 * the checkbox. But if 'property_path' => FALSE, set, we found no way to set a checkbox as 'checked' (manually).
   	 * If you know a way to handle a checkbox correctly without an property in your modell, please let me know :)
   	 * You can find me on github <https://github.com/andygrunwald>
   	 *
   	 * @see DigitalKanban\BaseBundle\Form\Type\UserType
   	 * @see DigitalKanban\BaseBundle\Controller\UserController
   	 *
   	 * @return bool
   	 */
   	public function getManager() {
   		return $this->isManager();
   	}

   	/**
   	 * ATTENTION: This is only a dummy method.
   	 * Why we do this? We do this to display in DigitalKanban\BaseBundle\Form\Type\UserType a manager checkbox.
   	 * This User-Model has no admin property. Yes, we know, there is a 'property_path' => FALSE, property for
   	 * the checkbox. But if 'property_path' => FALSE, set, we found no way to set a checkbox as 'checked' (manually).
   	 * If you know a way to handle a checkbox correctly without an property in your modell, please let me know :)
   	 * You can find me on github <https://github.com/andygrunwald>
   	 *
   	 * @see DigitalKanban\BaseBundle\Form\Type\UserType
   	 * @see DigitalKanban\BaseBundle\Controller\UserController
   	 *
   	 * @return void
   	 */
   	public function setManager() {

   	}

	/**
	 * Removes sensitive data from the user.
	 *
	 * @return void
	 */
	public function eraseCredentials() {
	}

	/**
	 * The equality comparison should neither be done by referential equality
	 * nor by comparing identities (i.e. getId() === getId()).
	 *
	 * However, you do not need to compare every attribute, but only those that
	 * are relevant for assessing whether re-authentication is required.
	 *
	 * @param UserInterface $user
	 * @return Boolean
	 */
	public function equals(UserInterface $user) {
		return md5($this->getUsername()) == md5($user->getUsername());
	}

	/**
	 * String implementation of this object
	 *
	 * @return string
	 */
	public function __toString() {
		$name = $this->getFirstName() . ' ' . $this->getLastName();
		$name .= ' (' . $this->getUsername() . ')';

		return $name;
	}
}