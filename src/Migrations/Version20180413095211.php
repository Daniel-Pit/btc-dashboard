<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180413095211 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE bitfinex_eur_history (id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, spot DOUBLE PRECISION NOT NULL, insert_time INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bitfinex_usd_history (id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, spot DOUBLE PRECISION NOT NULL, insert_time INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bitstamp_eur_history (id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, spot DOUBLE PRECISION NOT NULL, insert_time INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bitstamp_usd_history (id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, spot DOUBLE PRECISION NOT NULL, insert_time INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE gdax_eur_history (id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, spot DOUBLE PRECISION NOT NULL, insert_time INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE gdax_usd_history (id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, spot DOUBLE PRECISION NOT NULL, insert_time INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE bitfinex_eur_history');
        $this->addSql('DROP TABLE bitfinex_usd_history');
        $this->addSql('DROP TABLE bitstamp_eur_history');
        $this->addSql('DROP TABLE bitstamp_usd_history');
        $this->addSql('DROP TABLE gdax_eur_history');
        $this->addSql('DROP TABLE gdax_usd_history');
    }
}
