<?php

namespace Pkw\CheckBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Polling station repository
 */
class PollingStationRepository extends EntityRepository
{
    /**
     * Count
     *
     * @return integer
     */
    public function count()
    {
        $query = $this->_em->createQuery('SELECT COUNT(ps.id) FROM PkwCheckBundle:PollingStation ps');
        $count = $query->getSingleScalarResult();

        return (integer) $count;
    }
}
