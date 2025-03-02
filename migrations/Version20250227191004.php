<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250227191004 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE blog CHANGE image image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE evenement CHANGE url_image url_image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE formation CHANGE datedebut datedebut DATE DEFAULT NULL, CHANGE datefin datefin DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE partenaire CHANGE id_p id_p VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE company_name company_name VARCHAR(255) DEFAULT NULL, CHANGE matricule matricule VARCHAR(255) DEFAULT NULL, CHANGE image image VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE blog CHANGE image image VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE evenement CHANGE url_image url_image VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE formation CHANGE datedebut datedebut DATE DEFAULT \'NULL\', CHANGE datefin datefin DATE DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE partenaire CHANGE id_p id_p VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE user CHANGE company_name company_name VARCHAR(255) DEFAULT \'NULL\', CHANGE matricule matricule VARCHAR(255) DEFAULT \'NULL\', CHANGE image image VARCHAR(255) DEFAULT \'NULL\'');
    }
}
