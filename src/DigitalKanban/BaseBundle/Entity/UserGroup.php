<?php

namespace DigitalKanban\BaseBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="usergroup")
 * @ORM\HasLifecycleCallbacks()
 */
class UserGroup
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
   	protected $id;
    /**
     * @ORM\Column(type="string", length=100)
     */
   	protected $name;
    /**
   	 * @var ArrayCollection $users
   	 *
   	 * @ORM\ManyToMany(targetEntity="User", mappedBy="groups")
   	 */
   	protected $users;

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

  
    public function __construct()
    {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
    }
    /**
   	 * If this class will be used as a string,
   	 * the name of this group will represent this object
   	 *
   	 * @return string
   	 */
   	public function __toString() {
   		return $this->getName();
   	}
    
    /**
     * Add users
     *
     * @param DigitalKanban\BaseBundle\Entity\User $users
     */
    public function addUser(\DigitalKanban\BaseBundle\Entity\User $users)
    {
        $this->users[] = $users;
    }

    /**
     * Get users
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getUsers()
    {
        return $this->users;
    }
}