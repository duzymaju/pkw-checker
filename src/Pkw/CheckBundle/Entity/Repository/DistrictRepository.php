<?php

namespace Pkw\CheckBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * District repository
 */
class DistrictRepository extends EntityRepository
{
    /**
     * Count
     *
     * @return integer
     */
    public function count()
    {
        $query = $this->_em->createQuery('SELECT COUNT(d.id) FROM PkwCheckBundle:District d');
        $count = $query->getSingleScalarResult();

        return (integer) $count;
    }
}
