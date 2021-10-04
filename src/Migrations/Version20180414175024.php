<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180414175024 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE INDEX bitfinex_eur_idx ON bitfinex_eur_history (insert_time)');
        $this->addSql('CREATE INDEX bitfinex_usd_idx ON bitfinex_usd_history (insert_time)');
        $this->addSql('CREATE INDEX bitstamp_eur_idx ON bitstamp_eur_history (insert_time)');
        $this->addSql('CREATE INDEX bitstamp_usd_idx ON bitstamp_usd_history (insert_time)');
        $this->addSql('CREATE INDEX gdax_eur_idx ON gdax_eur_history (insert_time)');
        $this->addSql('CREATE INDEX gdax_usd_idx ON gdax_usd_history (insert_time)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX bitfinex_eur_idx ON bitfinex_eur_history');
        $this->addSql('DROP INDEX bitfinex_usd_idx ON bitfinex_usd_history');
        $this->addSql('DROP INDEX bitstamp_eur_idx ON bitstamp_eur_history');
        $this->addSql('DROP INDEX bitstamp_usd_idx ON bitstamp_usd_history');
        $this->addSql('DROP INDEX gdax_eur_idx ON gdax_eur_history');
        $this->addSql('DROP INDEX gdax_usd_idx ON gdax_usd_history');
    }
}
