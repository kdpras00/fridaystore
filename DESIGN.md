---
name: POS Toko Design System (Shadcn UI Aesthetic)
description: Clean professional dashboard with pure white panels, stark off-black actions, and clean gray borders.
colors:
  bg: "oklch(1 0 0)"
  surface: "oklch(1 0 0)"
  surface-2: "oklch(0.97 0 0)"
  surface-3: "oklch(0.922 0 0)"
  border: "oklch(0.922 0 0)"
  border-subtle: "oklch(0.95 0 0)"
  ink: "oklch(0.145 0 0)"
  ink-muted: "oklch(0.556 0 0)"
  ink-subtle: "oklch(0.708 0 0)"
  amber: "oklch(0.205 0 0)"
  amber-dim: "oklch(0.30 0 0)"
  success: "oklch(0.58 0.11 145)"
  danger: "oklch(0.577 0.245 27.325)"
typography:
  display:
    fontFamily: "Inter, system-ui, sans-serif"
    fontSize: "18px"
    fontWeight: 600
    lineHeight: 1.25
    letterSpacing: "-0.02em"
  body:
    fontFamily: "Inter, system-ui, sans-serif"
    fontSize: "15px"
    fontWeight: 400
    lineHeight: 1.5
  mono:
    fontFamily: "JetBrains Mono, monospace"
    fontSize: "13.5px"
    fontWeight: 400
rounded:
  sm: "6px"
  md: "8px"
  lg: "10px"
spacing:
  xs: "4px"
  sm: "8px"
  md: "12px"
  lg: "16px"
  xl: "20px"
components:
  button-primary:
    backgroundColor: "{colors.amber}"
    textColor: "oklch(0.985 0 0)"
    rounded: "{rounded.sm}"
    padding: "7px 14px"
  button-primary-hover:
    backgroundColor: "{colors.amber-dim}"
  button-ghost:
    backgroundColor: "transparent"
    textColor: "{colors.ink-muted}"
    rounded: "{rounded.sm}"
    padding: "7px 14px"
---

# Design System: Shadcn UI Aesthetic

## 1. Overview

**Creative North Star: "Clean Component-Based Simplicity"**

Inspired by Shadcn UI, this design system combines pure white backgrounds, high-contrast typography, standard thin gray borders, and a stark off-black primary accent color. The goal is to provide a clean, uncluttered interface that feels like a modern SaaS dashboard.

**Key Characteristics:**
- Pure white background screens and surface components.
- Consistent standard corner radius (`8px` on cards/inputs, `6px` on buttons/badges).
- Stark off-black primary actions (`#18181b`).
- Soft gray border frames (`#e4e4e7`).
- Clean, standard typography using the `Inter` typeface.

## 2. Colors

A high-contrast, professional palette.

### Primary Accent
- **Stark Charcoal/Black** (`oklch(0.205 0 0)`): Primary actions, active navigation states, and confirm buttons.

### Neutral
- **Pure White** (`oklch(1 0 0)`): Base screen background and panel surfaces.
- **Secondary Gray** (`oklch(0.97 0 0)`): Muted areas, sidebar items, and table headers.
- **Off-black** (`oklch(0.145 0 0)`): Default high-contrast ink for body text.
- **Muted Foreground** (`oklch(0.556 0 0)`): Helper text, secondary labels.

## 3. Typography

**Display/Body Font:** Inter (with system-ui fallback)
**Label/Mono Font:** JetBrains Mono (for prices, currency, inventory numbers)

### Hierarchy
- **Display** (600, `18px`, `1.25`): Page titles and card headers.
- **Body** (400, `15px`, `1.5`): General body text and form labels.
- **Mono** (400, `13.5px`): Prices, totals, barcode keys, and quantities.

## 4. Elevation

Subtle, crisp shadows.

### Shadow Vocabulary
- **Micro-shadow** (Cards/Buttons): `0 1px 2px 0 oklch(0 0 0 / 0.05)`
- **Overlay shadow** (Modals/Popups): `0 10px 15px -3px oklch(0 0 0 / 0.1), 0 4px 6px -4px oklch(0 0 0 / 0.1)`

## 5. Components

### Buttons
- **Shape:** Soft Rectangle (`6px` to `8px` radius)
- **Primary:** Stark off-black background, white text.
- **Hover:** Dark gray (`oklch(0.30 0 0)`).

### Containers (Cards)
- **Corner Style:** Rounded (`8px` radius)
- **Border:** 1px gray border (`oklch(0.922 0 0)`).
- **Elevation:** Subtle shadow (`0 1px 2px 0 rgba(0,0,0,0.05)`).

### Inputs / Fields
- **Style:** Background Pure White, 1px gray border, rounded `8px`.
- **Focus:** 1px off-black border and ring outline.
