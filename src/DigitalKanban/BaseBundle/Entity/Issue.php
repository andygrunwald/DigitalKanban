<?php

namespace DigitalKanban\BaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use DigitalKanban\BaseBundle\Entity\User;
use DigitalKanban\BaseBundle\Entity\BoardColumn;

/**
 * DigitalKanban\BaseBundle\Entity\Issue
 *
 * Model of an Issue
 *
 * @ORM\Table(name="issue")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class Issue {

	/**
	 * @var integer $id
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
	 * @var string $name
	 *
	 * @Assert\NotBlank()
	 * @Assert\MaxLength(100)
	 * @ORM\Column(name="name", type="string", length=100)
	 */
	protected $name;

	/**
	 * @var User $created_user
	 *
	 * @ORM\ManyToOne(targetEntity="User")
	 * @ORM\JoinColumn(name="created_user_id", referencedColumnName="id", onDelete="CASCADE")
	 */
	protected $created_user;

	/**
	 * @var User $last_edited_user
	 *
	 * @ORM\ManyToOne(targetEntity="User")
	 * @ORM\JoinColumn(name="last_edited_user_id", referencedColumnName="id", onDelete="CASCADE")
	 */
	protected $last_edited_user;

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
	 * @var integer $sorting
	 *
	 * @ORM\Column(name="sorting", type="integer")
	 */
	protected $sorting;
	
	/**
	 * @var datetime $duration
	 *
	 * @ORM\Column(name="duration", type="integer")
	 */
	protected $duration;
	
	/**
	 * @var string $group1
	 *
	 * @ORM\Column(name="group1", type="string", length=100, nullable=true)
	 */
	protected $group1;
	
	
	
	/**
	 * @var string $group2
	 *
	 * @ORM\Column(name="group2", type="string", length=100, nullable=true)
	 */
	protected $group2;
	
	
	/**
	 * @var string $group3
	 *
	 * @ORM\Column(name="group3", type="string", length=100, nullable=true)
	 */
	protected $group3;
	
	
	/**
	 * @var boolean $closed
	 *
	 * @ORM\Column(name="closed", type="boolean")
	 */
	private $closed;

	/**
	 * @ORM\ManyToOne(targetEntity="BoardColumn", inversedBy="issues")
	 * @ORM\JoinColumn(name="boardcolumn_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
	 */
	protected $boardColumn;

	
	public function __construct(){
	    $this->duration = 0;
	    $this->closed = false;
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
	 * Set name
	 *
	 * @param string $name
	 * @return void
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * Get name
	 *
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Set created_user
	 *
	 * @param User $createdUser
	 * @return void
	 */
	public function setCreatedUser(User $createdUser) {
		$this->created_user = $createdUser;
	}

	/**
	 * Get created_user
	 *
	 * @return User
	 */
	public function getCreatedUser() {
		return $this->created_user;
	}

	/**
	 * Set last_edited_user
	 *
	 * @param User $lastEditedUser
	 * @return void
	 */
	public function setLastEditedUser(User $lastEditedUser) {
		$this->last_edited_user = $lastEditedUser;
	}

	/**
	 * Get last_edited_user
	 *
	 * @return User
	 */
	public function getLastEditedUser() {
		return $this->last_edited_user;
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
	 * Set sorting
	 *
	 * @param integer $sorting
	 * @return void
	 */
	public function setSorting($sorting) {
		$this->sorting = (int) $sorting;
	}

	/**
	 * Get sorting
	 *
	 * @return integer
	 */
	public function getSorting() {
		return $this->sorting;
	}
	
	/**
	 * Set duration
	 *
	 * @return void
	 */
	public function setDuration($duration) {
	    $this->duration = $duration;
	}
	
	/**
	 * Get duration
	 *
	 * @return integer
	 */
	public function getDuration() {
	    return $this->duration;
	}
	
	
	/**
	 * Set group1
	 *
	 * @param string $group1
	 * @return void
	 */
	public function setGroup1($group1) {
	    $this->group1 = $group1;
	}
	
	/**
	 * Get group1
	 *
	 * @return string
	 */
	public function getGroup1() {
	    return $this->group1;
	}
	
	/**
	 * Set group2
	 *
	 * @param string $group2
	 * @return void
	 */
	public function setGroup2($group2) {
	    $this->group2 = $group2;
	}
	
	/**
	 * Get group2
	 *
	 * @return string
	 */
	public function getGroup2() {
	    return $this->group2;
	}
	
	/**
	 * Set group3
	 *
	 * @param string $group3
	 * @return void
	 */
	public function setGroup3($group3) {
	    $this->group3 = $group3;
	}
	
	/**
	 * Get group3
	 *
	 * @return string
	 */
	public function getGroup3() {
	    return $this->group3;
	}

	/**
	 * Set closed
	 *
	 * @param boolean $closed
	 */
	public function setClosed($closed)
	{
	    if ($closed == true){
	        $this->setBoardColumn(null);
	    }
	    $this->closed = $closed;
	}
	
	/**
	 * Get closed
	 *
	 * @return boolean
	 */
	public function getClosed()
	{
	    return $this->closed;
	}
	
	
	/**
	 * Set BoardColumn
	 *
	 * @param BoardColumn $boardColumn
	 * @return void
	 */
	public function setBoardColumn($boardColumn) {
		$this->boardColumn = $boardColumn;
	}

	/**
	 * Get BoardColumn
	 *
	 * @return BoardColumn
	 */
	public function getBoardColumn() {
		return $this->boardColumn;
	}

	/**
	 * Gets a random rotation for the issue post-it
	 *
	 * @return integer
	 */
	public function getRandomRotation() {
		return rand(-7, 7);
	}
}