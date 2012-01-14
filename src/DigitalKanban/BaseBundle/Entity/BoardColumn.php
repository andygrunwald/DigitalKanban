<?php

namespace DigitalKanban\BaseBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use DigitalKanban\BaseBundle\Entity\Board;

/**
 * DigitalKanban\BaseBundle\Entity\BoardColumn
 *
 * Model of a board column
 *
 * @ORM\Table(name="board_column")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class BoardColumn {

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
	 * @Assert\MinLength(3)
	 * @Assert\MaxLength(50)
	 * @ORM\Column(name="name", type="string", length=50)
	 */
	protected $name;

	/**
	 * @var integer $max_issues
	 *
	 * @ORM\Column(name="max_issues", type="integer")
	 */
	protected $max_issues;

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
	private $edited;

	/**
	 * @var integer $sorting
	 *
	 * @ORM\Column(name="sorting", type="integer")
	 */
	protected $sorting;

	/**
	 * @ORM\ManyToOne(targetEntity="Board", inversedBy="columns")
	 * @ORM\JoinColumn(name="board_id", referencedColumnName="id", onDelete="CASCADE")
	 */
	protected $board;

	/**
	 * @var ArrayCollection $issues
	 *
	 * @ORM\OneToMany(targetEntity="Issue", mappedBy="boardColumn")
	 */
	protected $issues;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->issues = new ArrayCollection();
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
	 * Set max_issues
	 *
	 * @param integer $maxIssues
	 * @return void
	 */
	public function setMaxIssues($maxIssues) {
		$this->max_issues = (int) $maxIssues;
	}

	/**
	 * Get max_issues
	 *
	 * @return integer
	 */
	public function getMaxIssues() {
		return $this->max_issues;
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
		$this->sorting = $sorting;
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
	 * Set the board
	 *
	 * @param Board $board
	 * @return void
	 */
	public function setBoard(Board $board) {
		$this->board = $board;
	}

	/**
	 * Get the board
	 *
	 * @return Board
	 */
	public function getBoard() {
		return $this->board;
	}

	/**
	 * Get the issues
	 *
	 * @return ArrayCollection
	 */
	public function getIssues() {
		return $this->issues;
	}
}