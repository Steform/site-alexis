<?php

declare(strict_types=1);

namespace App\Service;

/**
 * @brief Domain keys for entity snapshot history (filter prefix entity:).
 *
 * @date 2026-03-22
 * @author Stephane H.
 */
final class EntitySnapshotDomain
{
    public const HORAIRES = 'horaires';

    public const COORDINATES = 'coordinates';

    public const AVIS = 'avis';

    public const MESSAGE = 'message';

    public const DEVIS_PRESTATION = 'devis_prestation';

    public const DEVIS_CARBURANT = 'devis_carburant';

    public const SERVICES_WHY_CARD = 'services_why_card';

    public const SERVICE_PROCESS_STEP = 'service_process_step';

    public const SERVICE = 'service';

    public const GALLERY_ITEM = 'gallery_item';

    public const ABOUT_PHOTO = 'about_photo';

    public const HOME_HERO_PHOTO = 'home_hero_photo';

    public const USER = 'user';
}
