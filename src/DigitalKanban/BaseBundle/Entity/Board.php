<?php

namespace DigitalKanban\BaseBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * DigitalKanban\BaseBundle\Entity\Board
 *
 * Model of a board
 *
 * @ORM\Table(name="board")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class Board
{

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
     * @Assert\MaxLength(50)
     * @ORM\Column(name="name", type="string", length=50)
     */
    protected $name;

    /**
     * @var string $description
     *
     * @ORM\Column(name="description", type="text")
     */
    protected $description = '';

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
     * @var ArrayCollection $columns
     *
     * @ORM\OneToMany(targetEntity="BoardColumn", mappedBy="board")
     */
    protected $columns;

    /**
     * @var ArrayCollection $archives
     *
     * @ORM\OneToMany(targetEntity="Archive", mappedBy="board")
     */
    protected $archives;

    /**
     * @var ArrayCollection $users
     *
     * @ORM\ManyToMany(targetEntity="User", mappedBy="boards")
     */
    protected $users;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->columns = new ArrayCollection();
        $this->archives = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    /**
     * If this class will be used as a string,
     * the name of this board will represent this object
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return void
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set created
     *
     * @ORM\prePersist
     * @return void
     */
    public function setCreated()
    {
        $this->created = new \DateTime();
    }

    /**
     * Get created
     *
     * @return datetime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set edited
     *
     * @ORM\prePersist
     * @ORM\preUpdate
     * @return void
     */
    public function setEdited()
    {
        $this->edited = new \DateTime();
    }

    /**
     * Get edited
     *
     * @return integer
     */
    public function getEdited()
    {
        return $this->edited;
    }

    /**
     * Get the columns
     *
     * @return ArrayCollection
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Get the archives
     *
     * @return ArrayCollection
     */
    public function getArchives()
    {
        return $this->archives;
    }

    /**
     * Get the users
     *
     * @return ArrayCollection
     */
    public function getUsers()
    {
        return $this->users;
    }
}
