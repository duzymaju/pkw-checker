<?php

namespace Pkw\CheckBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entity
 *
 * @ORM\Entity(
 *      repositoryClass = "Pkw\CheckBundle\Entity\Repository\PollingStationRepository"
 * )
 * @ORM\Table(
 *      name = "polling_station",
 *      uniqueConstraints = {
 *          @ORM\UniqueConstraint(
 *              columns = {
 *                  "key"
 *              },
 *              name = "L_UNIQUE_IDX_1"
 *          )
 *      }
 * )
 */
class PollingStation
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
     *      name = "number",
     *      type = "integer",
     * )
     */
    protected $number;

    /**
     * @var string
     *
     * @ORM\Column(
     *      length = 32,
     *      name = "`key`",
     *      options = {
     *          "fixed" = true
     *      },
     *      type = "string"
     * )
     */
    protected $key;

    /**
     * @var Constituency
     *
     * @ORM\ManyToOne(
     *      inversedBy = "pollingStations",
     *      targetEntity = "Constituency"
     * )
     */
    protected $constituency;

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
     * Set key
     *
     * @param string $key key
     *
     * @return self
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Get key
     *
     * @return string 
     */
    public function getKey()
    {
        return $this->key;
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
}
