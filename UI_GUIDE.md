# AMIS Admin Portal — Premium UI Design System Guide

Welcome to the **AMIS Admin Portal** UI Design System. This document serves as the developer reference for building, extending, and maintaining consistent, high-fidelity UI components. 

The design language of `amis_admin` is carefully synchronized with the floating-card, soft-aesthetic model of `amis_student`, optimized for desktop-first workflows with zero inline styling, clean typography, standardized spacing scales, and lightweight SVG icons via Lucide.

---

## 1. Typography System

The typography scale utilizes **Plus Jakarta Sans** for expressive headings and **Inter** for highly readable body copy.

*   **Font Stack (Body):** `'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;`
*   **Font Stack (Headings):** `'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;`

### Heading Scales & Hierarchy
All headings are rendered with a letter-spacing of `-0.02em`, custom `line-height` ratios, and maximum weight classes:

| Element | CSS Target | Size (rem / px) | Weight / Style | Description |
| :--- | :--- | :--- | :--- | :--- |
| **H1** | `h1`, `.error-page-code` | `1.75rem` (28px) | 800 (Extra Bold) | Major portal titles, error code visuals |
| **H2** | `h2` | `1.375rem` (22px) | 800 (Extra Bold) | Card titles, detail header titles |
| **H3** | `h3` | `1.125rem` (18px) | 700 (Bold) | Module sections, mid-level headers |
| **H4** | `h4` | `0.9375rem` (15px) | 700 (Bold) | Nested card sections, sub-headings |
| **Body** | `body` | `0.875rem` (14px) | 400 (Regular) | Primary copy text, tables text, inputs |
| **Muted** | `.text-muted` | `0.875rem` (14px) | 400 / Muted | Secondary descriptions, helper texts |
| **Labels** | `.admin-form-group label` | `0.8125rem` (13px) | 600 (Semi-Bold) | Native HTML form label styling |
| **Captions** | `.text-xs`, `.badge` | `0.75rem` (12px) | 700 / All-caps | Status indicators, micro-metadata |

---

## 2. Color System

To achieve semantic clarity without looking generic, `amis_admin` uses curated, harmonic CSS color tokens.

### Theme & Grayscale Palette
Grayscale elements use a sophisticated slate color scale that avoids harsh black `#000000` text or borders.

```css
/* Core Variables */
--bg:          #eef0f7;  /* Premium soft lavender-slate background */
--surface:     #ffffff;  /* Primary container card color */
--surface-2:   #f8fafc;  /* Subtle nested grid or block shading */
--text-main:   #0f172a;  /* Slate-900: High-contrast legible body text */
--text-muted:  #64748b;  /* Slate-500: Secondary text / labels */
--border:      #e2e8f0;  /* Slate-200: Elegant component divider lines */
--border-soft: #f8fafc;  /* Slate-50: Ultra-light background rows */
```

### Semantic Status Colors
Every semantic state features a primary hue, a hover modifier, and a light background variant:

| State | Primary Variable | Hover Variable | Light Variant | Application |
| :--- | :--- | :--- | :--- | :--- |
| **Primary (Brand)**| `--primary` (`#059669`) | `--primary-hover` (`#047857`) | `--primary-light` (`#ecfdf5`) | Base buttons, active nav elements |
| **Success** | `--success` (`#10b981`) | `--success-hover` (`#059669`) | `--success-light` (`#ecfdf5`) | Approved files, paid accounts, active |
| **Warning** | `--warning` (`#f59e0b`) | `--warning-hover` (`#d97706`) | `--warning-light` (`#fffbeb`) | Pending reviews, draft modes |
| **Danger** | `--danger` (`#ef4444`) | `--danger-hover` (`#dc2626`) | `--danger-light` (`#fef2f2`) | Rejected files, deleted data actions |
| **Info** | `--info` (`#3b82f6`) | `--info-hover` (`#2563eb`) | `--info-light` (`#eff6ff`) | Microsoft Sync states, audit details |

---

## 3. Spacing System (4px Base Grid)

All margins, paddings, and flexbox gaps conform strictly to a **4px geometric grid scale** to eliminate random spacing hacks. Do not write inline styles for spacing; use the dedicated utility classes.

### Custom Spacing Scale Variables
```css
--sp-1:  4px;
--sp-2:  8px;
--sp-3:  12px;
--sp-4:  16px;
--sp-5:  20px;
--sp-6:  24px;
--sp-8:  32px;
--sp-10: 40px;
--sp-12: 48px;
--sp-16: 64px;
```

### Spacing Utility Classes
Always leverage these shorthand utilities to separate structural markup:

*   **Paddings:** `.p-1` (4px), `.p-2` (8px), `.p-3` (12px), `.p-4` (16px), `.p-6` (24px)
*   **Margins:** `.m-1`, `.m-2`, `.m-3`, `.m-4`
*   **Margins (Bottom):** `.mb-1`, `.mb-2` (8px), `.mb-3` (12px), `.mb-4` (16px), `.mb-6` (24px)
*   **Margins (Top):** `.mt-2` (8px), `.mt-3` (12px), `.mt-6` (24px)
*   **Flex Gaps:** `.gap-1` (4px), `.gap-2` (8px), `.gap-3` (12px), `.gap-4` (16px), `.gap-6` (24px)

---

## 4. Elevation (Shadow Hierarchy)

