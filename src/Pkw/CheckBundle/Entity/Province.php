<?php

namespace Pkw\CheckBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entity
 *
 * @ORM\Entity(
 *      repositoryClass = "Pkw\CheckBundle\Entity\Repository\ProvinceRepository"
 * )
 * @ORM\Table(
 *      name = "province"
 * )
 */
class Province
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
     *      mappedBy = "province",
     *      targetEntity = "District"
     * )
     */
    protected $districts;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->districts = new ArrayCollection();
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
     * Add districts
     *
     * @param District $district district
     *
     * @return self
     */
    public function addDistrict(District $district)
    {
        $this->districts[] = $district;

        return $this;
    }

    /**
     * Remove districts
     *
     * @param District $district district
     */
    public function removeDistrict(District $district)
    {
        $this->districts->removeElement($district);
    }

    /**
     * Get districts
     *
     * @return ArrayCollection
     */
    public function getDistricts()
    {
        return $this->districts;
    }
}
