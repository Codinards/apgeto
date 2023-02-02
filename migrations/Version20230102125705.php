<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230102125705 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE CotisationDay (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, createdAt DATE NOT NULL)');
        $this->addSql('CREATE TABLE DebtAvalist (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, debt_id INTEGER NOT NULL, observation VARCHAR(255) DEFAULT NULL)');
        $this->addSql('CREATE INDEX IDX_9E8ECB15A76ED395 ON DebtAvalist (user_id)');
        $this->addSql('CREATE INDEX IDX_9E8ECB15240326A5 ON DebtAvalist (debt_id)');
        $this->addSql('CREATE TABLE DebtRenewal (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, debt_id INTEGER NOT NULL, account_id INTEGER NOT NULL, admin_id INTEGER DEFAULT NULL, wording VARCHAR(255) NOT NULL, amount INTEGER NOT NULL, createdAt DATETIME NOT NULL, year VARCHAR(4) NOT NULL, observation VARCHAR(255) DEFAULT NULL, debtRate DOUBLE PRECISION DEFAULT NULL, linkedFund_id INTEGER DEFAULT NULL, linkedDebt_id INTEGER DEFAULT NULL)');
        $this->addSql('CREATE INDEX IDX_CF8283D7240326A5 ON DebtRenewal (debt_id)');
        $this->addSql('CREATE INDEX IDX_CF8283D79B6B5FBA ON DebtRenewal (account_id)');
        $this->addSql('CREATE INDEX IDX_CF8283D7642B8210 ON DebtRenewal (admin_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CF8283D7304432C6 ON DebtRenewal (linkedFund_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CF8283D731E49BEA ON DebtRenewal (linkedDebt_id)');
        $this->addSql('CREATE TABLE account (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, admin_id INTEGER DEFAULT NULL, cash_in_flows INTEGER DEFAULT 0 NOT NULL, cash_out_flows INTEGER DEFAULT 0 NOT NULL, cash_balances INTEGER DEFAULT 0 NOT NULL, loan_in_flows INTEGER DEFAULT 0 NOT NULL, loan_out_flows INTEGER DEFAULT 0 NOT NULL, loan_balances INTEGER DEFAULT 0 NOT NULL, createdAt DATETIME NOT NULL, currentDebt_id INTEGER DEFAULT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7D3656A4A76ED395 ON account (user_id)');
        $this->addSql('CREATE INDEX IDX_7D3656A4642B8210 ON account (admin_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7D3656A4154422D4 ON account (currentDebt_id)');
        $this->addSql('CREATE TABLE "action" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, module_id INTEGER NOT NULL, name VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, isUpdatable BOOLEAN DEFAULT \'1\' NOT NULL, hasAuth BOOLEAN DEFAULT \'1\' NOT NULL, isIndex BOOLEAN DEFAULT \'0\' NOT NULL, isActivated BOOLEAN NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_47CC8C925E237E06 ON "action" (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_47CC8C922B36786B ON "action" (title)');
        $this->addSql('CREATE INDEX IDX_47CC8C92AFC2B591 ON "action" (module_id)');
        $this->addSql('CREATE TABLE assistance (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, type_id INTEGER NOT NULL, user_id INTEGER NOT NULL, admin_id INTEGER NOT NULL, amount INTEGER NOT NULL, created_at DATETIME NOT NULL, total_contributions INTEGER NOT NULL, wording VARCHAR(255) NOT NULL, year VARCHAR(4) NOT NULL)');
        $this->addSql('CREATE INDEX IDX_1B4F85F2C54C8C93 ON assistance (type_id)');
        $this->addSql('CREATE INDEX IDX_1B4F85F2A76ED395 ON assistance (user_id)');
        $this->addSql('CREATE INDEX IDX_1B4F85F2642B8210 ON assistance (admin_id)');
        $this->addSql('CREATE TABLE assistance_type (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, amount INTEGER DEFAULT NULL, is_amount BOOLEAN DEFAULT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EB7863615E237E06 ON assistance_type (name)');
        $this->addSql('CREATE TABLE contributor (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, assistance_id INTEGER NOT NULL, user_id INTEGER NOT NULL, amount INTEGER NOT NULL)');
        $this->addSql('CREATE INDEX IDX_DA6F97937096529A ON contributor (assistance_id)');
        $this->addSql('CREATE INDEX IDX_DA6F9793A76ED395 ON contributor (user_id)');
        $this->addSql('CREATE TABLE cotisation_failure (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, unity_id INTEGER NOT NULL, tontiner_id INTEGER NOT NULL, tontine_id INTEGER NOT NULL, createdAt DATETIME NOT NULL, cotisationDay_id INTEGER NOT NULL)');
        $this->addSql('CREATE INDEX IDX_2D28ACDBF6859C8C ON cotisation_failure (unity_id)');
        $this->addSql('CREATE INDEX IDX_2D28ACDB9C1FE128 ON cotisation_failure (tontiner_id)');
        $this->addSql('CREATE INDEX IDX_2D28ACDB3023704B ON cotisation_failure (cotisationDay_id)');
        $this->addSql('CREATE INDEX IDX_2D28ACDBDEB5C9FD ON cotisation_failure (tontine_id)');
        $this->addSql('CREATE TABLE debt (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, account_id INTEGER NOT NULL, user_id INTEGER NOT NULL, parent_id INTEGER DEFAULT NULL, admin_id INTEGER NOT NULL, wording VARCHAR(255) NOT NULL, loan_in_flows INTEGER DEFAULT NULL, loan_out_flows INTEGER DEFAULT NULL, loan_renewals INTEGER DEFAULT NULL, loan_balances INTEGER DEFAULT 0 NOT NULL, interests INTEGER DEFAULT NULL, observations CLOB DEFAULT NULL, year VARCHAR(4) NOT NULL, createdAt DATETIME NOT NULL, isCurrent BOOLEAN DEFAULT \'1\' NOT NULL, renewalAt DATETIME DEFAULT NULL, paybackAt DATETIME DEFAULT NULL, previousBalances INTEGER NOT NULL, renewalPeriod VARCHAR(255) DEFAULT NULL --(DC2Type:dateinterval)
        , previousTotalInflows INTEGER DEFAULT 0 NOT NULL, previousTotalOutflows INTEGER DEFAULT 0 NOT NULL, debtRate DOUBLE PRECISION DEFAULT NULL, type VARCHAR(10) NULL)');
        $this->addSql('CREATE INDEX IDX_DBBF0A839B6B5FBA ON debt (account_id)');
        $this->addSql('CREATE INDEX IDX_DBBF0A83A76ED395 ON debt (user_id)');
        $this->addSql('CREATE INDEX IDX_DBBF0A83727ACA70 ON debt (parent_id)');
        $this->addSql('CREATE INDEX IDX_DBBF0A83642B8210 ON debt (admin_id)');
        $this->addSql('CREATE TABLE debt_interest (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, debt_id INTEGER NOT NULL, wording VARCHAR(255) NOT NULL, interest INTEGER NOT NULL, isRenewal BOOLEAN DEFAULT \'0\' NOT NULL, createdAt DATETIME NOT NULL, year VARCHAR(4) NOT NULL, debtRate DOUBLE PRECISION DEFAULT NULL)');
        $this->addSql('CREATE INDEX IDX_2222ACE0240326A5 ON debt_interest (debt_id)');
        $this->addSql('CREATE TABLE fund (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, account_id INTEGER NOT NULL, user_id INTEGER NOT NULL, admin_id INTEGER NOT NULL, operation_id INTEGER DEFAULT NULL, assistance_id INTEGER DEFAULT NULL, wording VARCHAR(255) NOT NULL, cash_in_flows INTEGER DEFAULT NULL, cash_out_flows INTEGER DEFAULT NULL, cash_balances INTEGER DEFAULT 0 NOT NULL, createdAt DATETIME NOT NULL, observations VARCHAR(255) DEFAULT NULL, year VARCHAR(4) NOT NULL, previousBalances INTEGER NOT NULL, previousTotalInflows INTEGER DEFAULT 0 NOT NULL, previousTotalOutflows INTEGER DEFAULT 0 NOT NULL, type VARCHAR(10) DEFAULT NULL)');
        $this->addSql('CREATE INDEX IDX_DC923E109B6B5FBA ON fund (account_id)');
        $this->addSql('CREATE INDEX IDX_DC923E10A76ED395 ON fund (user_id)');
        $this->addSql('CREATE INDEX IDX_DC923E10642B8210 ON fund (admin_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_DC923E1044AC3583 ON fund (operation_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_DC923E107096529A ON fund (assistance_id)');
        $this->addSql('CREATE TABLE module (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, isActivated BOOLEAN DEFAULT \'0\' NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C2426285E237E06 ON module (name)');
        $this->addSql('CREATE TABLE operation (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, type_id INTEGER NOT NULL, admin_id INTEGER NOT NULL, wording VARCHAR(255) NOT NULL, inflows INTEGER DEFAULT NULL, outflows INTEGER DEFAULT NULL, balance INTEGER NOT NULL, observation VARCHAR(255) DEFAULT NULL, createdAt DATETIME NOT NULL, year VARCHAR(4) DEFAULT NULL)');
        $this->addSql('CREATE INDEX IDX_1981A66DC54C8C93 ON operation (type_id)');
        $this->addSql('CREATE INDEX IDX_1981A66D642B8210 ON operation (admin_id)');
        $this->addSql('CREATE TABLE operation_type (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, admin_id INTEGER NOT NULL, name VARCHAR(255) NOT NULL, balance INTEGER NOT NULL, isUpdatable BOOLEAN DEFAULT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A3AE0AB85E237E06 ON operation_type (name)');
        $this->addSql('CREATE INDEX IDX_A3AE0AB8642B8210 ON operation_type (admin_id)');
        $this->addSql('CREATE TABLE role (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, isDeletable BOOLEAN DEFAULT \'0\' NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_57698A6A5E237E06 ON role (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_57698A6A2B36786B ON role (title)');
        $this->addSql('CREATE TABLE role_useraction (role_id INTEGER NOT NULL, useraction_id INTEGER NOT NULL, PRIMARY KEY(role_id, useraction_id))');
        $this->addSql('CREATE INDEX IDX_A002F17AD60322AC ON role_useraction (role_id)');
        $this->addSql('CREATE INDEX IDX_A002F17A537F5155 ON role_useraction (useraction_id)');
        $this->addSql('CREATE TABLE tontine (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, type_id INTEGER NOT NULL, admin_id INTEGER NOT NULL, cotisation INTEGER NOT NULL, amount INTEGER NOT NULL, isCurrent BOOLEAN NOT NULL, count INTEGER DEFAULT 0 NOT NULL, maxCount INTEGER DEFAULT 1 NOT NULL, won INTEGER DEFAULT 0 NOT NULL, countDeminNom INTEGER DEFAULT 0 NOT NULL, createdAt DATETIME NOT NULL, closedAt DATETIME DEFAULT NULL, addMember BOOLEAN DEFAULT \'1\' NOT NULL, name VARCHAR(255) DEFAULT NULL, lockedCount INTEGER DEFAULT 0 NOT NULL, DemiLockedCount INTEGER DEFAULT 0 NOT NULL)');
        $this->addSql('CREATE INDEX IDX_3F164B7FC54C8C93 ON tontine (type_id)');
        $this->addSql('CREATE INDEX IDX_3F164B7F642B8210 ON tontine (admin_id)');
        $this->addSql('CREATE TABLE tontine_tontineur (tontine_id INTEGER NOT NULL, tontineur_id INTEGER NOT NULL, PRIMARY KEY(tontine_id, tontineur_id))');
        $this->addSql('CREATE INDEX IDX_16D542AEDEB5C9FD ON tontine_tontineur (tontine_id)');
        $this->addSql('CREATE INDEX IDX_16D542AED8C4B14B ON tontine_tontineur (tontineur_id)');
        $this->addSql('CREATE TABLE tontine_type (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, cotisation INTEGER NOT NULL, minAchat INTEGER NOT NULL, name VARCHAR(255) NOT NULL, isCurrent BOOLEAN NOT NULL, hasAvaliste BOOLEAN DEFAULT \'1\' NOT NULL, amend INTEGER DEFAULT 0 NOT NULL, minAmend INTEGER DEFAULT 0 NOT NULL, hasAchat BOOLEAN DEFAULT \'0\' NOT NULL, hasMultipleTontine BOOLEAN DEFAULT \'0\' NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A6197AEE5E237E06 ON tontine_type (name)');
        $this->addSql('CREATE TABLE tontineur (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, admin_id INTEGER DEFAULT NULL, name VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E4334D6BA76ED395 ON tontineur (user_id)');
        $this->addSql('CREATE INDEX IDX_E4334D6B642B8210 ON tontineur (admin_id)');
        $this->addSql('CREATE TABLE tontineur_data (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, tontineur_id INTEGER NOT NULL, tontine_id INTEGER NOT NULL, count INTEGER NOT NULL, won INTEGER NOT NULL, lockedCount INTEGER NOT NULL, countDemiNom INTEGER NOT NULL, isLocked BOOLEAN DEFAULT \'0\' NOT NULL, lockedAt DATETIME DEFAULT NULL)');
        $this->addSql('CREATE INDEX IDX_6B7528E5D8C4B14B ON tontineur_data (tontineur_id)');
        $this->addSql('CREATE INDEX IDX_6B7528E5DEB5C9FD ON tontineur_data (tontine_id)');
        $this->addSql('CREATE TABLE unity (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, tontineur_id INTEGER NOT NULL, tontine_id INTEGER NOT NULL, avaliste_id INTEGER DEFAULT NULL, admin_id INTEGER NOT NULL, achat INTEGER DEFAULT NULL, amount INTEGER NOT NULL, createdAt DATETIME NOT NULL, benefitAt DATETIME DEFAULT NULL, isWon BOOLEAN DEFAULT \'0\' NOT NULL, isStopped BOOLEAN DEFAULT \'0\' NOT NULL, stoppedAt DATETIME DEFAULT NULL, isDemiNom BOOLEAN DEFAULT \'0\' NOT NULL, observation VARCHAR(255) DEFAULT NULL, tontineurData_id INTEGER NOT NULL)');
        $this->addSql('CREATE INDEX IDX_9659D57D8C4B14B ON unity (tontineur_id)');
        $this->addSql('CREATE INDEX IDX_9659D57DEB5C9FD ON unity (tontine_id)');
        $this->addSql('CREATE INDEX IDX_9659D57C7E4069D ON unity (avaliste_id)');
        $this->addSql('CREATE INDEX IDX_9659D577DA38D34 ON unity (tontineurData_id)');
        $this->addSql('CREATE INDEX IDX_9659D57642B8210 ON unity (admin_id)');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, admin_id INTEGER DEFAULT NULL, role_id INTEGER NOT NULL, parrain_id INTEGER DEFAULT NULL, pseudo VARCHAR(180) DEFAULT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) DEFAULT NULL, username VARCHAR(255) NOT NULL, address VARCHAR(255) DEFAULT NULL, telephone VARCHAR(255) DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME NOT NULL, locked BOOLEAN DEFAULT \'0\' NOT NULL, lockedAt DATETIME DEFAULT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D64986CC499D ON user (pseudo)');
        $this->addSql('CREATE INDEX IDX_8D93D649642B8210 ON user (admin_id)');
        $this->addSql('CREATE INDEX IDX_8D93D649D60322AC ON user (role_id)');
        $this->addSql('CREATE INDEX IDX_8D93D649DE2A7A37 ON user (parrain_id)');
        $this->addSql('CREATE TABLE user_interest (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, debt_id INTEGER NOT NULL, account_balance INTEGER NOT NULL, debt_balance INTEGER NOT NULL, account_interest INTEGER NOT NULL, debt_interest INTEGER NOT NULL, month VARCHAR(2) NOT NULL, year VARCHAR(4) NOT NULL)');
        $this->addSql('CREATE INDEX IDX_8CB3FE67A76ED395 ON user_interest (user_id)');
        $this->addSql('CREATE INDEX IDX_8CB3FE67240326A5 ON user_interest (debt_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE CotisationDay');
        $this->addSql('DROP TABLE DebtAvalist');
        $this->addSql('DROP TABLE DebtRenewal');
        $this->addSql('DROP TABLE account');
        $this->addSql('DROP TABLE "action"');
        $this->addSql('DROP TABLE assistance');
        $this->addSql('DROP TABLE assistance_type');
        $this->addSql('DROP TABLE contributor');
        $this->addSql('DROP TABLE cotisation_failure');
        $this->addSql('DROP TABLE debt');
        $this->addSql('DROP TABLE debt_interest');
        $this->addSql('DROP TABLE fund');
        $this->addSql('DROP TABLE module');
        $this->addSql('DROP TABLE operation');
        $this->addSql('DROP TABLE operation_type');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP TABLE role_useraction');
        $this->addSql('DROP TABLE tontine');
        $this->addSql('DROP TABLE tontine_tontineur');
        $this->addSql('DROP TABLE tontine_type');
        $this->addSql('DROP TABLE tontineur');
        $this->addSql('DROP TABLE tontineur_data');
        $this->addSql('DROP TABLE unity');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_interest');
    }
}
