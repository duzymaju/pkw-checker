<?php

namespace Pkw\CheckBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Constituency repository
 */
class ConstituencyRepository extends EntityRepository
{
    /**
     * Count
     *
     * @return integer
     */
    public function count()
    {
        $query = $this->_em->createQuery('SELECT COUNT(c.id) FROM PkwCheckBundle:Constituency c');
        $count = $query->getSingleScalarResult();

        return (integer) $count;
    }
}
