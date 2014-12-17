<?php

namespace Pkw\CheckBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entity
 *
 * @ORM\Entity(
 *      repositoryClass = "Pkw\CheckBundle\Entity\Repository\ConstituencyRepository"
 * )
 * @ORM\Table(
 *      name = "constituency"
 * )
 */
class Constituency
{
    /**
     * @var integer
     *
     * @ORM\Column(
     *      name = "id",
     *      type = "integer"
     * )
     * @ORM\Id
     * @ORM\GeneratedValue(
     *      strategy="AUTO"
     * )
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(
     *      name = "number",
     *      type = "integer",
     * )
     */
    protected $number;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(
     *      cascade = {
     *          "PERSIST",
     *          "REMOVE",
     *      },
     *      mappedBy = "constituency",
     *      targetEntity = "District"
     * )
     */
    protected $districts;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(
     *      cascade = {
     *          "PERSIST",
     *          "REMOVE",
     *      },
     *      mappedBy = "constituency",
     *      targetEntity = "PollingStation"
     * )
     */
    protected $pollingStations;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->districts = new ArrayCollection();
        $this->pollingStations = new ArrayCollection();
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
     * Set number
     *
     * @param integer $number number
     *
     * @return self
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return integer 
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Add district
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
     * Remove district
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

    /**
     * Add polling station
     *
     * @param PollingStation $pollingStation polling station
     *
     * @return self
     */
    public function addPollingStation(PollingStation $pollingStation)
    {
        $this->pollingStations[] = $pollingStation;

        return $this;
    }

    /**
     * Remove polling station
     *
     * @param PollingStation $pollingStation polling station
     */
    public function removePollingStation(PollingStation $pollingStation)
    {
        $this->pollingStations->removeElement($pollingStation);
    }

    /**
     * Get polling stations
     *
     * @return ArrayCollection
     */
    public function getPollingStations()
    {
        return $this->pollingStations;
    }
}
