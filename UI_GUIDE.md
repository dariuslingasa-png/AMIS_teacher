# AMIS Admin Portal — Premium UI/UX Design System Guide

Welcome to the modernized **AMIS Admin Portal** UI Design System. This document serves as the developer source-of-truth for building, extending, and maintaining consistent, high-fidelity UI modules.

The design language of `amis_admin` is carefully synchronized with the floating-card, soft-aesthetic models of the Enrollment and Finance workspaces, optimized for desktop-first admin workflows with zero inline styling, clean typography, standardized spacing scales, custom responsive gradients, and native Tailwind CSS v4 directives.

---

## 1. Typography System

The typography scale utilizes **Plus Jakarta Sans** for expressive headings and **Inter** for highly readable body copy.

*   **Font Stack (Body):** `'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;`
*   **Font Stack (Headings):** `'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;`

### Heading Scales & Hierarchy
All headings are rendered with a letter-spacing of `-0.02em`, custom `line-height` ratios, and maximum weight classes:

| Element | CSS Target | Size (rem / px) | Weight / Style | Description |
| :--- | :--- | :--- | :--- | :--- |
| **H1** | `h1` | `1.75rem` (28px) | 900 (Black) | Major portal titles, workspace hero headings |
| **H2** | `h2` | `1.375rem` (22px) | 800 (Extra Bold) | Card titles, detail header titles |
| **H3** | `h3` | `1.125rem` (18px) | 700 (Bold) | Module sections, mid-level headers |
| **H4** | `h4` | `0.9375rem` (15px) | 700 (Bold) | Nested card sections, sub-headings |
| **Body** | `body` | `0.875rem` (14px) | 500 (Medium) | Primary copy text, tables text, inputs |
| **Muted** | `.text-muted` | `0.875rem` (14px) | 400 (Regular) | Secondary descriptions, helper texts |
| **Labels** | `label` | `0.8125rem` (13px) | 700 (Bold) | Form label styling |
| **Captions** | `.text-xs`, `.badge` | `0.75rem` (12px) | 800 (Extra Bold) | Status indicators, micro-metadata |

---

## 2. Color System & Custom Theme (Tailwind CSS v4)

To achieve semantic clarity without looking generic, `amis_admin` uses curated, harmonic CSS color tokens registered in the `@theme` directive in `app.css`.

### Core Palette
Grayscale elements use a slate color scale that avoids harsh black `#000000` text or borders.

```css
@theme {
    /* Brand Color Scale (Emerald) */
    --color-primary-50:  #ecfdf5;
    --color-primary-500: #10b981;
    --color-primary-700: #047857;
    --color-primary-900: #064e3b;

    /* Custom Premium Grays & Borders (Solves default outline fallback) */
    --color-gray-150:    #ebedf2;  /* Light, soft border for tables and list grids */
    --color-slate-150:   #eef2f6;  /* Sleek, ultra-soft border for container cards */
}
```

### Semantic Status Colors
Every semantic state features a primary hue, a hover modifier, and a light background variant:

| State | CSS Variable | Light Variant | Application |
| :--- | :--- | :--- | :--- |
| **Primary** | `--primary-500` (`#10b981`) | `--primary-50` (`#ecfdf5`) | Base buttons, active nav elements |
| **Success** | `--success` (`#10b981`) | `--success-light` (`#ecfdf5`) | Approved status badges, verified items |
| **Warning** | `--warning` (`#f59e0b`) | `--warning-light` (`#fffbeb`) | Pending reviews, draft modes |
| **Danger** | `--danger` (`#ef4444`) | `--danger-light` (`#fef2f2`) | Rejected payments, deleted actions |
| **Info** | `--info` (`#3b82f6`) | `--info-light` (`#eff6ff`) | Microsoft Sync states, audit details |

---

## 3. Premium Banners & Gradients

Dashboard banners are highly expressive and use organic fluid gradients with glassy overlay blur orbs to deliver a state-of-the-art visual first impression.

