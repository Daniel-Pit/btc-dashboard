<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180414090131 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE btc_market_data (id INT AUTO_INCREMENT NOT NULL, currency VARCHAR(64) NOT NULL, market VARCHAR(64) NOT NULL, ago_24h_change DOUBLE PRECISION DEFAULT NULL, ago_24h_high DOUBLE PRECISION DEFAULT NULL, ago_24h_low DOUBLE PRECISION DEFAULT NULL, ago_1w_change DOUBLE PRECISION DEFAULT NULL, ago_1w_high DOUBLE PRECISION DEFAULT NULL, ago_1w_low DOUBLE PRECISION DEFAULT NULL, ago_1m_change DOUBLE PRECISION DEFAULT NULL, ago_1m_high DOUBLE PRECISION DEFAULT NULL, ago_1m_low DOUBLE PRECISION DEFAULT NULL, ago_3m_change DOUBLE PRECISION DEFAULT NULL, ago_3m_high DOUBLE PRECISION DEFAULT NULL, ago_3m_low DOUBLE PRECISION DEFAULT NULL, ago_6m_change DOUBLE PRECISION DEFAULT NULL, ago_6m_high DOUBLE PRECISION DEFAULT NULL, ago_6m_low DOUBLE PRECISION DEFAULT NULL, ago_1y_change DOUBLE PRECISION DEFAULT NULL, ago_1y_high DOUBLE PRECISION DEFAULT NULL, ago_1y_low DOUBLE PRECISION DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE btc_market_data');
    }
}
