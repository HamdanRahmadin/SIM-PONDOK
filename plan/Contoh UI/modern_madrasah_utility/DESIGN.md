---
name: Modern Madrasah Utility
colors:
  surface: '#f5fbf3'
  surface-dim: '#d6dcd4'
  surface-bright: '#f5fbf3'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#eff5ed'
  surface-container: '#eaf0e8'
  surface-container-high: '#e4eae2'
  surface-container-highest: '#dee4dc'
  on-surface: '#171d18'
  on-surface-variant: '#3e4a40'
  inverse-surface: '#2c322d'
  inverse-on-surface: '#ecf2ea'
  outline: '#6e7a70'
  outline-variant: '#bdcabe'
  surface-tint: '#006d3f'
  primary: '#006a3d'
  on-primary: '#ffffff'
  primary-container: '#00864e'
  on-primary-container: '#f6fff5'
  inverse-primary: '#6cdc99'
  secondary: '#606216'
  on-secondary: '#ffffff'
  secondary-container: '#e7e88d'
  on-secondary-container: '#67681c'
  tertiary: '#9e3a43'
  on-tertiary: '#ffffff'
  tertiary-container: '#be515a'
  on-tertiary-container: '#fffbff'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#89f9b4'
  primary-fixed-dim: '#6cdc99'
  on-primary-fixed: '#00210f'
  on-primary-fixed-variant: '#00522e'
  secondary-fixed: '#e7e88d'
  secondary-fixed-dim: '#cacb74'
  on-secondary-fixed: '#1c1d00'
  on-secondary-fixed-variant: '#484a00'
  tertiary-fixed: '#ffdada'
  tertiary-fixed-dim: '#ffb3b5'
  on-tertiary-fixed: '#40000b'
  on-tertiary-fixed-variant: '#82242f'
  background: '#f5fbf3'
  on-background: '#171d18'
  surface-variant: '#dee4dc'
typography:
  display-lg:
    fontFamily: Inter
    fontSize: 32px
    fontWeight: '700'
    lineHeight: '1.2'
    letterSpacing: -0.02em
  headline-md:
    fontFamily: Inter
    fontSize: 24px
    fontWeight: '600'
    lineHeight: '1.3'
    letterSpacing: -0.01em
  headline-sm:
    fontFamily: Inter
    fontSize: 20px
    fontWeight: '600'
    lineHeight: '1.4'
  body-lg:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '400'
    lineHeight: '1.6'
  body-md:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '400'
    lineHeight: '1.5'
  label-md:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '600'
    lineHeight: '1'
    letterSpacing: 0.02em
  headline-md-mobile:
    fontFamily: Inter
    fontSize: 20px
    fontWeight: '600'
    lineHeight: '1.3'
  body-lg-mobile:
    fontFamily: Inter
    fontSize: 15px
    fontWeight: '400'
    lineHeight: '1.5'
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  base: 4px
  margin-mobile: 16px
  margin-desktop: 24px
  gutter: 12px
  touch-target-min: 44px
  stack-sm: 8px
  stack-md: 16px
  stack-lg: 24px
---

## Brand & Style

The design system is anchored in the principles of **Academic Discipline** and **Spiritual Warmth**. It aims to transform the administrative rigor of a boarding school into a seamless, high-performance experience. By blending the efficiency of modern SaaS (like Linear) with the accessibility of premium EdTech, the UI fosters a sense of calm authority.

The aesthetic follows a **Modern SaaS** movement: high-utility, minimalist, and clutter-free. It prioritizes clarity over decoration, ensuring that teachers can perform high-frequency tasks—like taking attendance for fifty students—with zero cognitive load. The emotional response is one of reliability and "Sakinah" (tranquility through order), using generous whitespace and a refined green-based palette to signal growth and success.

## Colors

The color strategy centers on **Emerald Growth** and **Olive Harmony**. 

