<?php

namespace App\Tools\Entity;

use App\Entity\Tontines\Tontine;
use App\Entity\Tontines\Unity;

class TontineInfoResolver
{
    private $montant = 0;

    private $montantTotal = 0;

    private $cotisationTotal = 0;

    private $relicat = 0;

    private $reste = 0;

    private $achat = 0;

    private $totalAchat = 0;


    public function getRelicat(array $unities)
    {

        /** @var Unity $unity */
        foreach ($unities as $unity) {
            $this->achat += $unity->getAchat();
            $this->montant += $unity->getIsDemiNom() ? $unity->getTontine()->getAmount() /2 : $unity->getTontine()->getAmount();
        }
        $tontine = $unity->getTontine();
        $this->totalAchat += $this->achat;
        $this->relicat +=  $this->achat + $tontine->getCotisation() - $this->montant;
        $this->reste = $this->relicat - $this->totalAchat;
        $this->montantTotal += $this->montant;
        $this->cotisationTotal += $this->montant;
        $this->montant = 0;
        $this->achat = 0;
        return $this->relicat;
    }

    public function getReste()
    {
        return $this->reste;
    }

    public function setAchat($achat)
    {
        $this->totalAchat += $achat;
    }

    public function relicatNegatif(): bool
    {
        return $this->relicat < 0;
    }

    public function isTotalNegatif(): bool
    {
        return $this->reste < 0;
    }

    public function getTotalAchat()
    {

        $total = $this->totalAchat;
        //$this->totalAchat = 0;
        return $total;
    }

    public function getCotisation(Tontine $tontine)
    {
        return $tontine->getCotisation();
    }

    /**
     * Get the value of montantTotal
     */
    public function getMontantTotal()
    {
        return $this->montantTotal;
    }

    /**
     * Get the value of cotisationTotal
     */
    public function getCotisationTotal()
    {
        return $this->cotisationTotal;
    }
}
