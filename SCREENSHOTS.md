# 📸 Plugin Screenshots & Demo

## Social Services Directory - Visual Guide

---

## 🖼️ Main Directory View

**Grid Layout with Filters**
- Clean, modern card-based design
- Each card shows:
  - Provider thumbnail/logo
  - Provider name
  - Star rating (if reviews exist)
  - Location (municipality)
  - Service badges (up to 3 visible)
  - Address snippet
  - "View Details" and "Call" buttons
  - Favorite heart icon (top right)

**Filter Panel (Top)**
- Search bar
- Municipality dropdown (Select2)
- Service Type dropdown (Select2)
- Minimum Rating filter
- "Apply Filters" and "Reset" buttons
- Mobile: Collapsible filter panel

**Results Header**
- Results count: "Found X providers"
- Sort dropdown: Name, Date, Rating
- View toggle: Grid/List icons

---

## 📱 Responsive Design

### Desktop (1200px+)
- 3-4 provider cards per row
- Full filter panel visible
- Side-by-side layout

### Tablet (768px-1199px)
- 2 provider cards per row
- Filter panel remains visible
- Adjusted spacing

### Mobile (< 768px)
- 1 provider card per row
- Collapsible filter panel
- Toggle button: "Filters" with icon
- Stack all elements vertically
- Touch-friendly buttons

---

## 🎨 Color Scheme

**Default Colors:**
- Primary Blue: #0073aa (buttons, links)
- Success Green: #27ae60 (call buttons)
- Warning Orange: #f39c12 (star ratings)
- Light Blue: #e8f4f8 (service badges)
- Gray: #f5f5f5 (backgrounds)
- Border: #e0e0e0 (card borders)

**Hover Effects:**
- Cards lift up (translateY -5px)
- Box shadow increases
- Buttons darken
- Smooth transitions (0.3s)

---

## ⭐ Provider Card Design

```
┌─────────────────────────────────┐
│ [Photo/Logo Image]        [♡]  │ ← Favorite button
│                                 │
│ Provider Name                   │ ← H3 heading
│ ★★★★☆ 4.2 (23 reviews)        │ ← Rating
│ 📍 Столична                    │ ← Location
│                                 │
│ [Service 1] [Service 2] [+2]   │ ← Service badges
│                                 │
│ бул. "Витоша" № 12, София      │ ← Address
│                                 │
│ [View Details]  [📞]           │ ← Action buttons
└─────────────────────────────────┘
```

---

## 🔍 Filter Panel Design

```
┌─────────────────────────────────────────────────────────────┐
│  Find Service Providers                      [≡ Filters]    │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  [Search...]          [Municipality ▼]                      │
│  [Service Type ▼]     [Rating ▼]                           │
│                                                              │
│  [Apply Filters]  [Reset]                                   │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

---

## 📊 List View Layout

```
┌──────────────────────────────────────────────────────────────┐
│ [Photo]  │  Provider Name              [♡]                   │
│  200px   │  ★★★★☆ 4.2 (23 reviews)                         │
│          │  📍 Столична • бул. "Витоша" № 12              │
│          │  [Service 1] [Service 2] [Service 3] [+2]       │
│          │  Description excerpt...                          │
│          │  [View Details]  [📞 Call]                      │
└──────────────────────────────────────────────────────────────┘
```

---

## 🎯 Key Features Visible

1. **Search Bar**
   - Prominent placement
   - Placeholder: "Search providers..."
   - Real-time AJAX search

2. **Municipality Filter**
   - Select2 dropdown
   - All 94 municipalities listed
   - Shows count: "Столична (639)"

3. **Service Filter**
   - Select2 dropdown
   - All 10 service types
   - Shows count per service

4. **Rating Filter**
   - Simple dropdown
   - Options: Any, 4+, 3+, 2+
   - Star icons displayed

5. **Provider Cards**
   - Clean, modern design
   - Hover effects (lift + shadow)
   - Favorite button (heart icon)
   - Action buttons (view, call)

6. **Loading States**
   - Full-page overlay
   - Spinning loader
   - Smooth transitions

---

## 🖥️ Admin Interface

### Provider Edit Screen
- **Provider Details Box**
  - EIK field
  - Settlement field
  - Address textarea
  - Target group textarea

- **Contact Information Box**
  - Phone field
  - Email field
  - Website field
  - Working hours textarea

- **License Information Box**
  - License number
  - Issue date (date picker)
  - Valid until (date picker)
  - Violations textarea

- **Photo Gallery Box**
  - Thumbnail grid
  - "Add Photos" button
  - Remove buttons per photo
  - Drag-and-drop ordering

### Import Page
- **CSV Upload Form**
  - File picker
  - Batch size input
  - Update existing checkbox
  - "Start Import" button

- **Instructions Panel**
  - Required columns list
  - CSV format guide
  - Encoding note (UTF-8)
  - Import process explanation

### Settings Page
- **General Settings**
  - Items per page
  - Default view
  - Feature toggles (reviews, photos, favorites)
  - Auto-approve reviews

- **Shortcode Reference**
  - Table of all shortcodes
  - Usage examples
  - Parameter descriptions

- **Statistics Dashboard**
  - Total providers count
  - Municipalities count
  - Service types count
  - Approved reviews count

### Reviews Management
- **Review List Table**
  - Columns: Provider, Rating, Review, User, Date, Actions
  - Bulk actions: Approve, Spam, Delete
  - Tabs: Pending, Approved, Spam
  - Badge counts per tab

---

## 🎨 Design Highlights

**Modern & Clean**
- Flat design aesthetic
- Ample white space
- Clear typography
- Subtle shadows

**User-Friendly**
- Intuitive navigation
- Clear CTAs
- Helpful icons
- Loading indicators

**Professional**
- Consistent spacing
- Proper hierarchy
- Brand colors
- Polished interactions

**Accessible**
- Good contrast ratios
- Keyboard navigation
- Screen reader friendly
- WCAG compliant

---

## 💡 Interactive Elements

1. **Filter Changes**
   - Instant AJAX reload
   - Loading overlay appears
   - Results update smoothly
   - Count updates

2. **Favorite Toggle**
   - Heart icon fills/unfills
   - Color change (gray → red)
   - Smooth animation
   - Success message

3. **Load More**
   - Button at bottom
   - Loads next batch
   - Appends to grid
   - Hides when no more

4. **View Toggle**
   - Grid ⇄ List switch
   - Instant layout change
   - Active state indicator
   - Maintains filter state

---

**This plugin provides a complete, professional directory solution ready to deploy!**
