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
     * @var Community
     *
     * @ORM\ManyToOne(
     *      inversedBy = "constituencies",
     *      targetEntity = "Community"
     * )
     */
    protected $community;

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
     * Set community
     *
     * @param Community $community community
     *
     * @return self
     */
    public function setCommunity(Community $community = null)
    {
        $this->community = $community;

        return $this;
    }

    /**
     * Get community
     *
     * @return Community
     */
    public function getCommunity()
    {
        return $this->community;
    }

    /**
     * Add polling station
     *
     * @param PollingStation $pollingStation polling station
     *
     * @return Constituency
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
