<?php

namespace Pkw\CheckBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Committee repository
 */
class CommitteeRepository extends EntityRepository
{
    /**
     * Count
     *
     * @return integer
     */
    public function count()
    {
        $query = $this->_em->createQuery('SELECT COUNT(c.id) FROM PkwCheckBundle:Committee c');
        $count = $query->getSingleScalarResult();

        return (integer) $count;
    }
}
