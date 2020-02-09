<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200111180129 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE produits DROP FOREIGN KEY FK_BE2DDF8C3BD38833');
        $this->addSql('DROP INDEX UNIQ_BE2DDF8C3BD38833 ON produits');
        $this->addSql('ALTER TABLE produits ADD rubrique VARCHAR(100) NOT NULL, DROP rubrique_id, CHANGE tva_id tva_id INT DEFAULT NULL, CHANGE couleur_id couleur_id INT DEFAULT NULL, CHANGE taille_id taille_id INT DEFAULT NULL, CHANGE promotion_id promotion_id INT DEFAULT NULL, CHANGE public public VARCHAR(5) DEFAULT NULL');
        $this->addSql('ALTER TABLE user_adresses CHANGE nom nom VARCHAR(20) DEFAULT NULL, CHANGE prenom prenom VARCHAR(20) DEFAULT NULL, CHANGE telephone telephone INT DEFAULT NULL, CHANGE code_postal code_postal INT DEFAULT NULL, CHANGE pays pays VARCHAR(100) DEFAULT NULL, CHANGE ville ville VARCHAR(100) DEFAULT NULL, CHANGE complement complement VARCHAR(255) DEFAULT NULL, CHANGE avatar avatar VARCHAR(255) DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL, CHANGE profession profession VARCHAR(255) DEFAULT NULL, CHANGE birthday birthday DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE promotion CHANGE code_promo code_promo VARCHAR(20) DEFAULT NULL, CHANGE start_date start_date DATETIME DEFAULT NULL, CHANGE end_date end_date DATETIME DEFAULT NULL, CHANGE remise remise INT DEFAULT NULL');
        $this->addSql('ALTER TABLE contact CHANGE entreprise entreprise VARCHAR(100) DEFAULT NULL, CHANGE fonction fonction VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE commandes CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE media CHANGE produits_id produits_id INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE commandes CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE contact CHANGE entreprise entreprise VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE fonction fonction VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE media CHANGE produits_id produits_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE produits ADD rubrique_id INT NOT NULL, DROP rubrique, CHANGE tva_id tva_id INT DEFAULT NULL, CHANGE couleur_id couleur_id INT DEFAULT NULL, CHANGE taille_id taille_id INT DEFAULT NULL, CHANGE promotion_id promotion_id INT DEFAULT NULL, CHANGE public public VARCHAR(5) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE produits ADD CONSTRAINT FK_BE2DDF8C3BD38833 FOREIGN KEY (rubrique_id) REFERENCES rubrique (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BE2DDF8C3BD38833 ON produits (rubrique_id)');
        $this->addSql('ALTER TABLE promotion CHANGE code_promo code_promo VARCHAR(20) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE start_date start_date DATETIME DEFAULT \'NULL\', CHANGE end_date end_date DATETIME DEFAULT \'NULL\', CHANGE remise remise INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user_adresses CHANGE nom nom VARCHAR(20) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE prenom prenom VARCHAR(20) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE telephone telephone INT DEFAULT NULL, CHANGE code_postal code_postal INT DEFAULT NULL, CHANGE pays pays VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE ville ville VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE complement complement VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE updated_at updated_at DATETIME DEFAULT \'NULL\', CHANGE avatar avatar VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE profession profession VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE birthday birthday DATE DEFAULT \'NULL\'');
    }
}