We maintain a flat, clean floating-card appearance. Massive diffuse shadows are strictly forbidden.

*   `--shadow-none`: Removes shadow properties (used for tables, flat lists, buttons).
*   `--shadow-sm` (`.shadow-sm`): Minimal 1px vertical offset. Perfect for small cards, badges, and top headers.
*   `--shadow-md` (`.shadow-md`): Mid-size shadow with low-opacity slate offsets. Default for primary card modules, floating modals, and toast notifications.
*   `--shadow-lg` (`.shadow-lg`): Dramatic dropdown/overlay layer elevation.

---

## 5. Z-Index Layer System

To eliminate overlap visual bugs where elements float above headers or models, all components must utilize the standard layer variables:

```css
--z-sticky:   100;   /* Sticky sidebar and local actions */
--z-header:   200;   /* Sticky table headers */
--z-dropdown: 300;   /* Profile and action dropdown panels */
--z-overlay:  500;   /* Modal dark backdrops */
--z-modal:    1000;  /* Modal dialogue boxes */
--z-toast:    2000;  /* Toast notification systems */
```

---

## 6. Icons System (Lucide Wrapper)

Static SVG bloat is completely eliminated. AMIS Admin Portal uses a highly responsive, cached Lucide wrapper.

### How to use Lucide Icons
1. Add an `<i>` element with a `data-lucide` attribute representing the name of the icon:
   ```html
   <i data-lucide="shield-check" class="toast-icon"></i>
   <i data-lucide="trash-2"></i>
   ```
2. On DOM load, Lucide automatically parses the page and swaps the elements for clean, lightweight SVGs.
3. Class rules: Use `.toast-icon` or standard dimensions. Sidebar and layout icons default to uniform `18px` squares automatically configured in CSS.

---

## 7. Premium Components & Code Snippets

Use these standardized HTML frameworks to build responsive views.

### A. Buttons System
Buttons should always employ semantic classes rather than custom selectors.

```html
<!-- Primary CTA Button -->
<button class="btn btn-primary">
    <i data-lucide="check"></i> Approve Record
</button>

<!-- Secondary / Cancel Button -->
<a href="#" class="btn btn-secondary">
    <i data-lucide="x"></i> Cancel Action
</a>

<!-- Danger / Reject Button -->
<button class="btn btn-danger btn-sm">
    <i data-lucide="slash"></i> Reject File
</button>

<!-- Loading State (Trigger class is-loading dynamically via JS) -->
<button class="btn btn-primary btn-loading-ready">
    <span class="btn-loading-spinner"></span>
    <span class="btn-loading-label">Submit Form</span>
</button>
```

### B. Cards System
The foundational floating-card layout is standard for dashboards and indexes.

```html
<div class="admin-card shadow-md">
    <div class="admin-card-header">
        <h2 class="admin-card-title">Enrolled Students</h2>
        <span class="badge badge-green">Active Session</span>
    </div>
    <div class="p-4">
        <!-- Content goes here -->
    </div>
</div>
```

### C. Forms & Validation System
Form elements should be contained in standardized form groups. Validation states append clean borders and clear labels.

```html
<div class="admin-form-group">
    <label for="student_email">Student Email Address <span class="text-danger">*</span></label>
    <div class="input-wrapper">
        <i data-lucide="mail" class="input-icon"></i>
        <input type="email" id="student_email" class="admin-input input-with-left-icon" placeholder="name@domain.edu" required>
    </div>
    <span class="form-help">Ensure you use the official institutional account.</span>
</div>

<!-- Validation Error State (Add custom style/class wrapper) -->
<div class="admin-form-group">
    <label for="phone">Mobile Phone</label>
    <input type="text" id="phone" class="admin-input" style="border-color: var(--danger)">
    <span class="text-danger text-xs mt-1">This contact number is invalid.</span>
</div>
```

### D. Table System (With Sticky Headers)
Designed to scroll vertically inside high-resolution screens without losing table labels:

```html
<div class="admin-table-container">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Applicant Name</th>
                <th>Course Program</th>
                <th>Status</th>
                <th style="text-align: right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="font-semibold">Lingasa, Darius</td>
                <td>BS Computer Science</td>
                <td><span class="badge badge-yellow">Pending Review</span></td>
                <td style="text-align: right;">
                    <a href="#" class="btn btn-secondary btn-sm">Review File</a>
                </td>
            </tr>
        </tbody>
    </table>
</div>
```

### E. Badges System
Badges represent current system statuses.

```html
<span class="badge badge-yellow">Pending Review</span>
<span class="badge badge-green">Approved / Active</span>
<span class="badge badge-red">Rejected / Inactive</span>
<span class="badge badge-blue">Processing Synced</span>
<span class="badge badge-gray">Draft Mode</span>
```

---

## 8. Development & Review Checklist
When writing or updating administrative views:
1.  **Strict 300-Line Limit:** Blade template files must stay below **300 lines of code** each.
2.  **No Inline CSS Style Attributes:** All visual layout adjustments must utilize design system variables and semantic classes inside `admin.css`.
3.  **Desktop-First Workflows:** Focus on elegant dashboard presentation without introducing mobile shifting properties.
4.  **Alpine JS for Reactivity:** Manage dropdowns, modals, and list filter state transitions dynamically via Alpine stores.
