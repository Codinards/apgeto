<?php

namespace App\Entity\Utils;

use App\Entity\Main\Funds\Account;
use App\Entity\Main\Users\User;
use App\Form\Validators\LowerThan;
use App\Tools\AppConstants;
use DateTime;
use DateTimeInterface;

class LoanInFlowsUpdate
{
    private $wording;

    private $observations;

    private $createdAt;

    // private $paybackAt;

    private $firstAvaliste;

    private $firstObservation;

    private $secondAvaliste;

    private $secondObservation;

    private $thirdAvaliste;

    private $thirdObservation;

    private $fourthAvaliste;

    private $fourthObservation;

    private $fifthAvaliste;

    private $fifthObservation;

    private ?int $year = null;

    private \DateInterval $renewalPeriod;

    // public function __construct()
    // {
    //     $this->createdAt = new DateTime();
    //     $this->renewalPeriod = new \DateInterval(AppConstants::$RENEWALPERIOD);
    // }

    /**
     * Get the value of wording
     */
    public function getWording(): ?string
    {
        return $this->wording;
    }

    /**
     * Set the value of wording
     *
     * @return  self
     */
    public function setWording(?string $wording): self
    {
        $this->wording = $wording;

        return $this;
    }

    /**
     * Get the value of observation
     */
    public function getObservations(): ?string
    {
        return $this->observations;
    }

    /**
     * Set the value of observation
     *
     * @return  self
     */
    public function setObservations(?string $observation): self
    {
        $this->observations = $observation;

        return $this;
    }

    /**
     * Get the value of createdAt
     */
    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * Set the value of createdAt
     *
     * @return  self
     */
    public function setCreatedAt(?DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        $this->year = (int) $this->createdAt->format('Y');

        return $this;
    }

    /**
     * Get the value of firstAvaliste
     */
    public function getFirstAvaliste() //: null|User|Account
    {
        return $this->firstAvaliste;
    }

    /**
     * Set the value of firstAvaliste
     *
     * @return  self
     */
    public function setFirstAvaliste(/*null|User|Account*/$firstAvaliste): self
    {
        $this->firstAvaliste = (!$firstAvaliste instanceof User) ? $firstAvaliste->getUser() : $firstAvaliste;

        return $this;
    }

    /**
     * Get the value of secondAvaliste
     */
    public function getSecondAvaliste() //: null|User|Account
    {
        return $this->secondAvaliste;
    }

    /**
     * Set the value of secondAvaliste
     *
     * @return  self
     */
    public function setSecondAvaliste(/*null|User|Account*/$secondAvaliste): self
    {
        $this->secondAvaliste = (!$secondAvaliste instanceof User) ? $secondAvaliste->getUser() : $secondAvaliste;;

        return $this;
    }

    /**
     * Get the value of thirdAvaliste
     */
    public function getThirdAvaliste() //: null|User|Account
    {
        return $this->thirdAvaliste;
    }

    /**
     * Set the value of thirdAvaliste
     *
     * @return  self
     */
    public function setThirdAvaliste(/*null|User|Account*/$thirdAvaliste): self
    {
        $this->thirdAvaliste = (!$thirdAvaliste instanceof User) ? $thirdAvaliste->getUser() : $thirdAvaliste;

        return $this;
    }

    /**
     * Get the value of fourthAvaliste
     */
    public function getFourthAvaliste() //: null|User|Account
    {
        return $this->fourthAvaliste;
    }

    /**
     * Set the value of fourthAvaliste
     *
     * @return  self
     */
    public function setFourthAvaliste(/*null|User|Account*/$fourthAvaliste): self
    {
        $this->fourthAvaliste = (!$fourthAvaliste instanceof User) ? $fourthAvaliste->getUser() : $fourthAvaliste;

        return $this;
    }

    /**
     * Get the value of fifthAvaliste
     */
    public function getFifthAvaliste() //: null|User|Account
    {
        return $this->fifthAvaliste;
    }

    /**
     * Set the value of fifthAvaliste
     *
     * @return  self
     */
    public function setFifthAvaliste(/*null|User|Account*/$fifthAvaliste): self
    {
        $this->fifthAvaliste = (!$fifthAvaliste instanceof User) ? $fifthAvaliste->getUser() : $fifthAvaliste;

        return $this;
    }

    /**
     * Get the value of firstObservation
     */
    public function getFirstObservation(): ?string
    {
        return $this->firstObservation;
    }

    /**
     * Set the value of firstObservation
     *
     * @return  self
     */
    public function setFirstObservation(?string $firstObservation): self
    {
        $this->firstObservation = $firstObservation;

        return $this;
    }

