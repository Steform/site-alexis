<?php

declare(strict_types=1);

namespace App\Service;

/**
 * @brief Action codes for AdminAuditLog (prefix before dot = domain for filters).
 *
 * @date 2026-03-22
 * @author Stephane H.
 */
final class AdminAuditActions
{
    public const HORAIRES_UPDATE = 'horaires.update';

    public const COORDINATES_UPDATE = 'coordinates.update';

    public const AVIS_CREATE = 'avis.create';

    public const AVIS_UPDATE = 'avis.update';

    public const AVIS_DELETE = 'avis.delete';

    public const MESSAGE_CREATE = 'message.create';

    public const MESSAGE_UPDATE = 'message.update';

    public const MESSAGE_DELETE = 'message.delete';

    public const USER_CREATE = 'user.create';

    public const USER_UPDATE = 'user.update';

    public const USER_DELETE = 'user.delete';

    public const DEVIS_PRESTATION_CREATE = 'devis.prestation.create';

    public const DEVIS_PRESTATION_UPDATE = 'devis.prestation.update';

    public const DEVIS_PRESTATION_DELETE = 'devis.prestation.delete';

    public const DEVIS_CARBURANT_CREATE = 'devis.carburant.create';

    public const DEVIS_CARBURANT_UPDATE = 'devis.carburant.update';

    public const DEVIS_CARBURANT_DELETE = 'devis.carburant.delete';

    public const SERVICES_WHY_CARD_CREATE = 'services.why_card.create';

    public const SERVICES_WHY_CARD_UPDATE = 'services.why_card.update';

    public const SERVICES_WHY_CARD_DELETE = 'services.why_card.delete';

    public const SERVICE_PROCESS_STEP_CREATE = 'service.process_step.create';

    public const SERVICE_PROCESS_STEP_UPDATE = 'service.process_step.update';

    public const SERVICE_PROCESS_STEP_DELETE = 'service.process_step.delete';

    public const SERVICES_TEASER_IMAGE_UPDATE = 'services.teaser_image.update';

    public const SERVICE_DETAIL_HERO_IMAGE_UPDATE = 'service.detail_hero_image.update';

    public const MAINTENANCE_TOGGLE = 'maintenance.toggle';

    public const BACKUP_CREATE = 'backup.create';

    public const BACKUP_DELETE = 'backup.delete';

    public const BACKUP_RESTORE = 'backup.restore';

    public const BACKUP_UPLOAD_RESTORE = 'backup.upload_restore';

    public const SLIDER_ABOUT_REORDER = 'slider.about.reorder';

    public const SLIDER_HERO_REORDER = 'slider.hero.reorder';

    public const GALLERY_ITEM_CREATE = 'gallery.item.create';

    public const GALLERY_ITEM_UPDATE = 'gallery.item.update';
}
