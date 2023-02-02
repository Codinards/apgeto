<?php

namespace App\Tools;

use ReflectionClass;

class AppConstants
{
    /** Base */
    static $SIGLE = null;
    static $LOGO = null;

    /** Languages */
    static $DEFAULT_LANGUAGE_KEY = null; // 'en';
    static $DEFAULT_LANGUAGE_ABBR = null; // 'en_US';


    /** Tontines */

    //const TONTINEUR_MAX_COUNT = 10;
    static $TONTINEUR_MAX_COUNT = null; // 10;

    /** Devise */

    static $MONEY_DEVISE = null; // 'xaf';

    /** Account */

    static ?bool $FUND_CAN_BE_NEGATIVE = null; // true;
    static $LOAN_BASE_FUND = null; // 0;
    static $BASE_ASSURANCE_AMOUNT = null;

    static $ACCOUNT_BASE_FUND = null; // 0;
    static $INSURRANCE_AMOUNT = null;

    static $ACCOUNT_BASE_AMOUNT_TO_BENEFIT_INTEREST = null;

    static ?bool $USER_MULTIPLE_LOAN = null; // false;

    static $LOANPERIOD = null; // false;

    static $RENEWALPERIOD = null; // false;

    static $instance;

    /** INTERESTS */
    static $INTEREST_RATE;
    static $INTEREST_COMMON_PERCENT;
    static $INTEREST_BORROWER_PERCENT;
    static $INTEREST_SAVING_PERCENT;


    /** CONFIGS */
    static ?bool $IS_CONFIGURATE;

    /**
     * @var JsonFileManager
     */
    private $fileManager;

    public function __construct()
    {
        $this->fileManager = new JsonFileManager(
            DirectoryResolver::getDirectory('var/configs/constants.json', false)
        );

        $constants = $this->fileManager->decode();
        foreach ($constants as $key => $value) {
            if (property_exists($this, $key) and $key !== 'instance') {
                self::$$key = $value;
            }
        }
    }

    public function save()
    {
        $constants = $this->resolveConstants();
        if ($constants['instance'] ?? false) {
            unset($constants['instance']);
        }
        foreach ($constants as $prop => $value) {
            if ($value === 'yes' || $value === 'true' || $value === true) {
                $constants[$prop] = 1;
            }
            if ($value === 'no' || $value === 'false' || $value === false) {
                $constants[$prop] = 0;
            }
        }
        $this->fileManager->encode($constants);
    }

    public function resolveConstants(): array
    {
        $instance = self::getInstance();
        $reflection = new ReflectionClass($instance);
        return $reflection->getStaticProperties();
    }

    public static function toArray(): array
    {
        return self::getInstance()->resolveConstants();
    }

    public static function getInstance(): self
    {
        if (null === self::$instance) {
            self::$instance = new AppConstants;
        }


        return self::$instance;
    }

    public function getSigle(): string
    {
        return self::$SIGLE;
    }

    public function getLogo(): string
    {
        return self::$LOGO;
    }

    public function devise(): string
    {
        return self::$MONEY_DEVISE;
    }

    public function hasFirstConfig(): bool
    {
        return self::$IS_CONFIGURATE === true;
    }

    public function baseFund()
    {
        return self::$ACCOUNT_BASE_FUND;
    }

    public function baseLoan()
    {
        return self::$LOAN_BASE_FUND;
    }

    public function getAssuranceAmount()
    {
        return self::$INSURRANCE_AMOUNT;
    }

    public function getBaseAssuranceAmount()
    {
        return self::$BASE_ASSURANCE_AMOUNT;
    }
}
