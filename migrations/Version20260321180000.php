<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * @brief Seeds mentions_legales content blocks (FR/DE) for Carrosserie Lino.
 *
 * @date 2026-03-21
 * @author Stephane H.
 */
final class Version20260321180000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Seed mentions_legales content blocks with professional legal content (FR/DE).';
    }

    public function up(Schema $schema): void
    {
        $now = (new \DateTimeImmutable())->format('Y-m-d H:i:s');

        $editorFr = '<p><strong>Éditeur du site</strong></p>
<p>Carrosserie Lino<br>
[Adresse complète à renseigner dans Coordonnées]<br>
Téléphone : [à compléter]<br>
Email : [à compléter]</p>
<p>Forme juridique : [Entreprise individuelle / SARL / etc.]<br>
SIRET : [à compléter]<br>
Siège social : Baldersheim, France</p>
<p>Responsable de la publication : Alexis Haffner</p>';

        $editorDe = '<p><strong>Herausgeber der Website</strong></p>
<p>Carrosserie Lino<br>
[Vollständige Adresse in Kontaktdaten ergänzen]<br>
Telefon: [zu ergänzen]<br>
E-Mail: [zu ergänzen]</p>
<p>Rechtsform: [Einzelunternehmen / GmbH / etc.]<br>
SIRET: [zu ergänzen]<br>
Firmensitz: Baldersheim, Frankreich</p>
<p>Verantwortlich für die Veröffentlichung: Alexis Haffner</p>';

        $hosterFr = '<p><strong>Hébergement</strong></p>
<p>Ce site est hébergé par :<br>
[Nom de l\'hébergeur – ex. OVH, Gandi, etc.]<br>
[Adresse complète de l\'hébergeur]<br>
[Contact hébergeur]</p>
<p>Merci de mettre à jour ces informations selon votre hébergeur réel.</p>';

        $hosterDe = '<p><strong>Hosting</strong></p>
<p>Diese Website wird gehostet von:<br>
[Name des Hosting-Anbieters – z.B. OVH, Gandi, etc.]<br>
[Vollständige Adresse des Anbieters]<br>
[Kontakt Hosting-Anbieter]</p>
<p>Bitte aktualisieren Sie diese Angaben entsprechend Ihrem tatsächlichen Anbieter.</p>';

        $intellectualFr = '<p><strong>Propriété intellectuelle</strong></p>
<p>L\'ensemble du contenu de ce site (textes, images, logos, graphismes, etc.) est protégé par le droit d\'auteur et les lois sur la propriété intellectuelle. Toute reproduction, représentation, modification ou exploitation, totale ou partielle, sans autorisation écrite préalable de Carrosserie Lino est strictement interdite.</p>
<p>Les marques et logos figurant sur le site sont des marques déposées. Toute utilisation non autorisée constitue une contrefaçon.</p>';

        $intellectualDe = '<p><strong>Geistiges Eigentum</strong></p>
<p>Der gesamte Inhalt dieser Website (Texte, Bilder, Logos, Grafiken usw.) unterliegt dem Urheberrecht und den Gesetzen zum geistigen Eigentum. Jede Vervielfältigung, Darstellung, Änderung oder Nutzung, ganz oder teilweise, ohne vorherige schriftliche Genehmigung der Carrosserie Lino ist strengstens untersagt.</p>
<p>Die auf der Website erscheinenden Marken und Logos sind eingetragene Marken. Jede unbefugte Verwendung stellt eine Verletzung dar.</p>';

        $hyperlinksFr = '<p><strong>Liens hypertextes</strong></p>
<p>Les liens hypertextes vers d\'autres sites internet ne sauraient engager la responsabilité de Carrosserie Lino. Nous n\'exerçons aucun contrôle sur le contenu des sites tiers et déclinons toute responsabilité quant à leur contenu.</p>
<p>La création de liens vers ce site est autorisée sous réserve que les pages ne soient pas imbriquées dans des frames et que la source soit clairement indiquée.</p>';

        $hyperlinksDe = '<p><strong>Hyperlinks</strong></p>
<p>Hyperlinks zu anderen Websites können die Haftung der Carrosserie Lino nicht begründen. Wir üben keine Kontrolle über den Inhalt externer Websites aus und lehnen jede Verantwortung für deren Inhalt ab.</p>
<p>Die Erstellung von Links zu dieser Website ist unter der Voraussetzung gestattet, dass die Seiten nicht in Frames eingebettet werden und die Quelle deutlich angegeben wird.</p>';

        $privacyFr = '<p><strong>Données personnelles</strong></p>
<p>Conformément au Règlement général sur la protection des données (RGPD), les données collectées via les formulaires de contact et de demande de devis sont utilisées uniquement pour traiter votre demande et vous recontacter.</p>
<p>Vos données ne sont pas cédées à des tiers. Vous disposez d\'un droit d\'accès, de rectification, de suppression et de portabilité de vos données. Pour exercer ces droits, contactez-nous à l\'adresse indiquée sur le site.</p>
<p>Les données sont conservées pendant la durée nécessaire au traitement de votre demande, puis archivées conformément aux obligations légales.</p>';

        $privacyDe = '<p><strong>Datenschutz</strong></p>
<p>Gemäß der Datenschutz-Grundverordnung (DSGVO) werden die über die Kontakt- und Offertanfrageformulare erhobenen Daten ausschließlich zur Bearbeitung Ihrer Anfrage und zur Kontaktaufnahme verwendet.</p>
<p>Ihre Daten werden nicht an Dritte weitergegeben. Sie haben das Recht auf Zugang, Berichtigung, Löschung und Übertragbarkeit Ihrer Daten. Um diese Rechte auszuüben, kontaktieren Sie uns unter der auf der Website angegebenen Adresse.</p>
<p>Die Daten werden für die Dauer der Bearbeitung Ihrer Anfrage aufbewahrt und anschließend gemäß den gesetzlichen Aufbewahrungspflichten archiviert.</p>';

        $cookiesFr = '<p><strong>Cookies</strong></p>
<p>Ce site peut utiliser des cookies techniques nécessaires au bon fonctionnement du site (session, préférences). Ces cookies ne nécessitent pas de consentement préalable.</p>
<p>Si des cookies d\'analyse ou de mesure d\'audience sont utilisés, un bandeau d\'information et un mécanisme de consentement vous seront proposés conformément à la réglementation.</p>
<p>Vous pouvez configurer votre navigateur pour refuser les cookies ou être informé de leur dépôt.</p>';

        $cookiesDe = '<p><strong>Cookies</strong></p>
<p>Diese Website kann technisch notwendige Cookies für die ordnungsgemäße Funktion der Website verwenden (Sitzung, Einstellungen). Diese Cookies erfordern keine vorherige Zustimmung.</p>
<p>Falls Analyse- oder Besucherzählungs-Cookies verwendet werden, werden Ihnen ein Informationsbanner und ein Zustimmungsmechanismus gemäß den Vorschriften angeboten.</p>
<p>Sie können Ihren Browser so einstellen, dass er Cookies ablehnt oder Sie über deren Speicherung informiert.</p>';

        $blocks = [
            ['mentions_legales', 'editor', 'fr', 'rich', $editorFr],
            ['mentions_legales', 'hoster', 'fr', 'rich', $hosterFr],
            ['mentions_legales', 'intellectual_property', 'fr', 'rich', $intellectualFr],
            ['mentions_legales', 'hyperlinks', 'fr', 'rich', $hyperlinksFr],
            ['mentions_legales', 'personal_data', 'fr', 'rich', $privacyFr],
            ['mentions_legales', 'cookies', 'fr', 'rich', $cookiesFr],
            ['mentions_legales', 'editor', 'de', 'rich', $editorDe],
            ['mentions_legales', 'hoster', 'de', 'rich', $hosterDe],
            ['mentions_legales', 'intellectual_property', 'de', 'rich', $intellectualDe],
            ['mentions_legales', 'hyperlinks', 'de', 'rich', $hyperlinksDe],
            ['mentions_legales', 'personal_data', 'de', 'rich', $privacyDe],
            ['mentions_legales', 'cookies', 'de', 'rich', $cookiesDe],
        ];

        foreach ($blocks as $row) {
            $this->connection->executeStatement(
                'INSERT INTO content_block (page_name, block_key, locale, type, value, updated_at) VALUES (?, ?, ?, ?, ?, ?)',
                [$row[0], $row[1], $row[2], $row[3], $row[4], $now],
                [
                    ParameterType::STRING,
                    ParameterType::STRING,
                    ParameterType::STRING,
                    ParameterType::STRING,
                    ParameterType::STRING,
                    ParameterType::STRING,
                ]
            );
        }
    }

    public function down(Schema $schema): void
    {
        $this->connection->executeStatement(
            "DELETE FROM content_block WHERE page_name = 'mentions_legales'"
        );
    }
}
