<?php

namespace Pkw\CheckBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entity
 *
 * @ORM\Entity(
 *      repositoryClass = "Pkw\CheckBundle\Entity\Repository\CommunityRepository"
 * )
 * @ORM\Table(
 *      name = "community"
 * )
 */
class Community
{
    /** @const integer */
    const TYPE_CITY = 1;

    /** @const integer */
    const TYPE_COMMUNITY = 2;

    /**
     * @var integer
     *
     * @ORM\Column(
     *      name = "id",
     *      type = "integer"
     * )
     * @ORM\Id
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(
     *      name = "name",
     *      type = "string",
     * )
     */
    protected $name;

    /**
     * @var integer
     *
     * @ORM\Column(
     *      name = "type",
     *      type = "smallint",
     * )
     */
    protected $type;

    /**
     * @var District
     *
     * @ORM\ManyToOne(
     *      inversedBy = "communities",
     *      targetEntity = "District"
     * )
     */
    protected $district;

    /**
     * Set ID
     *
     * @param integer $id ID
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get ID
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
     * @param string $name name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
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

    /**
     * Set type
     *
     * @param integer $type
     *
     * @return self
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set district
     *
     * @param District $district district
     * 
     * @return self
     */
    public function setDistrict(District $district)
    {
        $this->district = $district;

        return $this;
    }

    /**
     * Get district
     *
     * @return District 
     */
    public function getDistrict()
    {
        return $this->district;
    }
}
