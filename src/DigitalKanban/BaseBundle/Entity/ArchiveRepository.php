<?php
namespace DigitalKanban\BaseBundle\Entity;
use Doctrine\ORM\EntityRepository;

/**
 * ArchiveRepository
 *
 */
class ArchiveRepository extends EntityRepository
{

    public function getFiltered($user, $options = array())
    {
        $default_params = array(
            'datedeb' => new \DateTime(), 'datefin' => new \DateTime(), 'myarchive' => true, 'board' => false,
        );

        $params_set = array();

        foreach ($default_params as $k => $v) {
            $params_set[$k] = (array_key_exists($k, $options)) ? $options[$k] : $v;
        }

        $qb = $this->createQueryBuilder('a');
        $qb->leftJoin('a.board', 'b')
                ->leftJoin('a.archived_user', 'u')
                ->addOrderBy('a.archived');

        $qb->andWhere('a.archived > :datedeb');
        $qb->andWhere('a.archived < :datefin');

        $datedeb = $params_set['datedeb'];
        $datefin = $params_set['datefin'];
        $datedeb->setTime(0, 0, 0);
        $datefin->setTime(23, 59, 59);

        $qb->setParameter('datedeb', $datedeb);
        $qb->setParameter('datefin', $datefin);

        if ($params_set['myarchive']) {
            $qb->andWhere('a.archived_user = :user');
            $qb->setParameter('user', $user->getID());
        }

        if ($params_set['board']) {
            $qb->andWhere('a.board = :board');
            $qb->setParameter('board', $params_set['board']);
        }

        return $qb->getQuery()
                  ->execute();
    }
}