- **Primary Green (#149459)** is the "Action" color, used for primary buttons, active states, and "Hadir" (Present) status indicators.
- **Secondary Green-Yellow (#CCCD76)** acts as a soft highlight color, used for accents, progress bars, and subtle differentiators in data visualization.
- **Background and Surface** levels are strictly separated to create a clear "layering" effect. The background is a cool, slightly desaturated mint-grey to reduce eye strain during long usage sessions.
- **Semantic Colors** are used traditionally: Amber for "Izin" (Excused) and Red for "Alfa" (Absent/Unexcused).

## Typography

This design system utilizes **Inter** exclusively to leverage its exceptional legibility and neutral, systematic character. The type scale is optimized for "at-a-glance" scanning.

- **Headlines** use tighter letter spacing and semi-bold weights to create a strong visual anchor for page titles (e.g., Student Names or Class Sections).
- **Body Text** is set with generous line-height to ensure readability in list-heavy views.
- **Labels** are used for metadata like "Student ID" or "Parent Contact," often utilizing uppercase styles to distinguish them from actionable data.
- On mobile devices, the type scale shifts slightly smaller to maximize the density of information without sacrificing touch targets.

## Layout & Spacing

This is a **Mobile-First** layout model designed for one-handed operation by educators moving between classrooms. 

1. **Grid**: A fluid 4-column grid for mobile, scaling to 12 columns for tablet/desktop dashboards. 
2. **Safe Zones**: Every screen maintains a 16px side margin. 
3. **Touch-First Hierarchy**: Actionable elements are placed in the "Thumb Zone" (the bottom 60% of the screen). 
4. **Spacing Rhythm**: A strict 4px/8px baseline grid is used. Elements within a student card use `stack-sm` (8px), while the gap between separate student cards uses `stack-md` (16px).
5. **Sticky Elements**: Headers remain fixed to provide context (Class Name), while primary actions (Submit Attendance) are housed in a fixed bottom bar.

## Elevation & Depth

This design system uses **Tonal Layering** combined with **Ambient Shadows** to create a sophisticated, depth-aware UI.

- **Level 0 (Base)**: The #F8FAF9 background.
- **Level 1 (Cards)**: White surfaces (#FFFFFF) with a very soft, diffused shadow (0px 2px 8px, 4% opacity black). This is where the primary content lives.
- **Level 2 (Overlays/Modals)**: Surfaces that sit above the UI, using a more pronounced shadow (0px 10px 25px, 8% opacity) to suggest physical height and focus.
- **Glassmorphism**: Sticky headers use a subtle backdrop-blur (12px) with a semi-transparent white tint (90% opacity) to maintain a sense of space while scrolling.

## Shapes

The shape language is **Soft-Geometric**. A standard corner radius of **12px (0.75rem)** is applied to all primary containers, cards, and buttons.

- **Cards**: 12px radius ensures a modern, friendly feel.
- **Segmented Controls**: The outer container uses 12px, while the inner active "pill" uses a nested radius of 8px to maintain visual harmony.
- **Input Fields**: 12px radius to match buttons, creating a unified form-factor across the interface.
- **Avatar/Profile**: Circular (999px) to distinguish human elements from functional UI blocks.

## Components

### Buttons & Inputs
- **Primary Button**: 48px height for mobile. Solid #149459 background, white text, 12px radius.
- **Segmented Control**: A toggle for "Hadir, Izin, Alfa." Hadir uses a green tint when active, Izin uses amber, and Alfa uses red. The inactive state is a neutral light grey.
- **Input Fields**: Subtle 1px border (#E5E7EB) that thickens and changes to #149459 on focus.

### Cards
- **Student Card**: Features a small avatar, student name in `headline-sm`, and a status indicator. Use a soft vertical gradient (White to #FDFDFD) to give it a "premium" tactile feel.

### Navigation
- **Navigation Drawer**: A clean, slide-out hamburger menu from the left. Icons are line-weight (2px) to match the Inter font.
- **Bottom Action Bar**: A floating or fixed bar at the bottom containing the primary "Save/Submit" action, ensuring it is always reachable.

### Analytics & Feedback
- **Timeline**: A vertical line with "status dots" (Primary Green) to show student attendance history.
- **Charts**: Use #149459 for "Good" trends and #CCCD76 for "Neutral/Average" trends to maintain brand consistency.