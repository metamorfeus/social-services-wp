<?php
/**
 * Bulgarian Translation Strings
 * This file contains all Bulgarian translations used throughout the plugin
 */

if (!defined('ABSPATH')) {
    exit;
}

class SSD_Bulgarian_Translations {
    
    /**
     * Get all translation strings
     */
    public static function get_strings() {
        return array(
            // Frontend - Main Directory
            'find_service_providers' => 'Намери доставчик на услуги',
            'search_providers' => 'Търси доставчици',
            'search_placeholder' => 'Въведете име, услуга или ключова дума...',
            'filter_by' => 'Филтрирай по',
            'all_municipalities' => 'Всички общини',
            'all_services' => 'Всички услуги',
            'all_target_groups' => 'Всички целеви групи',
            'minimum_rating' => 'Минимален рейтинг',
            'any_rating' => 'Всякакъв рейтинг',
            'view_mode' => 'Изглед',
            'grid_view' => 'Мрежа',
            'list_view' => 'Списък',
            'sort_by' => 'Подреди по',
            'name_asc' => 'Име (А-Я)',
            'name_desc' => 'Име (Я-А)',
            'rating_high' => 'Най-висок рейтинг',
            'rating_low' => 'Най-нисък рейтинг',
            'newest' => 'Най-нови',
            'search' => 'Търси',
            'reset_filters' => 'Изчисти филтри',
            'apply_filters' => 'Приложи филтри',
            
            // Provider Card
            'view_details' => 'Виж детайли',
            'call' => 'Обади се',
            'add_to_favorites' => 'Добави в любими',
            'remove_from_favorites' => 'Премахни от любими',
            'review' => 'отзив',
            'reviews' => 'отзива',
            'reviews_plural' => 'отзива',
            'no_reviews_yet' => 'Все още няма отзиви',
            'be_first_review' => 'Бъдете първи, който ще остави отзив',
            
            // Single Provider Page
            'provider_information' => 'Информация за доставчика',
            'contact_information' => 'Контактна информация',
            'services_offered' => 'Предлагани услуги',
            'target_groups' => 'Целеви групи',
            'license_information' => 'Лицензна информация',
            'location' => 'Местоположение',
            'address' => 'Адрес',
            'municipality' => 'Община',
            'settlement' => 'Населено място',
            'phone' => 'Телефон',
            'email' => 'Имейл',
            'website' => 'Уебсайт',
            'eik_code' => 'ЕИК',
            
            // License Fields
            'license_number' => 'Номер на лиценз',
            'license_issue_date' => 'Дата на издаване',
            'license_validity_date' => 'Дата на валидност',
            'license_original' => 'Оригинален лиценз',
            'license_modified' => 'Лиценз с промяна',
            'license_renewed' => 'Подновен лиценз',
            'license_modifications' => 'Промени в лиценза',
            'violations' => 'Нарушения',
            'no_violations' => 'Няма установени нарушения',
            
            // Photo Gallery
            'photo_gallery' => 'Фото галерия',
            'photos' => 'снимки',
            'view_photo' => 'Виж снимка',
            'close' => 'Затвори',
            'previous' => 'Предишна',
            'next' => 'Следваща',
            'no_photos' => 'Няма налични снимки',
            
            // Reviews Section
            'customer_reviews' => 'Отзиви от клиенти',
            'average_rating' => 'Среден рейтинг',
            'write_review' => 'Напиши отзив',
            'your_rating' => 'Вашата оценка',
            'your_review' => 'Вашият отзив',
            'submit_review' => 'Изпрати отзив',
            'review_submitted' => 'Благодарим за вашия отзив!',
            'review_pending' => 'Вашият отзив очаква одобрение.',
            'review_error' => 'Грешка при изпращане на отзив. Моля, опитайте отново.',
            'login_to_review' => 'Моля, влезте в профила си, за да оставите отзив.',
            
            // Pagination
            'showing_results' => 'Показване на',
            'of' => 'от',
            'results' => 'резултата',
            'page' => 'Страница',
            'previous_page' => 'Предишна',
            'next_page' => 'Следваща',
            'first_page' => 'Първа',
            'last_page' => 'Последна',
            
            // Messages
            'loading' => 'Зареждане...',
            'no_results' => 'Не са намерени доставчици.',
            'no_providers_found' => 'Не са намерени доставчици, отговарящи на вашите критерии.',
            'try_different_filters' => 'Опитайте с различни филтри.',
            'error_occurred' => 'Възникна грешка. Моля, опитайте отново.',
            
            // Admin Section
            'providers' => 'Доставчици',
            'add_new_provider' => 'Добави нов доставчик',
            'edit_provider' => 'Редактирай доставчик',
            'provider_details' => 'Детайли на доставчика',
            'basic_information' => 'Основна информация',
            'import_data' => 'Импортирай данни',
            'export_data' => 'Експортирай данни',
            'settings' => 'Настройки',
            'statistics' => 'Статистика',
            
            // Import Tool
            'import_providers' => 'Импортирай доставчици',
            'select_csv_file' => 'Изберете CSV файл',
            'upload_file' => 'Качете файл',
            'import_in_progress' => 'Импортиране...',
            'import_complete' => 'Импортирането приключи успешно!',
            'imported' => 'Импортирани',
            'providers_imported' => 'доставчика',
            'import_error' => 'Грешка при импортиране',
            'file_format_error' => 'Невалиден формат на файл. Моля, използвайте CSV файл.',
            
            // Settings
            'general_settings' => 'Общи настройки',
            'display_settings' => 'Настройки на показване',
            'items_per_page' => 'Елементи на страница',
            'default_view' => 'Изглед по подразбиране',
            'enable_reviews' => 'Активирай отзиви',
            'enable_photos' => 'Активирай снимки',
            'enable_favorites' => 'Активирай любими',
            'map_api_key' => 'API ключ за карти',
            'save_settings' => 'Запази настройки',
            'settings_saved' => 'Настройките са запазени успешно!',
            
            // Service Types (Bulgarian)
            'residential_care' => 'Резидентна грижа',
            'information_consulting' => 'Информиране и консултиране',
            'therapy_rehabilitation' => 'Терапия и рехабилитация',
            'skills_training' => 'Обучение за придобиване на умения',
            'advocacy_mediation' => 'Застъпничество и посредничество',
            'social_rehabilitation' => 'Социална рехабилитация и интеграция',
            'legal_assistance' => 'Правна защита и съдействие',
            'crisis_intervention' => 'Кризисна интервенция',
            'community_support' => 'Подкрепа в общността',
            'temporary_accommodation' => 'Временно настаняване',
            
            // Target Groups (Bulgarian)
            'children' => 'Деца',
            'elderly' => 'Възрастни хора',
            'disabilities' => 'Хора с увреждания',
            'mental_disorders' => 'Лица с психични разстройства',
            'dementia' => 'Лица с деменция',
            'risk_children' => 'Деца в риск',
            'homeless' => 'Бездомни лица',
            'domestic_violence' => 'Жертви на домашно насилие',
            'trafficking_victims' => 'Жертви на трафик',
            'addiction' => 'Лица с проблеми със зависимости',
            
            // Municipalities (top ones)
            'sofia' => 'София',
            'plovdiv' => 'Пловдив',
            'varna' => 'Варна',
            'burgas' => 'Бургас',
            'ruse' => 'Русе',
            'stara_zagora' => 'Стара Загора',
            'pleven' => 'Плевен',
            'sliven' => 'Сливен',
            
            // Date Formats
            'date_format' => 'd.m.Y',
            'datetime_format' => 'd.m.Y H:i',
            
            // Misc
            'yes' => 'Да',
            'no' => 'Не',
            'save' => 'Запази',
            'cancel' => 'Откажи',
            'delete' => 'Изтрий',
            'edit' => 'Редактирай',
            'update' => 'Обнови',
            'back' => 'Назад',
            'confirm' => 'Потвърди',
            'success' => 'Успешно!',
            'error' => 'Грешка!',
            'warning' => 'Внимание!',
            'info' => 'Информация',
        );
    }
    
    /**
     * Get translated string
     */
    public static function __($key) {
        $strings = self::get_strings();
        return isset($strings[$key]) ? $strings[$key] : $key;
    }
    
    /**
     * Echo translated string
     */
    public static function _e($key) {
        echo self::__($key);
    }
}

/**
 * Helper functions for translations
 */
function ssd__($key) {
    return SSD_Bulgarian_Translations::__($key);
}

function ssd_e($key) {
    SSD_Bulgarian_Translations::_e($key);
}
