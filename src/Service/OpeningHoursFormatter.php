<?php

namespace App\Service;

use App\Entity\Horaires;

/**
 * Formats opening hours in compact or full mode with FR/DE support.
 *
 * @author Stephane H.
 * @created 2026-03-12
 *
 * @inputs  Horaires[] array, locale string
 * @outputs Structured rows for compact/full display
 */
class OpeningHoursFormatter
{
    /**
     * @param Horaires[] $horaires
     * @param string     $locale
     * @return array<int, array{label:string, ranges:string, comment:string|null}>
     */
    public function formatCompact(array $horaires, string $locale): array
    {
        $dayOrder = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche'];
        $dayLabelsFr = ['lundi' => 'Lun', 'mardi' => 'Mar', 'mercredi' => 'Mer', 'jeudi' => 'Jeu', 'vendredi' => 'Ven', 'samedi' => 'Sam', 'dimanche' => 'Dim'];
        $dayLabelsDe = ['lundi' => 'Mo', 'mardi' => 'Di', 'mercredi' => 'Mi', 'jeudi' => 'Do', 'vendredi' => 'Fr', 'samedi' => 'Sa', 'dimanche' => 'So'];

        $isDe = str_starts_with($locale, 'de');
        $labels = $isDe ? $dayLabelsDe : $dayLabelsFr;

        // Build signatures per day
        $signatures = [];
        foreach ($horaires as $h) {
            $jour = $h->getJour();
            if (!\in_array($jour, $dayOrder, true)) {
                continue;
            }
            $index = array_search($jour, $dayOrder, true);
            $ranges = $this->buildRangeString($h);
            $comment = $isDe ? $h->getCommentaireDe() : $h->getCommentaire();
            $comment = $comment !== null ? trim($comment) : '';
            // Si aucun horaire et aucun commentaire → on ne montre rien (infos non renseignées)
            if ($ranges === null && $comment === '') {
                continue;
            }
            $signature = $ranges . '|' . $comment;
            $signatures[$signature]['days'][] = ['jour' => $jour, 'index' => $index];
            $signatures[$signature]['ranges'] = $ranges;
            $signatures[$signature]['comment'] = $comment !== '' ? $comment : null;
        }

        $rows = [];
        foreach ($signatures as $data) {
            $days = $data['days'];
            usort($days, fn ($a, $b) => $a['index'] <=> $b['index']);
            $runs = [];
            $currentRun = [$days[0]];
            for ($i = 1; $i < \count($days); $i++) {
                $prev = $days[$i - 1];
                $curr = $days[$i];
                if ($curr['index'] === $prev['index'] + 1) {
                    $currentRun[] = $curr;
                } else {
                    $runs[] = $currentRun;
                    $currentRun = [$curr];
                }
            }
            $runs[] = $currentRun;

            foreach ($runs as $run) {
                $first = $run[0]['jour'];
                $last = $run[\count($run) - 1]['jour'];
                if (\count($run) === 1) {
                    $label = $labels[$first];
                } else {
                    $label = sprintf('%s – %s', $labels[$first], $labels[$last]);
                }
                $rows[] = [
                    'label' => $label,
                    'ranges' => $data['ranges'],
                    'comment' => $data['comment'],
                ];
            }
        }

        // Keep order by first day index for readability
        usort($rows, function ($a, $b) use ($labels, $dayOrder) {
            $reverseLabels = array_flip($labels);
            $ai = array_search($reverseLabels[explode(' – ', $a['label'])[0]] ?? null, $dayOrder, true);
            $bi = array_search($reverseLabels[explode(' – ', $b['label'])[0]] ?? null, $dayOrder, true);
            return $ai <=> $bi;
        });

        return $rows;
    }

    /**
     * @param Horaires[] $horaires
     * @param string     $locale
     * @return array<int, array{day:string, ranges:string, comment:string|null}>
     */
    public function formatFull(array $horaires, string $locale): array
    {
        $labelsFr = ['lundi' => 'Lundi', 'mardi' => 'Mardi', 'mercredi' => 'Mercredi', 'jeudi' => 'Jeudi', 'vendredi' => 'Vendredi', 'samedi' => 'Samedi', 'dimanche' => 'Dimanche'];
        $labelsDe = ['lundi' => 'Montag', 'mardi' => 'Dienstag', 'mercredi' => 'Mittwoch', 'jeudi' => 'Donnerstag', 'vendredi' => 'Freitag', 'samedi' => 'Samstag', 'dimanche' => 'Sonntag'];
        $isDe = str_starts_with($locale, 'de');
        $labels = $isDe ? $labelsDe : $labelsFr;

        $result = [];
        foreach ($horaires as $h) {
            $jour = $h->getJour();
            $ranges = $this->buildRangeString($h);
            $comment = $isDe ? $h->getCommentaireDe() : $h->getCommentaire();
            $comment = $comment !== null && trim($comment) !== '' ? trim($comment) : null;

            // Si aucune information n'est renseignée pour ce jour (ni horaires ni commentaire),
            // on ne l'affiche pas en mode full non plus.
            if ($ranges === null && $comment === null) {
                continue;
            }

            $result[] = [
                'day' => $labels[$jour] ?? $jour,
                'ranges' => $ranges ?? '',
                'comment' => $comment,
            ];
        }

        return $result;
    }

    private function buildRangeString(Horaires $h): ?string
    {
        $parts = [];
        if ($h->getHeureDebutMatin() && $h->getHeureFinMatin()) {
            $parts[] = sprintf('%s-%s', $h->getHeureDebutMatin()->format('H:i'), $h->getHeureFinMatin()->format('H:i'));
        }
        if ($h->getHeureDebutApresMidi() && $h->getHeureFinApresMidi()) {
            $parts[] = sprintf('%s-%s', $h->getHeureDebutApresMidi()->format('H:i'), $h->getHeureFinApresMidi()->format('H:i'));
        }
        if (empty($parts)) {
            return null;
        }

        return implode(' / ', $parts);
    }
}

