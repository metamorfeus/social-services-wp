# 🚀 Quick Installation Guide

## Social Services Directory WordPress Plugin

---

## ⚡ 5-Minute Setup

### Step 1: Install Plugin (2 minutes)

> ⚠️ **Updating from a previous version?** Remove the old plugin completely before installing the new one:
> 1. Go to **WordPress admin > Plugins**
> 2. Click **Deactivate** under "Social Services Directory"
> 3. Click **Delete** and confirm — this removes all plugin files
> 4. Then proceed with the installation below
>
> *Your imported provider data, reviews, photos and settings are stored in the database and are NOT affected by deleting the plugin files.*

1. **Download the plugin**
   - Use the `social-services-directory.zip` file provided

2. **Upload to WordPress**
   - Go to **Plugins > Add New > Upload Plugin**
   - Choose `social-services-directory.zip` and click **Install Now**

3. **Activate**
   - Click **Activate Plugin** after installation completes

### Step 2: Import Your Data (2 minutes)

1. **Prepare CSV File**
   - Use the `cleaned_social_services_data.csv` from the MySQL package
   - Ensure it's UTF-8 encoded

2. **Import**
   - Go to **Providers > Import Data**
   - Upload the CSV file
   - Set batch size to 50
   - Click "Start Import"
   - Wait for completion (may take a few minutes for 1,782 records)

### Step 3: Create Directory Page (1 minute)

1. **Create New Page**
   - Pages > Add New
   - Title: "Service Providers" (or any name)

2. **Add Shortcode**
   - In the content editor, add: `[ssd_directory]`
   - Publish the page

3. **View Your Directory**
   - Visit the page to see the directory with filters!

---

## 📋 What You Get

✅ **1,782 Service Providers** - From Bulgarian Social Services Registry  
✅ **94 Municipalities** - Automatically categorized  
✅ **10 Service Types** - Pre-configured taxonomies  
✅ **Advanced Filters** - Location, service, rating, search  
✅ **Review System** - User ratings and reviews  
✅ **Photo Galleries** - Multiple photos per provider  
✅ **Responsive Design** - Mobile-friendly interface  

---

## 🎨 Customization

### Change Colors

Add to your theme's CSS:

```css
/* Primary color */
.ssd-apply-filters,
.ssd-view-details {
    background-color: #your-color !important;
}

/* Service badges */
.ssd-service-badge {
    background-color: #your-light-color !important;
    color: #your-dark-color !important;
}
```

### Adjust Settings

Go to **Providers > Settings** to configure:
- Items per page
- Default view (grid/list)
- Enable/disable reviews
- Enable/disable photos
- Auto-approve reviews

---

## 📝 Available Shortcodes

### Basic Directory
```
[ssd_directory]
```

### With Filters
```
[ssd_directory view="list" municipality="sofia"]
```

### Single Provider
```
[ssd_provider id="123"]
```

---

## 🔧 Troubleshooting

### CSV Import Fails
- **Check encoding**: Ensure UTF-8
- **Reduce batch size**: Try 25 instead of 50
- **Increase memory**: Add to wp-config.php:
  ```php
  define('WP_MEMORY_LIMIT', '512M');
  ```

### Bulgarian Text Shows as ???
- **Database charset**: Should be utf8mb4
- **CSV encoding**: Must be UTF-8
- **Check file**: Open in Notepad++ to verify encoding

### Filters Not Working
- **Clear cache**: Clear WordPress and browser cache
- **Check jQuery**: Ensure jQuery is loaded
- **Disable plugins**: Test with other plugins disabled

---

## 📊 Expected Results

After import, you should have:

- **1,782 Providers** - All service providers
- **94 Municipalities** - From Столична to Велико Търново
- **10 Service Types** - Резидентна грижа, Информиране и консултиране, etc.
- **Complete Data** - EIK, addresses, licenses, contact info

---

## 🎯 Next Steps

1. **Add Photos** - Edit providers to add photo galleries
2. **Customize Design** - Match your theme's style
3. **Configure Reviews** - Set auto-approve or moderation
4. **Add More Providers** - Manual or CSV import
5. **Promote** - Share the directory with users!

---

## 📞 Need Help?

**Common Issues:**
- Import stuck: Increase PHP timeout
- Memory errors: Increase PHP memory limit
- Blank page: Check error logs

**Resources:**
- README.md - Full documentation
- WordPress Codex - General WP help
- Plugin settings - Configuration options

---

## ✨ You're Ready!

Your Social Services Directory is now live and fully functional. Users can:
- 🔍 Search and filter providers
- ⭐ Read and submit reviews
- 📷 View photo galleries
- ❤️ Save favorite providers
- 📱 Browse on mobile

**Enjoy your new directory!** 🎉
