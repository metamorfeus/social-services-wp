# Social Services Directory - WordPress Plugin

**Version:** 1.0.0  
**Requires:** WordPress 5.8+  
**Requires PHP:** 7.4+  
**License:** GPL v2 or later

## 📋 Description

A comprehensive WordPress plugin for displaying social service providers in a searchable, filterable directory with advanced features including:

- ✅ **Advanced Filtering** - Location, service type, ratings, keywords
- ✅ **Multiple View Modes** - Grid and list layouts
- ✅ **User Reviews & Ratings** - 5-star rating system with detailed reviews
- ✅ **Photo Galleries** - Multiple photos per provider
- ✅ **Favorites System** - Save favorite providers (logged-in users)
- ✅ **Responsive Design** - Mobile-friendly interface
- ✅ **CSV Import** - Easy bulk import from your MySQL data
- ✅ **Custom Taxonomies** - Municipality, service types, target groups
- ✅ **AJAX Filtering** - Real-time search without page reloads
- ✅ **SEO Friendly** - Clean URLs and structured data

## 🚀 Features

### For Users
- **Search & Filter** - Find providers by location, services, rating
- **Grid/List View** - Choose your preferred layout
- **Reviews** - Read and submit reviews
- **Photo Gallery** - View provider photos
- **Save Favorites** - Bookmark providers for later
- **Mobile Responsive** - Works perfectly on all devices

### For Administrators
- **Easy Import** - Import providers from CSV
- **Review Management** - Approve, reject, or mark reviews as spam
- **Custom Fields** - EIK, license info, contact details
- **Settings Panel** - Configure plugin behavior
- **Statistics** - View provider and review counts
- **Shortcodes** - Display directory anywhere

## 📦 Installation

### Method 1: Upload via WordPress Admin

1. Download the plugin zip file
2. Go to **Plugins > Add New** in WordPress admin
3. Click **Upload Plugin**
4. Choose the zip file and click **Install Now**
5. Activate the plugin

### Method 2: Manual Installation

1. Extract the plugin folder to `/wp-content/plugins/`
2. Activate through the **Plugins** menu in WordPress
3. Go to **Providers > Settings** to configure

## ⚙️ Setup

### 1. Initial Configuration

After activation, go to **Providers > Settings** to configure:

- **Items Per Page** - Number of providers to display
- **Default View** - Grid or List
- **Enable Reviews** - Allow users to submit reviews
- **Enable Photos** - Allow photo galleries
- **Enable Favorites** - Allow users to save favorites
- **Auto-approve Reviews** - Skip review moderation

### 2. Import Your Data

Go to **Providers > Import Data** to import your social services data:

1. Upload the `cleaned_social_services_data.csv` file
2. Set batch size (50 recommended for most servers)
3. Choose whether to update existing providers
4. Click "Start Import"

The import will:
- Create service provider posts
- Generate municipality and service type taxonomies
- Save all metadata (EIK, licenses, contact info)
- Associate providers with correct categories

### 3. Create a Directory Page

1. Create a new page in WordPress
2. Add the shortcode: `[ssd_directory]`
3. Publish the page
4. The directory will display with all filters

## 📝 Shortcodes

### Display Full Directory
```
[ssd_directory]
```

### Display with List View
```
[ssd_directory view="list"]
```

### Filter by Municipality
```
[ssd_directory municipality="sofia"]
```

### Filter by Service
```
[ssd_directory service="residential-care"]
```

### Display Specific Provider
```
[ssd_provider id="123"]
```

### Combined Filters
```
[ssd_directory view="list" municipality="varna" per_page="20"]
```

## 🎨 Customization

### Custom CSS

Add custom styles to your theme's `style.css`:

```css
/* Change primary color */
.ssd-apply-filters,
.ssd-view-details {
    background-color: #your-color !important;
}

/* Customize card appearance */
.ssd-provider-card {
    border-radius: 12px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
}
```

### Template Overrides

Copy templates from plugin to your theme:

```
/wp-content/plugins/social-services-directory/templates/directory.php
→ /wp-content/themes/your-theme/social-services-directory/directory.php
```

## 🔧 Technical Details

### Database Tables

The plugin creates 4 custom tables:
- `wp_ssd_reviews` - User reviews
- `wp_ssd_photos` - Photo gallery
- `wp_ssd_provider_meta` - Extended metadata
- `wp_ssd_favorites` - User favorites

