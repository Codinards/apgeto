<?php

namespace App\Entity\Utils;

class FirstConfigEntity
{
    private $pseudo;

    private $password;

    private $username;

    private $address;

    private $telephone;

    private $appName = 'PHP Tontine';

    private $logo;

    private $defaultLanguage;

    private $moneyDevise;

    private $fundCanBeNegative = false;

    private $baseFundAmountToLoan = 0;

    private $accountBaseAmount = 0;

    private $userCanGetMultipleLoan = false;

    private $interestRateToLoan = 0.1;

    private $insurranceAmount = 100000;

    private $unityMaxCount = 10;


    /**
     * Get the value of pseudo
     */
    public function getPseudo()
    {
        return $this->pseudo;
    }

    /**
     * Set the value of pseudo
     *
     * @return  self
     */
    public function setPseudo($pseudo)
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    /**
     * Get the value of password
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set the value of password
     *
     * @return  self
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get the value of username
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set the value of username
     *
     * @return  self
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get the value of address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set the value of address
     *
     * @return  self
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get the value of telephone
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * Set the value of telephone
     *
     * @return  self
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * Get the value of createdAt
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set the value of createdAt
     *
     * @return  self
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get the value of role
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set the value of role
     *
     * @return  self
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get the value of appName
     */
    public function getAppName()
    {
        return $this->appName;
    }

    /**
     * Set the value of appName
     *
     * @return  self
     */
    public function setAppName($appName)
    {
        $this->appName = $appName;

        return $this;
    }

    /**
     * Get the value of logo
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * Set the value of logo
     *
     * @return  self
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * Get the value of defaultLanguage
     */
    public function getDefaultLanguage()
    {
        return $this->defaultLanguage;
    }

    /**
     * Set the value of defaultLanguage
     *
     * @return  self
     */
    public function setDefaultLanguage($defaultLanguage)
    {
        $this->defaultLanguage = $defaultLanguage;

        return $this;
    }

    /**
     * Get the value of moneyDevise
     */
    public function getMoneyDevise()
    {
        return $this->moneyDevise;
    }

    /**
     * Set the value of moneyDevise
     *
     * @return  self
     */
    public function setMoneyDevise($moneyDevise)
    {
        $this->moneyDevise = $moneyDevise;

        return $this;
    }

    /**
     * Get the value of fundCanBeNegative
     */
    public function getFundCanBeNegative()
    {
        return $this->fundCanBeNegative;
    }

    /**
     * Set the value of fundCanBeNegative
     *
     * @return  self
     */
    public function setFundCanBeNegative($fundCanBeNegative)
    {
        $this->fundCanBeNegative = $fundCanBeNegative;

        return $this;
    }

    /**
     * Get the value of unityMaxCount
     */
    public function getUnityMaxCount()
    {
        return $this->unityMaxCount;
    }

    /**
     * Set the value of unityMaxCount
     *
     * @return  self
     */
    public function setUnityMaxCount($unityMaxCount)
    {
        $this->unityMaxCount = $unityMaxCount;

        return $this;
    }

    /**
     * Get the value of baseFundAmountToLoan
     */
    public function getBaseFundAmountToLoan()
    {
        return $this->baseFundAmountToLoan;
    }

    /**
     * Set the value of baseFundAmountToLoan
     *
     * @return  self
     */
    public function setBaseFundAmountToLoan($baseFundAmountToLoan)
    {
        $this->baseFundAmountToLoan = $baseFundAmountToLoan;

        return $this;
    }

    /**
     * Get the value of accountBaseAmount
     */
    public function getAccountBaseAmount()
    {
        return $this->accountBaseAmount;
    }

    /**
     * Set the value of accountBaseAmount
     *
     * @return  self
     */
    public function setAccountBaseAmount($accountBaseAmount)
    {
        $this->accountBaseAmount = $accountBaseAmount;

        return $this;
    }

    /**
     * Get the value of userCanGetMultipleLoan
     */
    public function getUserCanGetMultipleLoan()
    {
        return $this->userCanGetMultipleLoan;
    }

    /**
     * Set the value of userCanGetMultipleLoan
     *
     * @return  self
     */
    public function setUserCanGetMultipleLoan($userCanGetMultipleLoan)
    {
        $this->userCanGetMultipleLoan = $userCanGetMultipleLoan;

        return $this;
    }

    /**
     * Get the value of interestRateToLoan
     */
    public function getInterestRateToLoan()
    {
        return $this->interestRateToLoan;
    }

    /**
     * Set the value of interestRateToLoan
     *
     * @return  self
     */
    public function setInterestRateToLoan($interestRateToLoan)
    {
        $this->interestRateToLoan = $interestRateToLoan;

        return $this;
    }

    /**
     * Get the value of insurranceAmount
     */
    public function getInsurranceAmount()
    {
        return $this->insurranceAmount;
    }

    /**
     * Set the value of insurranceAmount
     *
     * @return  self
     */
    public function setInsurranceAmount($insurranceAmount)
    {
        $this->insurranceAmount = $insurranceAmount;

        return $this;
    }
}
