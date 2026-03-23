# Bulgarian Language Support

## Overview
The Social Services Directory plugin has been fully translated to Bulgarian language. All user-facing text, menus, buttons, labels, and messages are displayed in Bulgarian.

## Translation Implementation

### Translation System
The plugin uses a custom translation class (`SSD_Bulgarian_Translations`) that provides all Bulgarian strings through helper functions:
- `ssd__('key')` - Returns translated string
- `ssd_e('key')` - Echoes translated string

### Coverage

#### Frontend Text (Complete Bulgarian Translation)
- ✅ Main page title: "Намери доставчик на услуги"
- ✅ Search placeholder: "Въведете име, услуга или ключова дума..."
- ✅ Filter labels: "Филтрирай по", "Всички общини", "Всички услуги"
- ✅ View modes: "Мрежа", "Списък"
- ✅ Sort options: "Име (А-Я)", "Най-висок рейтинг"
- ✅ Buttons: "Търси", "Приложи филтри", "Изчисти филтри", "Виж детайли"
- ✅ Messages: "Зареждане...", "Не са намерени доставчици"
- ✅ Pagination: "Предишна", "Следваща", "Показване на ... от ... резултата"

#### Provider Card Text
- ✅ "Виж детайли" button
- ✅ "отзив" / "отзива" (review/reviews)
- ✅ Location icons and labels
- ✅ Service badges

#### Single Provider Page
- ✅ "Информация за доставчика"
- ✅ "Контактна информация"
- ✅ "Предлагани услуги"
- ✅ "Целеви групи"
- ✅ "Лицензна информация"
- ✅ "Адрес", "Община", "Населено място", "Телефон", "Имейл", "Уебсайт", "ЕИК"
- ✅ License fields: "Номер на лиценз", "Дата на издаване", "Дата на валидност"
- ✅ "Нарушения", "Няма установени нарушения"

#### Photo Gallery
- ✅ "Фото галерия"
- ✅ "снимки"
- ✅ Navigation: "Предишна", "Следваща", "Затвори"
- ✅ "Няма налични снимки"

#### Reviews Section
- ✅ "Отзиви от клиенти"
- ✅ "Среден рейтинг"
- ✅ "Напиши отзив"
- ✅ "Вашата оценка"
- ✅ "Вашият отзив"
- ✅ "Изпрати отзив"
- ✅ Review messages in Bulgarian

#### Admin Panel
- ✅ "Доставчици"
- ✅ "Добави нов доставчик"
- ✅ "Редактирай доставчик"
- ✅ "Импортирай данни"
- ✅ "Експортирай данни"
- ✅ "Настройки"
- ✅ "Статистика"

### Service Types (Bulgarian Names)
1. **Резидентна грижа** (Residential Care)
2. **Информиране и консултиране** (Information & Consulting)
3. **Терапия и рехабилитация** (Therapy & Rehabilitation)
4. **Обучение за придобиване на умения** (Skills Training)
5. **Застъпничество и посредничество** (Advocacy & Mediation)
6. **Социална рехабилитация и интеграция** (Social Rehabilitation)
7. **Правна защита и съдействие** (Legal Assistance)
8. **Кризисна интервенция** (Crisis Intervention)
9. **Подкрепа в общността** (Community Support)
10. **Временно настаняване** (Temporary Accommodation)

### Target Groups (Bulgarian Names)
1. **Деца** (Children)
2. **Възрастни хора** (Elderly)
3. **Хора с увреждания** (People with Disabilities)
4. **Лица с психични разстройства** (People with Mental Disorders)
5. **Лица с деменция** (People with Dementia)
6. **Деца в риск** (At-Risk Children)
7. **Бездомни лица** (Homeless)
8. **Жертви на домашно насилие** (Domestic Violence Victims)
9. **Жертви на трафик** (Trafficking Victims)
10. **Лица с проблеми със зависимости** (People with Addiction Issues)

## File Structure

```
includes/
  └── bulgarian-translations.php    # Main translation file with all Bulgarian strings

templates/
  ├── directory.php                 # Main directory template (Bulgarian)
  └── single-provider.php           # Single provider template (Bulgarian)
```

## Usage in Templates

### Basic Usage
```php
<?php
// Display translated text
ssd_e('find_service_providers');  // Outputs: Намери доставчик на услуги

// Get translated text as variable
$text = ssd__('search_placeholder');
?>
```

### In Forms and Labels
```php
<label><?php ssd_e('municipality'); ?></label>
<input type="text" placeholder="<?php ssd_e('search_placeholder'); ?>">
<button><?php ssd_e('search'); ?></button>
```

### Dynamic Text
```php
<?php
$count = 5;
echo $count . ' ' . ($count == 1 ? ssd__('review') : ssd__('reviews'));
// Outputs: 5 отзива
?>
```

## Date Formats
Bulgarian date formats are used throughout:
- Short date: `d.m.Y` (e.g., 24.02.2026)
- Date and time: `d.m.Y H:i` (e.g., 24.02.2026 14:30)

## Character Encoding
- All files use UTF-8 encoding
- Cyrillic characters (Bulgarian alphabet) are properly supported
- Database charset: `utf8mb4_unicode_ci`

## Testing Checklist

### Frontend
- [ ] Main directory page displays "Намери доставчик на услуги"
- [ ] Search bar shows Bulgarian placeholder text
- [ ] All filter dropdowns have Bulgarian labels
- [ ] Grid/List view buttons show "Мрежа" / "Списък"
- [ ] Sort dropdown has Bulgarian options
- [ ] Provider cards show "Виж детайли" button
- [ ] Pagination uses Bulgarian text
- [ ] No results message is in Bulgarian

### Single Provider Page
- [ ] All section headers are in Bulgarian
- [ ] Contact information labels are in Bulgarian
- [ ] License information is properly labeled
- [ ] Photo gallery text is in Bulgarian
- [ ] Reviews section is fully in Bulgarian

### Admin Panel
- [ ] Menu items are in Bulgarian
- [ ] Form labels are in Bulgarian
- [ ] Import tool messages are in Bulgarian
- [ ] Settings page is in Bulgarian

## Customization

### Adding New Translations
To add new translated strings, edit `includes/bulgarian-translations.php`:

```php
public static function get_strings() {
    return array(
        // ... existing translations ...
        
        // Add your new translation
        'your_key' => 'Вашият български текст',
    );
}
```

Then use in templates:
```php
<?php ssd_e('your_key'); ?>
```

### Modifying Existing Translations
Edit the corresponding key value in `includes/bulgarian-translations.php`:

```php
'view_details' => 'Вижте подробности',  // Changed from "Виж детайли"
```

## Compatibility
- ✅ WordPress 5.8+
- ✅ PHP 7.4+
- ✅ All modern browsers with UTF-8 support
- ✅ Mobile responsive
- ✅ Screen readers (Bulgarian text properly announced)

## Notes
- All WordPress admin notices and system messages remain in the WordPress installation language
- Third-party plugin integrations may have their own language settings
- Database field names remain in English for compatibility

## Support
For translation issues or suggestions, please check the translation strings in:
`includes/bulgarian-translations.php`

All user-facing text keys are documented with comments explaining their usage context.

---

**Version**: 1.0.0  
**Last Updated**: 2026-02-24  
**Language**: Bulgarian (Български език)