    /**
     * Get the value of secondObservation
     */
    public function getSecondObservation(): ?string
    {
        return $this->secondObservation;
    }

    /**
     * Set the value of secondObservation
     *
     * @return  self
     */
    public function setSecondObservation(?string $secondObservation): self
    {
        $this->secondObservation = $secondObservation;

        return $this;
    }

    /**
     * Get the value of thirdObservation
     */
    public function getThirdObservation(): ?string
    {
        return $this->thirdObservation;
    }

    /**
     * Set the value of thirdObservation
     *
     * @return  self
     */
    public function setThirdObservation(?string $thirdObservation): self
    {
        $this->thirdObservation = $thirdObservation;

        return $this;
    }

    /**
     * Get the value of fourthObservation
     */
    public function getFourthObservation(): ?string
    {
        return $this->fourthObservation;
    }

    /**
     * Set the value of fourthObservation
     *
     * @return  self
     */
    public function setFourthObservation(?string $fourthObservation): self
    {
        $this->fourthObservation = $fourthObservation;

        return $this;
    }

    /**
     * Get the value of fifthObservation
     */
    public function getFifthObservation(): ?string
    {
        return $this->fifthObservation;
    }

    /**
     * Set the value of fifthObservation
     *
     * @return  self
     */
    public function setFifthObservation(?string $fifthObservation): self
    {
        $this->fifthObservation = $fifthObservation;

        return $this;
    }

    public function generateObservation(string $glue = ','): string
    {
        $data = ['first', 'second', 'third', 'fourth', 'fifth'];
        $observation = '{';
        if ($this->observations !== null) {
            $observation .= '"observations": "' . $this->observations . '"' . $glue . ' ';
        }
        foreach ($data as $key) {
            $avaliste = $key . 'Avaliste';
            $observationAvaliste = $key . 'Observation';
            if ($this->$avaliste !== null) {
                if (empty($this->$observationAvaliste)) {
                    $observation .= '"' . $avaliste . '": "' . $this->$avaliste->getName() . '" ' . $glue . ' ';
                } else {
                    $ob = '{"avaliste": "' . $this->$avaliste->getName() . '", "observation": "' . $this->$observationAvaliste . '"}';
                    $observation .=  '"' . $avaliste . '": ' . $ob . $glue . ' ';
                }
            }
        }
        return $this->observations =  substr($observation, 0, -2) . '}';
    }

    public function getAvalistes(): array
    {
        $avalistes = [];
        $data = ['first', 'second', 'third', 'fourth', 'fifth'];
        foreach ($data as $key) {
            $avaliste = $key . 'Avaliste';
            if ($this->$avaliste !== null) {
                $avalistes[] = $this->$avaliste;
            }
        }

        return $avalistes;
    }


    /**
     * Get the value of year
     */
    public function getYear(): ?int
    {
        return $this->year;
    }


    /**
     * Get the value of year
     */
    public function setYear(?int $year): self
    {
        $this->year = $year;

        return $this;
    }

    // /**
    //  * Get the value of paybackAt
    //  */
    // public function getPaybackAt(): ?DateTimeInterface
    // {
    //     return $this->paybackAt;
    // }

    // /**
    //  * Set the value of paybackAt
    //  *
    //  * @return  self
    //  */
    // public function setPaybackAt(?DateTimeInterface $paybackAt)
    // {
    //     $this->paybackAt = $paybackAt;

    //     return $this;
    // }

    public function hydrate(\App\Entity\Main\Funds\Debt $debt): self
    {
        $avalistesKeys = ['firstAvaliste', 'secondAvaliste', 'thirdAvaliste', 'fourthAvaliste', 'fifthAvaliste'];
        $this->wording = $debt->getWording();
        $this->createdAt = $debt->getCreatedAt();
        $this->paybackAt = $debt->getPaybackAt();
        foreach ($debt->resolveObservations() as $key => $observation) {
            if ($key == $observation) {
                $this->observations = $observation;
            } else {
                $this->$key = is_array($observation) ? $observation["observation"] : null;
            }
        }

        foreach ($debt->getAvalistes() as $key => $avaliste) {
            $this->$avalistesKeys[$key] = $avaliste;
        }

        return $this;
    }

    public function getRenewalPeriod(): \DateInterval
    {
        return $this->renewalPeriod;
    }

    public function setRenewalPeriod(\DateInterval $dateInterval): self
    {
        $this->renewalPeriod = $dateInterval;

        return $this;
    }
}
