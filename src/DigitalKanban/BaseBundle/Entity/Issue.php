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
   	 * @var string $issueType
   	 *
   	 * @Assert\MaxLength(100)
   	 * @ORM\Column(name="issueType", type="string", length=100)
   	 */
   	protected $issueType;

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
	 * @ORM\ManyToOne(targetEntity="BoardColumn", inversedBy="issues")
	 * @ORM\JoinColumn(name="boardcolumn_id", referencedColumnName="id", onDelete="CASCADE")
	 */
	protected $boardColumn;

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
	 * Set BoardColumn
	 *
	 * @param BoardColumn $boardColumn
	 * @return void
	 */
	public function setBoardColumn(BoardColumn $boardColumn) {
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

    /**
     * Gets issue type
     *
     * @return string
     */
    public function getIssueType() {
        return $this->issueType;
    }

    /**
     * Set issue type
     *
     * @param $issueType
     */
    public function setIssueType($issueType) {
        $this->issueType = $issueType;
    }
}