# Story 8.1: Responsive

Status: ready-for-dev

<!-- Note: Validation is optional. Run validate-create-story for quality check before dev-story. -->

## Story

As a **visitor** (mobile, tablet or desktop),
I want **the site to display correctly and be usable on my device** (responsive layout, readable content, working navigation),
so that **I can find information and contact the garage regardless of screen size**.

## Acceptance Criteria

1. **Bootstrap breakpoints** — Layout respects Bootstrap 5 breakpoints (sm, md, lg, xl). Mobile-first approach: base styles for mobile, then `min-width` media queries / Bootstrap grid for larger viewports.
2. **Mobile (< 768px)** — Navigation uses hamburger menu (navbar toggler); menu collapses into toggle; cartes/services in single column; CTA "Demander un devis" visible and tappable; layout matches maquette intent on small screens.
3. **Tablet (768px–991px)** — Layout adapts: grid 2–3 columns where appropriate; nav can remain collapsed or expand per Bootstrap `navbar-expand-*`; sections (hero, cartes, Qui sommes-nous, témoignage, footer) readable and aligned with maquette.
4. **Desktop (≥ 992px)** — Full layout maquette: horizontal nav, grille services (e.g. 3–4 colonnes), barre contact, hero, cartes, sections et footer conformes à la maquette.
5. **Consistency** — All public pages (index, devis, qui-sommes-nous, galerie, horaires, avis, mentions légales) use the same responsive patterns and same navbar/partials; back-office layout also responsive (navbar collapse on small screens).

## Tasks / Subtasks

- [ ] **AC1–2** Verify Bootstrap breakpoints and mobile layout
  - [ ] Confirm Bootstrap 5 grid and utilities (container, row, col-*, d-*, etc.) are used consistently
  - [ ] Confirm navbar uses `navbar-expand-lg` and toggler/collapse for mobile; test hamburger opens/closes
  - [ ] Confirm hero, cartes, sections stack in one column on mobile; CTA visible
- [ ] **AC3** Tablet layout
  - [ ] Test 768px–991px: grid adapts (2–3 cols), nav behavior correct
  - [ ] No horizontal scroll; readable typography and spacing
- [ ] **AC4** Desktop layout
  - [ ] Test ≥ 992px: horizontal nav, full maquette layout (barre contact, hero, cartes, grille services, témoignage, footer)
  - [ ] Align with UX spec and maquette (palette, structure)
- [ ] **AC5** Consistency across pages
  - [ ] Index, devis, qui-sommes-nous, galerie, horaires, avis, mentions légales: same navbar partial, same responsive behavior
  - [ ] Back-office: navbar collapse on small screens; no layout break

## Dev Notes

- **Architecture:** Symfony 8+, Twig, Bootstrap 5. Responsive is Bootstrap breakpoints + custom CSS only where needed. [Source: architecture.md]
- **UX spec:** Mobile-first; breakpoints: Mobile &lt; 768px, Tablet 768–991px, Desktop ≥ 992px. Nav hamburger on mobile; cartes en colonne; CTA visible. [Source: ux-design-specification.md § Responsive & Accessibility]
- **Existing code:** `templates/partials/_navbar_public.html.twig` already uses `navbar-expand-lg`, `navbar-toggler`, `collapse navbar-collapse` (Bootstrap 5). Back `templates/back/base.html.twig` uses same pattern. Verify all public pages include the same navbar partial and that no page overrides breakpoints incorrectly.
- **Files to check:** All templates in `templates/public/`, `templates/partials/`, `templates/back/`; base layout and any page-specific CSS that could break responsive (fixed widths, missing container/row/col).
- **Testing:** Manual viewport resize (Chrome DevTools); test 320px, 768px, 992px, 1200px. No regressions on existing pages.

### Project Structure Notes

- Templates: `templates/public/`, `templates/partials/`, `templates/back/`
- Assets/CSS: align with existing Bootstrap + custom (e.g. `navbar-main`, variables in `public/index` or assets)
- No new routes required; story is layout/CSS and template consistency only.

### References

- [Source: _bmad-output/planning-artifacts/ux-design-specification.md § Responsive & Accessibility] — Breakpoints, mobile nav, layout
- [Source: _bmad-output/planning-artifacts/architecture.md] — Bootstrap, responsive, assets
- [Source: _bmad-output/planning-artifacts/epics.md § Epic 8] — 8.1 Responsive acceptance criteria

## Dev Agent Record

### Agent Model Used

-

### Debug Log References

-

### Completion Notes List

-

### File List

-
