<?php
namespace DigitalKanban\BaseBundle\Entity;
use Doctrine\ORM\EntityRepository;

/**
 * BoardRepository
 *
 */

class BoardRepository extends EntityRepository
{

    public function boardChoices($user)
    {

        $queryBuilder = $this->createQueryBuilder('b')
                             ->join('b.users', 'u')
                             ->where('u.id = :userid')
                             ->orderBy('b.name');

        $queryBuilder->setParameter('userid', $user->getId());

        $boards = $queryBuilder->getQuery()
                               ->execute();

        $ca = array();

        foreach ($boards as $b) {
            $ca[$b->getId()] = $b->getName();
        }

        return $ca;
    }

}