### Custom Post Types

- `ssd_provider` - Service Provider

### Taxonomies

- `ssd_municipality` - Municipalities
- `ssd_service_type` - Service Types
- `ssd_target_group` - Target Groups

### AJAX Actions

- `ssd_filter_providers` - Filter directory
- `ssd_toggle_favorite` - Add/remove favorites
- `ssd_submit_review` - Submit review
- `ssd_load_more` - Load more providers

## 🌐 Multilanguage Support

The plugin is translation-ready. Create translations in:

```
/wp-content/plugins/social-services-directory/languages/
```

Text domain: `social-services-directory`

### Bulgarian Translation Example

Create `social-services-directory-bg_BG.po` and `.mo` files for Bulgarian language support.

## 📊 CSV Import Format

Required columns in CSV file:

| Column | Description | Required |
|--------|-------------|----------|
| `provider_name` | Provider name | Yes |
| `eik` | Unified ID Code | No |
| `municipality` | Municipality name | No |
| `settlement` | City/town | No |
| `address` | Full address | No |
| `social_service` | Service type | No |
| `target_group` | Target group | No |
| `phone` | Contact phone | No |
| `email` | Contact email | No |
| `license_number` | License number | No |
| `license_validity_date` | Valid until (YYYY-MM-DD) | No |

**Important:** Ensure CSV is UTF-8 encoded for Bulgarian Cyrillic text.

## 🛠️ Troubleshooting

### Import Issues

**Problem:** CSV import fails  
**Solution:** Check CSV encoding is UTF-8, reduce batch size

**Problem:** Memory limit errors  
**Solution:** Increase PHP memory limit in `wp-config.php`:
```php
define('WP_MEMORY_LIMIT', '512M');
```

### Display Issues

**Problem:** Filters not working  
**Solution:** Clear cache, check jQuery is loaded

**Problem:** Bulgarian text shows as ???  
**Solution:** Ensure database charset is utf8mb4

### Performance

**Problem:** Slow loading with many providers  
**Solution:** Enable object caching, reduce items per page

## 🔒 Security

- All AJAX requests use nonces
- User input is sanitized and escaped
- Database queries use prepared statements
- File uploads validated
- Reviews moderated by default

## 📈 Performance Optimization

1. **Enable WordPress caching** - Use a caching plugin
2. **Optimize images** - Compress provider thumbnails
3. **Use CDN** - Serve assets from CDN
4. **Database indexes** - Already included
5. **Lazy loading** - Images load on scroll

## 🤝 Support

For support, bug reports, or feature requests:

1. Check documentation
2. Review FAQ section
3. Contact plugin developer
4. Visit support forum

## 📄 Changelog

### Version 1.0.0
- Initial release
- Directory listing with filters
- Review system
- Photo galleries
- Favorites system
- CSV import
- Admin settings
- Responsive design

## 📜 License

This plugin is licensed under GPL v2 or later.

```
Copyright (C) 2026

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
```

## 🎯 Roadmap

Future features planned:
- [ ] Google Maps integration
- [ ] Advanced search filters
- [ ] Export functionality
- [ ] Email notifications
- [ ] Social sharing
- [ ] Analytics dashboard
- [ ] Mobile app API

## 👨‍💻 Developer Info

### Hooks & Filters

**Actions:**
```php
do_action('ssd_before_provider_card', $provider_id);
do_action('ssd_after_provider_card', $provider_id);
do_action('ssd_provider_imported', $provider_id, $data);
```

**Filters:**
```php
apply_filters('ssd_provider_card_html', $html, $provider_id);
apply_filters('ssd_query_args', $args);
apply_filters('ssd_review_approved', $approved, $review_data);
```

### Custom Functions

```php
// Get providers
$providers = SSD_Frontend::get_providers(array(
    'municipality' => 'sofia',
    'service' => 'residential-care',
    'min_rating' => 4
));

// Get provider rating
$rating = SSD_Database::get_provider_rating($provider_id);

// Check if favorited
$is_fav = SSD_Database::is_favorited($user_id, $provider_id);
```

## 🙏 Credits

Developed for Bulgarian Social Services Registry  
Built with WordPress best practices  
Uses Select2 for enhanced dropdowns

---

**Made with ❤️ for Social Services**
