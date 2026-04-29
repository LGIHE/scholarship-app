# Logo Placement Visual Guide

## Logo Files
- **logo.png** (29KB) - Standard logo for light backgrounds
- **logo-white.png** (62KB) - White variant for dark backgrounds

---

## 🌐 Public Website

### Header (All Public Pages)
```
┌─────────────────────────────────────────────────────────┐
│  [LOGO]    About  Resources  FAQ  Contact  [Apply Now]  │
│  48px                                                    │
└─────────────────────────────────────────────────────────┘
```
- **File**: `logo.png`
- **Size**: 48px height
- **Component**: `PublicHeader.jsx`
- **Pages**: Welcome, About, Resources, FAQ, Contact

### Footer (All Public Pages)
```
┌─────────────────────────────────────────────────────────┐
│  ████████████████████████████████████████████████████   │
│  █ [WHITE LOGO]                                     █   │
│  █ 40px                                             █   │
│  █ Empowering future educators...                  █   │
│  ████████████████████████████████████████████████████   │
└─────────────────────────────────────────────────────────┘
```
- **File**: `logo-white.png`
- **Size**: 40px height
- **Component**: `PublicFooter.jsx`
- **Background**: Dark gray (#1F2937)

---

## 🔐 Authentication Pages

### Login/Register Pages
```
┌─────────────────────────────────────────────────────────┐
│                                                          │
│                       [LOGO]                             │
│                        80px                              │
│                                                          │
│              ┌──────────────────────┐                   │
│              │  Login Form          │                   │
│              │  ________________    │                   │
│              │  ________________    │                   │
│              │  [Login Button]      │                   │
│              └──────────────────────┘                   │
└─────────────────────────────────────────────────────────┘
```
- **File**: `logo.png`
- **Size**: 80px height
- **Layout**: `GuestLayout.jsx`
- **Pages**: Login, Register, Password Reset, Email Verification

---

## 👤 Authenticated User Area

### Navigation Bar
```
┌─────────────────────────────────────────────────────────┐
│  [LOGO]  Dashboard  My Application    [User Menu ▼]    │
│  40px                                                    │
└─────────────────────────────────────────────────────────┘
```
- **File**: `logo.png`
- **Size**: 40px height
- **Layout**: `AuthenticatedLayout.jsx`
- **Pages**: Dashboard, Application Form, Profile

---

## 🛡️ Admin Panel (Filament)

### Sidebar
```
┌──────────────┬──────────────────────────────────────────┐
│              │                                          │
│   [LOGO]     │  Dashboard Content                       │
│    48px      │                                          │
│              │                                          │
│ Dashboard    │                                          │
│ Applications │                                          │
│ Scholars     │                                          │
│ Users        │                                          │
│ Roles        │                                          │
│              │                                          │
└──────────────┴──────────────────────────────────────────┘
```
- **File**: `logo.png`
- **Size**: 48px (3rem)
- **Config**: `AdminPanelProvider.php`
- **Location**: Admin sidebar, Admin login page

### Browser Tab
```
[🖼️ LOGO] LGF Scholarship Admin
```
- **File**: `logo.png` (as favicon)
- **Config**: `AdminPanelProvider.php`

---

## 📧 Email Templates

### Email Header
```
┌─────────────────────────────────────────────────────────┐
│                      [LOGO]                              │
│                   max 60px height                        │
│                   max 200px width                        │
├─────────────────────────────────────────────────────────┤
│                                                          │
│  Dear Applicant,                                         │
│                                                          │
│  Email content here...                                   │
│                                                          │
│  [Button]                                                │
│                                                          │
└─────────────────────────────────────────────────────────┘
```
- **File**: `logo.png`
- **Size**: Max 60px height, 200px width
- **Template**: `mail/html/header.blade.php`
- **Emails**: Application Received, Application Approved, All system emails

---

## 📐 Size Reference

| Location | Logo File | Height | Width | Notes |
|----------|-----------|--------|-------|-------|
| Public Header | logo.png | 48px | auto | Clickable to home |
| Public Footer | logo-white.png | 40px | auto | Dark background |
| Guest Layout | logo.png | 80px | auto | Above login form |
| Auth Navigation | logo.png | 40px | auto | Top left corner |
| Admin Panel | logo.png | 48px | auto | Sidebar & login |
| Favicon | logo.png | 16-32px | auto | Browser tab |
| Email Header | logo.png | max 60px | max 200px | Centered |

---

## 🎨 Design Consistency

### Color Contexts
- **Light backgrounds**: Use `logo.png`
- **Dark backgrounds**: Use `logo-white.png`
- **Email**: Use `logo.png` (most email clients have light backgrounds)

### Responsive Behavior
All logos use:
- Fixed height with `h-{size}` classes
- Auto width with `w-auto` class
- Maintains aspect ratio automatically

### Accessibility
All logo images include:
- Descriptive `alt` text
- Proper semantic HTML
- Clickable links to homepage (where appropriate)

---

## 🔄 Component Reusability

The `ApplicationLogo` component supports both variants:

```jsx
// Regular logo (default)
<ApplicationLogo className="h-12 w-auto" />

// White logo for dark backgrounds
<ApplicationLogo white={true} className="h-12 w-auto" />
```

This makes it easy to add logos to new pages or components while maintaining consistency.
