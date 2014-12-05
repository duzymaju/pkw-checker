<?php

namespace Pkw\CheckBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Province repository
 */
class ProvinceRepository extends EntityRepository
{
    /**
     * Count
     *
     * @return integer
     */
    public function count()
    {
        $query = $this->_em->createQuery('SELECT COUNT(p.id) FROM PkwCheckBundle:Province p');
        $count = $query->getSingleScalarResult();

        return (integer) $count;
    }
}
