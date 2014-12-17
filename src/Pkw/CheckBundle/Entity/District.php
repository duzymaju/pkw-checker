<?php

namespace Pkw\CheckBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entity
 *
 * @ORM\Entity(
 *      repositoryClass = "Pkw\CheckBundle\Entity\Repository\DistrictRepository"
 * )
 * @ORM\Table(
 *      name = "district"
 * )
 */
class District
{
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
     * @var Province
     *
     * @ORM\ManyToOne(
     *      inversedBy = "districts",
     *      targetEntity = "Province"
     * )
     */
    protected $province;

    /**
     * @var Constituency
     *
     * @ORM\ManyToOne(
     *      inversedBy = "districts",
     *      targetEntity = "Constituency"
     * )
     */
    protected $constituency;

    /**
     * @var integer
     *
     * @ORM\Column(
     *      name = "electorates_number",
     *      type = "integer",
     * )
     */
    protected $electoratesNumber;

    /**
     * @var integer
     *
     * @ORM\Column(
     *      name = "polling_stations_number",
     *      type = "integer",
     * )
     */
    protected $pollingStationsNumber;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(
     *      cascade = {
     *          "PERSIST",
     *          "REMOVE",
     *      },
     *      mappedBy = "district",
     *      targetEntity = "Community"
     * )
     */
    protected $communities;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->communities = new ArrayCollection();
    }

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
     * Set province
     *
     * @param Province $province province
     *
     * @return self
     */
    public function setProvince(Province $province)
    {
        $this->province = $province;

        return $this;
    }

    /**
     * Get province
     *
     * @return Province 
     */
    public function getProvince()
    {
        return $this->province;
    }

    /**
     * Set constituency
     *
     * @param Constituency $constituency constituency
     *
     * @return self
     */
    public function setConstituency(Constituency $constituency)
    {
        $this->constituency = $constituency;

        return $this;
    }

    /**
     * Get constituency
     *
     * @return Constituency
     */
    public function getConstituency()
    {
        return $this->constituency;
    }

    /**
     * Set electorates number
     *
     * @param integer $electoratesNumber electorates number
     *
     * @return self
     */
    public function setElectoratesNumber($electoratesNumber)
    {
        $this->electoratesNumber = $electoratesNumber;

        return $this;
    }

    /**
     * Get electorates number
     *
     * @return integer
     */
    public function getElectoratesNumber()
    {
        return $this->electoratesNumber;
    }

    /**
     * Set polling stations number
     *
     * @param integer $pollingStationsNumber polling stations number
     *
     * @return self
     */
    public function setPollingStationsNumber($pollingStationsNumber)
    {
        $this->pollingStationsNumber = $pollingStationsNumber;

        return $this;
    }

    /**
     * Get polling stations number
     *
     * @return integer
     */
    public function getPollingStationsNumber()
    {
        return $this->pollingStationsNumber;
    }

    /**
     * Add community
     *
     * @param Community $community community
     *
     * @return self
     */
    public function addCommunity(Community $community)
    {
        $this->communities[] = $community;

        return $this;
    }

    /**
     * Remove community
     *
     * @param Community $community community
     */
    public function removeCommunity(Community $community)
    {
        $this->communities->removeElement($community);
    }

    /**
     * Get communities
     *
     * @return ArrayCollection
     */
    public function getCommunities()
    {
        return $this->communities;
    }
}
