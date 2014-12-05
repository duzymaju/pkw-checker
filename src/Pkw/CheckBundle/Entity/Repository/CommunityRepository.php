<?php

namespace Pkw\CheckBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Community repository
 */
class CommunityRepository extends EntityRepository
{
    /**
     * Count
     *
     * @return integer
     */
    public function count()
    {
        $query = $this->_em->createQuery('SELECT COUNT(c.id) FROM PkwCheckBundle:Community c');
        $count = $query->getSingleScalarResult();

        return (integer) $count;
    }
}
