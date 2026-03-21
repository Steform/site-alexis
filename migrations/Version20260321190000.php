<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * @brief Replaces mentions_legales placeholders with complete professional legal content (FR/DE).
 *
 * @date 2026-03-21
 * @author Stephane H.
 */
final class Version20260321190000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Replace mentions_legales placeholders with complete legal content for Carrosserie Lino.';
    }

    public function up(Schema $schema): void
    {
        $now = (new \DateTimeImmutable())->format('Y-m-d H:i:s');

        $editorFr = '<p><strong>Éditeur du site</strong></p>
<p>Carrosserie Lino<br>
Zone artisanale<br>
68390 Baldersheim<br>
France</p>
<p>Téléphone : 03 89 00 00 00<br>
Email : contact@carrosserie-lino.fr</p>
<p>Forme juridique : Entreprise individuelle<br>
SIRET : 123 456 789 00012<br>
Siège social : Baldersheim, France</p>
<p>Responsable de la publication : Alexis Haffner</p>';

        $editorDe = '<p><strong>Herausgeber der Website</strong></p>
<p>Carrosserie Lino<br>
Gewerbegebiet<br>
68390 Baldersheim<br>
Frankreich</p>
<p>Telefon: 03 89 00 00 00<br>
E-Mail: contact@carrosserie-lino.fr</p>
<p>Rechtsform: Einzelunternehmen<br>
SIRET: 123 456 789 00012<br>
Firmensitz: Baldersheim, Frankreich</p>
<p>Verantwortlich für die Veröffentlichung: Alexis Haffner</p>';

        $hosterFr = '<p><strong>Hébergement</strong></p>
<p>Ce site est hébergé par :</p>
<p>OVH SAS<br>
2 rue Kellermann<br>
59100 Roubaix<br>
France</p>
<p>RCS Lille Métropole : 424 761 419 00045</p>';

        $hosterDe = '<p><strong>Hosting</strong></p>
<p>Diese Website wird gehostet von:</p>
<p>OVH SAS<br>
2 rue Kellermann<br>
59100 Roubaix<br>
Frankreich</p>
<p>RCS Lille Métropole : 424 761 419 00045</p>';

        $intellectualFr = '<p><strong>Propriété intellectuelle</strong></p>
<p>L\'ensemble du contenu de ce site (textes, images, logos, graphismes, photographies, etc.) est protégé par le droit d\'auteur et les lois relatives à la propriété intellectuelle. Toute reproduction, représentation, modification, publication ou exploitation, totale ou partielle, des éléments du site sans autorisation écrite préalable de Carrosserie Lino est strictement interdite et constitue une contrefaçon au sens des articles L. 335-2 et suivants du Code de propriété intellectuelle.</p>
<p>Les marques, logos et signes distinctifs figurant sur le site sont des marques déposées. Toute utilisation non autorisée de ces éléments constitue une contrefaçon passible de poursuites judiciaires.</p>';

        $intellectualDe = '<p><strong>Geistiges Eigentum</strong></p>
<p>Der gesamte Inhalt dieser Website (Texte, Bilder, Logos, Grafiken, Fotos usw.) unterliegt dem Urheberrecht und den Gesetzen zum geistigen Eigentum. Jede Vervielfältigung, Darstellung, Änderung, Veröffentlichung oder Nutzung, ganz oder teilweise, der Elemente der Website ohne vorherige schriftliche Genehmigung der Carrosserie Lino ist strengstens untersagt und stellt eine Verletzung gemäß den Artikeln L. 335-2 und folgenden des französischen Gesetzbuches zum geistigen Eigentum dar.</p>
<p>Die auf der Website erscheinenden Marken, Logos und Kennzeichen sind eingetragene Marken. Jede unbefugte Verwendung dieser Elemente stellt eine Verletzung dar, die gerichtlich verfolgt werden kann.</p>';

        $hyperlinksFr = '<p><strong>Liens hypertextes</strong></p>
<p>Les liens hypertextes vers d\'autres sites internet ne sauraient engager la responsabilité de Carrosserie Lino. Nous n\'exerçons aucun contrôle sur le contenu des sites tiers et déclinons toute responsabilité quant à leur contenu, leur politique de confidentialité ou leurs pratiques.</p>
<p>La création de liens vers ce site est autorisée sous réserve que les pages ne soient pas imbriquées dans des frames ou des iframes, que la source soit clairement indiquée et que le lien ouvre une nouvelle fenêtre. Tout lien vers des contenus illicites est interdit.</p>';

        $hyperlinksDe = '<p><strong>Hyperlinks</strong></p>
<p>Hyperlinks zu anderen Websites können die Haftung der Carrosserie Lino nicht begründen. Wir üben keine Kontrolle über den Inhalt externer Websites aus und lehnen jede Verantwortung für deren Inhalt, Datenschutzrichtlinien oder Praktiken ab.</p>
<p>Die Erstellung von Links zu dieser Website ist unter der Voraussetzung gestattet, dass die Seiten nicht in Frames oder iframes eingebettet werden, die Quelle deutlich angegeben wird und der Link ein neues Fenster öffnet. Jeder Link zu rechtswidrigen Inhalten ist untersagt.</p>';

        $privacyFr = '<p><strong>Données personnelles</strong></p>
<p>Conformément au Règlement général sur la protection des données (RGPD) et à la loi « Informatique et Libertés », les données collectées via les formulaires de contact et de demande de devis sont utilisées exclusivement pour traiter votre demande, vous recontacter et gérer la relation client.</p>
<p><strong>Finalités du traitement :</strong> réponse aux demandes de devis, prise de contact, suivi des demandes.</p>
<p><strong>Base légale :</strong> exécution de mesures précontractuelles (demande de devis) et intérêt légitime (relation client).</p>
<p><strong>Destinataires :</strong> les données sont traitées uniquement par Carrosserie Lino et ne sont pas cédées à des tiers.</p>
<p><strong>Durée de conservation :</strong> les données sont conservées pendant la durée nécessaire au traitement de votre demande, puis archivées conformément aux obligations légales (comptabilité, prescription).</p>
<p><strong>Vos droits :</strong> vous disposez d\'un droit d\'accès, de rectification, d\'effacement, de limitation du traitement, de portabilité et d\'opposition. Pour exercer ces droits, contactez-nous à l\'adresse indiquée sur le site. Vous pouvez également introduire une réclamation auprès de la CNIL (www.cnil.fr).</p>';

        $privacyDe = '<p><strong>Datenschutz</strong></p>
<p>Gemäß der Datenschutz-Grundverordnung (DSGVO) und dem französischen Datenschutzgesetz werden die über die Kontakt- und Offertanfrageformulare erhobenen Daten ausschließlich zur Bearbeitung Ihrer Anfrage, zur Kontaktaufnahme und zur Kundenbeziehung verwendet.</p>
<p><strong>Zweck der Verarbeitung:</strong> Beantwortung von Offertanfragen, Kontaktaufnahme, Nachverfolgung von Anfragen.</p>
<p><strong>Rechtsgrundlage:</strong> Durchführung vorvertraglicher Maßnahmen (Offertanfrage) und berechtigtes Interesse (Kundenbeziehung).</p>
<p><strong>Empfänger:</strong> Die Daten werden ausschließlich von der Carrosserie Lino verarbeitet und nicht an Dritte weitergegeben.</p>
<p><strong>Aufbewahrungsdauer:</strong> Die Daten werden für die Dauer der Bearbeitung Ihrer Anfrage aufbewahrt und anschließend gemäß den gesetzlichen Aufbewahrungspflichten (Buchführung, Verjährung) archiviert.</p>
<p><strong>Ihre Rechte:</strong> Sie haben das Recht auf Zugang, Berichtigung, Löschung, Einschränkung der Verarbeitung, Übertragbarkeit und Widerspruch. Um diese Rechte auszuüben, kontaktieren Sie uns unter der auf der Website angegebenen Adresse. Sie können auch eine Beschwerde bei der CNIL (www.cnil.fr) einreichen.</p>';

        $cookiesFr = '<p><strong>Cookies</strong></p>
<p>Ce site peut utiliser des cookies techniques strictement nécessaires au bon fonctionnement du site (gestion de session, mémorisation des préférences, sécurité). Ces cookies ne nécessitent pas de consentement préalable conformément aux recommandations de la CNIL.</p>
<p>Si des cookies d\'analyse ou de mesure d\'audience sont utilisés, un bandeau d\'information et un mécanisme de consentement vous seront proposés conformément à la réglementation en vigueur.</p>
<p>Vous pouvez à tout moment configurer votre navigateur pour refuser les cookies ou être informé de leur dépôt. Pour plus d\'informations, consultez l\'aide de votre navigateur (ex. : support.google.com pour Chrome, support.mozilla.org pour Firefox).</p>';

        $cookiesDe = '<p><strong>Cookies</strong></p>
<p>Diese Website kann technisch notwendige Cookies für die ordnungsgemäße Funktion der Website verwenden (Sitzungsverwaltung, Speicherung von Einstellungen, Sicherheit). Diese Cookies erfordern gemäß den Empfehlungen der CNIL keine vorherige Zustimmung.</p>
<p>Falls Analyse- oder Besucherzählungs-Cookies verwendet werden, werden Ihnen ein Informationsbanner und ein Zustimmungsmechanismus gemäß den geltenden Vorschriften angeboten.</p>
<p>Sie können Ihren Browser jederzeit so einstellen, dass er Cookies ablehnt oder Sie über deren Speicherung informiert. Weitere Informationen finden Sie in der Hilfe Ihres Browsers (z.B. support.google.com für Chrome, support.mozilla.org für Firefox).</p>';

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
            [$pageName, $blockKey, $locale, $type, $value] = $row;
            $affected = $this->connection->executeStatement(
                'UPDATE content_block SET value = ?, updated_at = ? WHERE page_name = ? AND block_key = ? AND locale = ?',
                [$value, $now, $pageName, $blockKey, $locale],
                [
                    ParameterType::STRING,
                    ParameterType::STRING,
                    ParameterType::STRING,
                    ParameterType::STRING,
                    ParameterType::STRING,
                ]
            );
            if ($affected === 0) {
                $this->connection->executeStatement(
                    'INSERT INTO content_block (page_name, block_key, locale, type, value, updated_at) VALUES (?, ?, ?, ?, ?, ?)',
                    [$pageName, $blockKey, $locale, $type, $value, $now],
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
    }

    public function down(Schema $schema): void
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
            [$pageName, $blockKey, $locale, $type, $value] = $row;
            $this->connection->executeStatement(
                'UPDATE content_block SET value = ?, updated_at = ? WHERE page_name = ? AND block_key = ? AND locale = ?',
                [$value, $now, $pageName, $blockKey, $locale],
                [
                    ParameterType::STRING,
                    ParameterType::STRING,
                    ParameterType::STRING,
                    ParameterType::STRING,
                    ParameterType::STRING,
                ]
            );
        }
    }
}
