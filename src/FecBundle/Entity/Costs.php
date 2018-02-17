<?php

namespace FecBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Costs
 *
 * @ORM\Table(name="costs")
 * @ORM\Entity(repositoryClass="FecBundle\Repository\CostsRepository")
 */
class Costs
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="costs_category", type="string", length=100)
     */
    private $costsCategory;

    /**
     * @var string
     *
     * @ORM\Column(name="costs_group", type="string", length=100)
     */
    private $costsGroup;

    /**
     * @var string
     *
     * @ORM\Column(name="costs_entry", type="string", length=100)
     */
    private $costsEntry;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Transactions", mappedBy="costsEntry")
     */
    private $transactions;

    public function __construct()
    {
        $this->transactions = new ArrayCollection();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set costsCategory.
     *
     * @param string $costsCategory
     *
     * @return Costs
     */
    public function setCostsCategory($costsCategory)
    {
        $this->costsCategory = $costsCategory;

        return $this;
    }

    /**
     * Get costsCategory.
     *
     * @return string
     */
    public function getCostsCategory()
    {
        return $this->costsCategory;
    }

    /**
     * Set costsGroup.
     *
     * @param string $costsGroup
     *
     * @return Costs
     */
    public function setCostsGroup($costsGroup)
    {
        $this->costsGroup = $costsGroup;

        return $this;
    }

    /**
     * Get costsGroup.
     *
     * @return string
     */
    public function getCostsGroup()
    {
        return $this->costsGroup;
    }

    /**
     * Set costsEntry.
     *
     * @param string $costsEntry
     *
     * @return Costs
     */
    public function setCostsEntry($costsEntry)
    {
        $this->costsEntry = $costsEntry;

        return $this;
    }

    /**
     * Get costsEntry.
     *
     * @return string
     */
    public function getCostsEntry()
    {
        return $this->costsEntry;
    }

    /**
     * @return mixed
     */
    public function getTransactions()
    {
        return $this->transactions;
    }

    /**
     * @param mixed $transactions
     */
    public function setTransactions($transactions)
    {
        $this->transactions = $transactions;
    }
}