### Academic Hero Banner
Apply the `.academic-hero-banner` class to the container:
```html
<div class="academic-hero-banner">
    <!-- Glowing Blur Orbs -->
    <div class="absolute right-0 top-0 -mt-4 -mr-4 w-56 h-56 rounded-full bg-indigo-500/15 blur-3xl"></div>
    <div class="absolute left-1/3 bottom-0 -mb-8 w-64 h-64 rounded-full bg-sky-500/10 blur-3xl"></div>
    
    <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold bg-white/10 text-indigo-100 rounded-full border border-white/10 backdrop-blur-xs mb-3">
                <span class="w-1.5 h-1.5 rounded-full bg-sky-400 animate-pulse"></span>
                Academic Workspace
            </span>
            <h1 class="text-2xl md:text-3xl font-black tracking-tight text-white">Teachers Directory</h1>
            <p class="mt-2 text-sm md:text-base text-indigo-100 max-w-2xl font-light">
                Manage faculty details, department classifications, and school email contacts.
            </p>
        </div>
        <button type="button" class="inline-flex items-center gap-2 bg-white hover:bg-indigo-50 active:bg-indigo-100 text-indigo-950 font-black text-sm px-5 py-2.5 rounded-xl transition-all duration-150 shadow-md shadow-indigo-950/20 hover:scale-[1.02] cursor-pointer">
            <i data-lucide="plus-circle" class="w-4 h-4 text-indigo-700"></i>
            Register Teacher
        </button>
    </div>
</div>
```

---

## 4. Interaction States: Loading Spinner & Skeletons

To provide excellent user feedback, developers must employ visual loading components for all network-bound, filter, search, or form operations.

### A. Button Loading Spinner
Toggle `.btn-loading` on the button to hide text and show the loading spinner:
```html
<button type="button" class="relative inline-flex items-center justify-center px-5 py-2 text-xs font-bold text-white bg-indigo-700 hover:bg-indigo-600 rounded-xl transition cursor-pointer min-w-[125px] btn-loading">
    <!-- Spinning Loader Orb -->
    <span class="btn-spinner"></span>
    <span class="btn-text-content">Register Teacher</span>
</button>
```

### B. Skeleton Loader Cards & Rows
Use `.skeleton-box` blocks to form an elegant layout structure during page transitions or live search operations:
```html
<!-- Table Rows Skeleton (Search loading state) -->
<template x-if="showSkeleton">
    <tr>
        <td class="px-6 py-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full skeleton-box shrink-0"></div>
                <div class="h-4 w-32 skeleton-box"></div>
            </div>
        </td>
        <td class="px-6 py-4"><div class="h-3.5 w-44 skeleton-box"></div></td>
        <td class="px-6 py-4"><div class="h-3.5 w-28 skeleton-box"></div></td>
        <td class="px-6 py-4"><div class="h-5 w-24 skeleton-box"></div></td>
        <td class="px-6 py-4"><div class="h-5 w-16 skeleton-box"></div></td>
        <td class="px-6 py-4 text-right"><div class="inline-block h-8 w-24 skeleton-box"></div></td>
    </tr>
</template>
```

---

## 5. Spacing System (4px Geometric Grid)

All margins, paddings, and flexbox gaps conform strictly to a **4px geometric grid scale** to eliminate random spacing hacks.

*   `--sp-1`: 4px | `--sp-2`: 8px | `--sp-3`: 12px | `--sp-4`: 16px | `--sp-5`: 20px
*   `--sp-6`: 24px | `--sp-8`: 32px | `--sp-10`: 40px | `--sp-12`: 48px | `--sp-16`: 64px

Always leverage these shorthand utilities to separate structural markup:
*   **Paddings:** `.p-1` (4px), `.p-2` (8px), `.p-3` (12px), `.p-4` (16px), `.p-6` (24px)
*   **Margins (Bottom):** `.mb-1`, `.mb-2` (8px), `.mb-3` (12px), `.mb-4` (16px), `.mb-6` (24px)
*   **Flex Gaps:** `.gap-1` (4px), `.gap-2` (8px), `.gap-3` (12px), `.gap-4` (16px), `.gap-6` (24px)
