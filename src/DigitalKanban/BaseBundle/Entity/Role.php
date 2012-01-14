<?php

namespace DigitalKanban\BaseBundle\Entity;

use Symfony\Component\Security\Core\Role\RoleInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * DigitalKanban\BaseBundle\Entity\Role
 *
 * Model of a role
 *
 * @ORM\Table(name="role")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class Role implements RoleInterface {

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
	 * @Assert\NotBlank()
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
	 * Set description
	 *
	 * @param string $description
	 * @return void
	 */
	public function setDescription($description) {
		$this->description = $description;
	}

	/**
	 * Get description
	 *
	 * @return string
	 */
	public function getDescription() {
		return $this->description;
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
	 * Returns the role.
	 *
	 * This method returns a string representation whenever possible.
	 *
	 * When the role cannot be represented with sufficient precision by a
	 * string, it should return null.
	 *
	 * @return string|null A string representation of the role, or null
	 */
	public function getRole() {
		return $this->getName();
	}

	/**
	 * Automatic string conversion of object Role
	 *
	 * @return null|string
	 */
	public function __toString() {
		return $this->getRole();
	}
}