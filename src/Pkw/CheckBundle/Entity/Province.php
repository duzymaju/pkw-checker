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
     *      name = "residents_number",
     *      type = "integer",
     * )
     */
    protected $residentsNumber;

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
     *      name = "districts_number",
     *      type = "integer",
     * )
     */
    protected $districtsNumber;

    /**
     * @var integer
     *
     * @ORM\Column(
     *      name = "communities_number",
     *      type = "integer",
     * )
     */
    protected $communitiesNumber;

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
     * Set residents number
     *
     * @param integer $residentsNumber residents number
     *
     * @return self
     */
    public function setResidentsNumber($residentsNumber)
    {
        $this->residentsNumber = $residentsNumber;

        return $this;
    }

    /**
     * Get residents number
     *
     * @return integer
     */
    public function getResidentsNumber()
    {
        return $this->residentsNumber;
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
     * Set districts number
     *
     * @param integer $districtsNumber districts number
     *
     * @return self
     */
    public function setDistrictsNumber($districtsNumber)
    {
        $this->districtsNumber = $districtsNumber;

        return $this;
    }

    /**
     * Get districts number
     *
     * @return integer
     */
    public function getDistrictsNumber()
    {
        return $this->districtsNumber;
    }

    /**
     * Set communities number
     *
     * @param integer $communitiesNumber communities number
     *
     * @return self
     */
    public function setCommunitiesNumber($communitiesNumber)
    {
        $this->communitiesNumber = $communitiesNumber;

        return $this;
    }

    /**
     * Get communities number
     *
     * @return integer
     */
    public function getCommunitiesNumber()
    {
        return $this->communitiesNumber;
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
