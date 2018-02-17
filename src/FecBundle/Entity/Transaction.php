<?php

namespace FecBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Transactions
 *
 * @ORM\Table(name="transactions")
 * @ORM\Entity(repositoryClass="FecBundle\Repository\TransactionRepository")
 */
class Transaction
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
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var float
     *
     * @ORM\Column(name="sum", type="float")
     */
    private $sum;

    /**
     * @ORM\ManyToOne(targetEntity="FecBundle\Entity\Costs", inversedBy="transactions", fetch="EAGER")
     * @ORM\JoinColumn(nullable=true)
     */
    private $costsEntry;

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
     * Set date.
     *
     * @param \DateTime $date
     *
     * @return Transaction
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date.
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set sum.
     *
     * @param float $sum
     *
     * @return Transaction
     */
    public function setSum($sum)
    {
        $this->sum = $sum;

        return $this;
    }

    /**
     * Get sum.
     *
     * @return float
     */
    public function getSum()
    {
        return $this->sum;
    }

    /**
     * @return mixed
     */
    public function getCostsEntry()
    {
        return $this->costsEntry;
    }

    /**
     * @param mixed $costsEntry
     */
    public function setCostsEntry($costsEntry)
    {
        $this->costsEntry = $costsEntry;
    }
}
