#!/usr/bin/env python3
"""Extract settings UI catalog from ui-strings.php and add missing locales."""

from __future__ import annotations

import json
import re
import sys
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
SRC = ROOT / "includes" / "ui-strings.php"
OUT = ROOT / "config" / "settings-ui-strings.json"


def parse_locale_blocks(chunk: str) -> dict[str, dict[str, str]]:
    pattern = re.compile(r"'([a-zA-Z_0-9]+)'\s*=>\s*\[")
    locales: dict[str, dict[str, str]] = {}
    i = 0
    while True:
        match = pattern.search(chunk, i)
        if not match:
            break
        loc = match.group(1)
        j = match.end()
        depth = 1
        k = j
        while k < len(chunk) and depth:
            if chunk[k] == "[":
                depth += 1
            elif chunk[k] == "]":
                depth -= 1
            k += 1
        body = chunk[j : k - 1]
        entries = re.findall(r"'((?:\\'|[^'])*)'\s*=>\s*'((?:\\'|[^'])*)'", body)
        locales[loc] = {
            key.replace("\\'", "'"): value.replace("\\'", "'") for key, value in entries
        }
        i = k
    return locales


NEW_LOCALES: dict[str, dict[str, str]] = {
    "hi_IN": {
        "loading": "भाषा लागू हो रही है...",
        "loading_translations": "अनुवाद लोड हो रहे हैं...",
        "loading_finalizing": "प्रोफ़ाइल सिंक हो रही है...",
        "ready": "भाषा लागू हो गई। पृष्ठ पुनः लोड करने के लिए \"अपडेट\" पर क्लिक करें।",
        "success": "भाषा सफलतापूर्वक सहेजी गई।",
        "error": "चयनित भाषा सहेजी नहीं जा सकी।",
        "session_expired": "आपका सत्र समाप्त हो गया है। यह पृष्ठ पुनः लोड करें और फिर कोशिश करें।",
        "refresh": "अपडेट",
        "tab_title": "भाषाएँ",
        "page_description": "Breakdance Builder इंटरफ़ेस, तत्व नाम और प्रथम-पक्ष नियंत्रण में उपयोग की जाने वाली भाषा चुनें।",
        "builder_language_label": "बिल्डर भाषा",
        "header_alt": "बिल्डर भाषा",
        "license_notice": "Builder Languages for Breakdance अनुवाद उपयोग करने के लिए मान्य लाइसेंस सक्रिय करें।",
        "license_heading": "लाइसेंस",
        "license_status": "स्थिति",
        "license_account": "खाता",
        "license_status_dev": "डेवलपमेंट मोड",
        "license_status_active": "सक्रिय",
        "license_status_inactive": "निष्क्रिय",
        "license_dev_description": "डेवलपमेंट बिल्ड। इस वातावरण में सभी अनुवाद अनलॉक हैं।",
        "license_inactive_description": "Breakdance Builder अनुवाद लोड करने के लिए अपना लाइसेंस सक्रिय करें।",
        "license_active_description": "आपका लाइसेंस सक्रिय है। सभी शामिल अनुवाद उपलब्ध हैं।",
        "license_manage": "लाइसेंस प्रबंधित करें",
        "license_buy": "लाइसेंस खरीदें",
        "license_environment": "पर्यावरण",
        "license_environment_sandbox": "सैंडबॉक्स (Freemius)",
        "license_environment_production": "प्रोडक्शन",
        "license_environment_dev": "लोकल डेवलपमेंट",
        "profile_language_heading": "WordPress प्रोफ़ाइल भाषा",
        "profile_language_current": "आपकी प्रोफ़ाइल भाषा: %s",
        "profile_language_auto_hint": "“WordPress प्रोफ़ाइल भाषा उपयोग करें” चुनने पर बिल्डर Users → Profile → Language की भाषा का अनुसरण करता है। बिल्डर भाषा बदलने के लिए वहाँ बदलें।",
        "profile_language_align_hint": "जब आप कोई विशिष्ट बिल्डर भाषा चुनते हैं, तो यह प्लगइन आपकी WordPress प्रोफ़ाइल भाषा भी उसी से मिला देता है।",
        "profile_language_mismatch": "आपकी WordPress प्रोफ़ाइल %1$s पर है, लेकिन बिल्डर भाषा %2$s है। दोनों को एक ही भाषा पर मिलाएँ।",
        "profile_language_edit": "प्रोफ़ाइल भाषा संपादित करें",
        "profile_language_refresh_hint": "यदि Users → Profile अभी भी पुरानी भाषा दिखाता है, तो यहाँ सहेजने के बाद उस पृष्ठ को पुनः लोड करें।",
        "wp_language_pack_missing": "%s के लिए WordPress एडमिन भाषा पैक अभी इंस्टॉल नहीं है। Users → Profile और अन्य एडमिन स्क्रीन अनुवाद के लिए इसे इंस्टॉल करें।",
        "wp_language_pack_installed": "%s के लिए WordPress भाषा पैक इंस्टॉल हो गया।",
        "wp_language_pack_install_button": "WordPress भाषा पैक इंस्टॉल करें",
        "wp_language_pack_installing": "WordPress भाषा पैक इंस्टॉल हो रहा है...",
        "wp_language_pack_downloading": "अनुवाद फ़ाइलें डाउनलोड हो रही हैं...",
        "wp_language_pack_finalizing": "इंस्टॉलेशन पूरा हो रहा है...",
        "wp_language_pack_installed_short": "इंस्टॉलेशन पूर्ण।",
        "wp_language_pack_install_error": "WordPress भाषा पैक इंस्टॉल नहीं हो सका। कनेक्शन जाँचें या Dashboard → Updates → Translations से मैन्युअल इंस्टॉल करें।",
        "wp_language_pack_manual_hint": "किसी व्यवस्थापक से Dashboard → Updates → Translations, या Users → Profile → Language से भाषा इंस्टॉल करने को कहें।",
        "wp_language_pack_reload": "पृष्ठ अपडेट करें",
        "wp_language_pack_reload_hint": "WordPress एडमिन भाषा लागू करने के लिए “पृष्ठ अपडेट करें” पर क्लिक करें।",
    },
    "he_IL": {
        "loading": "מחיל שפה...",
        "loading_translations": "טוען תרגומים...",
        "loading_finalizing": "מסנכרן פרופיל...",
        "ready": "השפה הוחלה. לחץ על \"רענון\" כדי לטעון מחדש את העמוד.",
        "success": "השפה נשמרה בהצלחה.",
        "error": "לא ניתן לשמור את השפה שנבחרה.",
        "session_expired": "פג תוקף ההפעלה. טען מחדש את העמוד ונסה שוב.",
        "refresh": "רענון",
        "tab_title": "שפות",
        "page_description": "בחר את השפה לממשק Breakdance Builder, שמות אלמנטים ופקדים מובנים.",
        "builder_language_label": "שפת הבילדר",
        "header_alt": "שפת הבילדר",
        "license_notice": "הפעל רישיון תקף כדי להשתמש בתרגומי Builder Languages for Breakdance.",
        "license_heading": "רישיון",
        "license_status": "סטטוס",
        "license_account": "חשבון",
        "license_status_dev": "מצב פיתוח",
        "license_status_active": "פעיל",
        "license_status_inactive": "לא פעיל",
        "license_dev_description": "בניית פיתוח. כל התרגומים פתוחים בסביבה זו.",
        "license_inactive_description": "הפעל את הרישיון כדי לטעון תרגומי Breakdance Builder.",
        "license_active_description": "הרישיון פעיל. כל התרגומים הכלולים זמינים.",
        "license_manage": "ניהול רישיון",
        "license_buy": "רכישת רישיון",
        "license_environment": "סביבה",
        "license_environment_sandbox": "Sandbox (Freemius)",
        "license_environment_production": "Production",
        "license_environment_dev": "פיתוח מקומי",
        "profile_language_heading": "שפת פרופיל WordPress",
        "profile_language_current": "הפרופיל שלך מוגדר ל: %s",
        "profile_language_auto_hint": "עם \"השתמש בשפת פרופיל WordPress\", הבילדר עוקב אחרי Users → Profile → Language.",
        "profile_language_align_hint": "כשבוחרים שפת בילדר ספציפית, התוסף גם מעדכן את שפת פרופיל WordPress להתאמה.",
        "profile_language_mismatch": "פרופיל WordPress מוגדר ל-%1$s, אך שפת הבילדר היא %2$s. יישר את שתיהן.",
        "profile_language_edit": "עריכת שפת הפרופיל",
        "profile_language_refresh_hint": "אם Users → Profile עדיין מציג שפה ישנה, טען מחדש את העמוד אחרי השמירה כאן.",
        "wp_language_pack_missing": "חבילת שפת הניהול של WordPress עבור %s עדיין לא מותקנת. התקן אותה לתרגום Users → Profile ומסכי ניהול אחרים.",
        "wp_language_pack_installed": "חבילת שפת WordPress הותקנה עבור %s.",
        "wp_language_pack_install_button": "התקן חבילת שפת WordPress",
        "wp_language_pack_installing": "מתקין חבילת שפת WordPress...",
        "wp_language_pack_downloading": "מוריד קובצי תרגום...",
        "wp_language_pack_finalizing": "מסיים התקנה...",
        "wp_language_pack_installed_short": "ההתקנה הושלמה.",
        "wp_language_pack_install_error": "לא ניתן להתקין את חבילת שפת WordPress. בדוק חיבור או התקן ידנית ב-Dashboard → Updates → Translations.",
        "wp_language_pack_manual_hint": "בקש ממנהל להתקין את השפה ב-Dashboard → Updates → Translations או Users → Profile → Language.",
        "wp_language_pack_reload": "רענון העמוד",
        "wp_language_pack_reload_hint": "לחץ על \"רענון העמוד\" כדי להחיל את שפת ניהול WordPress.",
    },
    "nl_NL": {
        "loading": "Taal toepassen...",
        "loading_translations": "Vertalingen laden...",
        "loading_finalizing": "Profiel synchroniseren...",
        "ready": "Taal toegepast. Klik op \"Vernieuwen\" om de pagina te herladen.",
        "success": "Taal succesvol opgeslagen.",
        "error": "De geselecteerde taal kon niet worden opgeslagen.",
        "session_expired": "Je sessie is verlopen. Herlaad deze pagina en probeer opnieuw.",
        "refresh": "Vernieuwen",
        "tab_title": "Talen",
        "page_description": "Kies de taal voor de Breakdance Builder-interface, elementnamen en first-party bedieningselementen.",
        "builder_language_label": "Builder-taal",
        "header_alt": "Builder-taal",
        "license_notice": "Activeer een geldige licentie om Builder Languages for Breakdance-vertalingen te gebruiken.",
        "license_heading": "Licentie",
        "license_status": "Status",
        "license_account": "Account",
        "license_status_dev": "Ontwikkelmodus",
        "license_status_active": "Actief",
        "license_status_inactive": "Inactief",
        "license_dev_description": "Ontwikkelbuild. Alle vertalingen zijn ontgrendeld in deze omgeving.",
        "license_inactive_description": "Activeer je licentie om Breakdance Builder-vertalingen te laden.",
        "license_active_description": "Je licentie is actief. Alle inbegrepen vertalingen zijn beschikbaar.",
        "license_manage": "Licentie beheren",
        "license_buy": "Licentie kopen",
        "license_environment": "Omgeving",
        "license_environment_sandbox": "Sandbox (Freemius)",
        "license_environment_production": "Productie",
        "license_environment_dev": "Lokale ontwikkeling",
        "profile_language_heading": "WordPress-profiertaal",
        "profile_language_current": "Je profiel staat op: %s",
        "profile_language_auto_hint": "Met “WordPress-profiertaal gebruiken” volgt de builder de taal in Gebruikers → Profiel → Taal.",
        "profile_language_align_hint": "Als je een specifieke builder-taal kiest, werkt deze plugin ook je WordPress-profiertaal bij.",
        "profile_language_mismatch": "Je WordPress-profiel staat op %1$s, maar de builder-taal is %2$s. Stem beide af.",
        "profile_language_edit": "Profiertaal bewerken",
        "profile_language_refresh_hint": "Als Gebruikers → Profiel nog de oude taal toont, herlaad die pagina na het opslaan hier.",
        "wp_language_pack_missing": "Het WordPress-beheertaalpakket voor %s is nog niet geïnstalleerd. Installeer het om Gebruikers → Profiel en andere beheerschermen te vertalen.",
        "wp_language_pack_installed": "WordPress-taalpakket geïnstalleerd voor %s.",
        "wp_language_pack_install_button": "WordPress-taalpakket installeren",
        "wp_language_pack_installing": "WordPress-taalpakket installeren...",
        "wp_language_pack_downloading": "Vertalingsbestanden downloaden...",
        "wp_language_pack_finalizing": "Installatie afronden...",
        "wp_language_pack_installed_short": "Installatie voltooid.",
        "wp_language_pack_install_error": "Kon het WordPress-taalpakket niet installeren. Controleer de verbinding of installeer handmatig via Dashboard → Updates → Vertalingen.",
        "wp_language_pack_manual_hint": "Vraag een beheerder om de taal te installeren via Dashboard → Updates → Vertalingen of Gebruikers → Profiel → Taal.",
        "wp_language_pack_reload": "Pagina vernieuwen",
        "wp_language_pack_reload_hint": "Klik op “Pagina vernieuwen” om de WordPress-beheertaal toe te passen.",
    },
    "pl_PL": {
        "loading": "Stosowanie języka...",
        "loading_translations": "Ładowanie tłumaczeń...",
        "loading_finalizing": "Synchronizacja profilu...",
        "ready": "Język zastosowany. Kliknij „Odśwież”, aby przeładować stronę.",
        "success": "Język został zapisany.",
        "error": "Nie udało się zapisać wybranego języka.",
        "session_expired": "Sesja wygasła. Odśwież tę stronę i spróbuj ponownie.",
        "refresh": "Odśwież",
        "tab_title": "Języki",
        "page_description": "Wybierz język interfejsu Breakdance Builder, nazw elementów i natywnych kontrolek.",
        "builder_language_label": "Język buildera",
        "header_alt": "Język buildera",
        "license_notice": "Aktywuj ważną licencję, aby korzystać z tłumaczeń Builder Languages for Breakdance.",
        "license_heading": "Licencja",
        "license_status": "Status",
        "license_account": "Konto",
        "license_status_dev": "Tryb deweloperski",
        "license_status_active": "Aktywna",
        "license_status_inactive": "Nieaktywna",
        "license_dev_description": "Kompilacja deweloperska. Wszystkie tłumaczenia są odblokowane w tym środowisku.",
        "license_inactive_description": "Aktywuj licencję, aby wczytać tłumaczenia Breakdance Builder.",
        "license_active_description": "Licencja jest aktywna. Wszystkie dołączone tłumaczenia są dostępne.",
        "license_manage": "Zarządzaj licencją",
        "license_buy": "Kup licencję",
        "license_environment": "Środowisko",
        "license_environment_sandbox": "Sandbox (Freemius)",
        "license_environment_production": "Produkcja",
        "license_environment_dev": "Lokalne środowisko deweloperskie",
        "profile_language_heading": "Język profilu WordPress",
        "profile_language_current": "Twój profil jest ustawiony na: %s",
        "profile_language_auto_hint": "Przy „Użyj języka profilu WordPress” builder podąża za językiem w Użytkownicy → Profil → Język.",
        "profile_language_align_hint": "Po wyborze konkretnego języka buildera wtyczka aktualizuje też język profilu WordPress.",
        "profile_language_mismatch": "Profil WordPress jest ustawiony na %1$s, a język buildera to %2$s. Ujednolić oba.",
        "profile_language_edit": "Edytuj język profilu",
        "profile_language_refresh_hint": "Jeśli Użytkownicy → Profil nadal pokazuje stary język, odśwież tę stronę po zapisaniu tutaj.",
        "wp_language_pack_missing": "Pakiet językowy panelu WordPress dla %s nie jest jeszcze zainstalowany. Zainstaluj go, aby tłumaczyć Użytkownicy → Profil i inne ekrany admina.",
        "wp_language_pack_installed": "Zainstalowano pakiet językowy WordPress dla %s.",
        "wp_language_pack_install_button": "Zainstaluj pakiet językowy WordPress",
        "wp_language_pack_installing": "Instalowanie pakietu językowego WordPress...",
        "wp_language_pack_downloading": "Pobieranie plików tłumaczeń...",
        "wp_language_pack_finalizing": "Kończenie instalacji...",
        "wp_language_pack_installed_short": "Instalacja zakończona.",
        "wp_language_pack_install_error": "Nie udało się zainstalować pakietu językowego WordPress. Sprawdź połączenie lub zainstaluj ręcznie w Kokpit → Aktualizacje → Tłumaczenia.",
        "wp_language_pack_manual_hint": "Poproś administratora o instalację języka w Kokpit → Aktualizacje → Tłumaczenia lub Użytkownicy → Profil → Język.",
        "wp_language_pack_reload": "Odśwież stronę",
        "wp_language_pack_reload_hint": "Kliknij „Odśwież stronę”, aby zastosować język panelu WordPress.",
    },
    "ru_RU": {
        "loading": "Применение языка...",
        "loading_translations": "Загрузка переводов...",
        "loading_finalizing": "Синхронизация профиля...",
        "ready": "Язык применён. Нажмите «Обновить», чтобы перезагрузить страницу.",
        "success": "Язык успешно сохранён.",
        "error": "Не удалось сохранить выбранный язык.",
        "session_expired": "Сеанс истёк. Перезагрузите страницу и попробуйте снова.",
        "refresh": "Обновить",
        "tab_title": "Языки",
        "page_description": "Выберите язык интерфейса Breakdance Builder, названий элементов и встроенных элементов управления.",
        "builder_language_label": "Язык билдера",
        "header_alt": "Язык билдера",
        "license_notice": "Активируйте действующую лицензию, чтобы использовать переводы Builder Languages for Breakdance.",
        "license_heading": "Лицензия",
        "license_status": "Статус",
        "license_account": "Аккаунт",
        "license_status_dev": "Режим разработки",
        "license_status_active": "Активна",
        "license_status_inactive": "Неактивна",
        "license_dev_description": "Сборка для разработки. Все переводы разблокированы в этой среде.",
        "license_inactive_description": "Активируйте лицензию, чтобы загрузить переводы Breakdance Builder.",
        "license_active_description": "Лицензия активна. Все включённые переводы доступны.",
        "license_manage": "Управление лицензией",
        "license_buy": "Купить лицензию",
        "license_environment": "Среда",
        "license_environment_sandbox": "Sandbox (Freemius)",
        "license_environment_production": "Production",
        "license_environment_dev": "Локальная разработка",
        "profile_language_heading": "Язык профиля WordPress",
        "profile_language_current": "Ваш профиль установлен на: %s",
        "profile_language_auto_hint": "При выборе «Использовать язык профиля WordPress» билдер следует языку в Пользователи → Профиль → Язык.",
        "profile_language_align_hint": "При выборе конкретного языка билдера плагин также обновляет язык профиля WordPress.",
        "profile_language_mismatch": "Профиль WordPress установлен на %1$s, а язык билдера — %2$s. Выровняйте оба.",
        "profile_language_edit": "Изменить язык профиля",
        "profile_language_refresh_hint": "Если Пользователи → Профиль всё ещё показывает старый язык, перезагрузите ту страницу после сохранения здесь.",
        "wp_language_pack_missing": "Языковой пакет админки WordPress для %s ещё не установлен. Установите его для перевода Пользователи → Профиль и других экранов.",
        "wp_language_pack_installed": "Языковой пакет WordPress установлен для %s.",
        "wp_language_pack_install_button": "Установить языковой пакет WordPress",
        "wp_language_pack_installing": "Установка языкового пакета WordPress...",
        "wp_language_pack_downloading": "Загрузка файлов перевода...",
        "wp_language_pack_finalizing": "Завершение установки...",
        "wp_language_pack_installed_short": "Установка завершена.",
        "wp_language_pack_install_error": "Не удалось установить языковой пакет WordPress. Проверьте соединение или установите вручную в Консоль → Обновления → Переводы.",
        "wp_language_pack_manual_hint": "Попросите администратора установить язык в Консоль → Обновления → Переводы или Пользователи → Профиль → Язык.",
        "wp_language_pack_reload": "Обновить страницу",
        "wp_language_pack_reload_hint": "Нажмите «Обновить страницу», чтобы применить язык админки WordPress.",
    },
    "zh_CN": {
        "loading": "正在应用语言...",
        "loading_translations": "正在加载翻译...",
        "loading_finalizing": "正在同步个人资料...",
        "ready": "语言已应用。点击“刷新”重新加载页面。",
        "success": "语言已成功保存。",
        "error": "无法保存所选语言。",
        "session_expired": "会话已过期。请重新加载此页面后再试。",
        "refresh": "刷新",
        "tab_title": "语言",
        "page_description": "选择 Breakdance Builder 界面、元素名称和官方控件使用的语言。",
        "builder_language_label": "构建器语言",
        "header_alt": "构建器语言",
        "license_notice": "请激活有效许可证以使用 Builder Languages for Breakdance 翻译。",
        "license_heading": "许可证",
        "license_status": "状态",
        "license_account": "账户",
        "license_status_dev": "开发模式",
        "license_status_active": "已激活",
        "license_status_inactive": "未激活",
        "license_dev_description": "开发构建。此环境中所有翻译均已解锁。",
        "license_inactive_description": "请激活许可证以加载 Breakdance Builder 翻译。",
        "license_active_description": "许可证已激活。所有包含的翻译均可用。",
        "license_manage": "管理许可证",
        "license_buy": "购买许可证",
        "license_environment": "环境",
        "license_environment_sandbox": "沙盒 (Freemius)",
        "license_environment_production": "生产环境",
        "license_environment_dev": "本地开发",
        "profile_language_heading": "WordPress 个人资料语言",
        "profile_language_current": "您的个人资料语言为：%s",
        "profile_language_auto_hint": "选择“使用 WordPress 个人资料语言”时，构建器会跟随 用户 → 个人资料 → 语言。",
        "profile_language_align_hint": "选择特定构建器语言时，本插件也会更新您的 WordPress 个人资料语言以匹配。",
        "profile_language_mismatch": "您的 WordPress 个人资料为 %1$s，但构建器语言为 %2$s。请将两者对齐。",
        "profile_language_edit": "编辑个人资料语言",
        "profile_language_refresh_hint": "如果 用户 → 个人资料 仍显示旧语言，请在此处保存后重新加载该页面。",
        "wp_language_pack_missing": "%s 的 WordPress 管理语言包尚未安装。安装后可翻译 用户 → 个人资料 及其他管理界面。",
        "wp_language_pack_installed": "已为 %s 安装 WordPress 语言包。",
        "wp_language_pack_install_button": "安装 WordPress 语言包",
        "wp_language_pack_installing": "正在安装 WordPress 语言包...",
        "wp_language_pack_downloading": "正在下载翻译文件...",
        "wp_language_pack_finalizing": "正在完成安装...",
        "wp_language_pack_installed_short": "安装完成。",
        "wp_language_pack_install_error": "无法安装 WordPress 语言包。请检查网络，或在 仪表盘 → 更新 → 翻译 中手动安装。",
        "wp_language_pack_manual_hint": "请让管理员在 仪表盘 → 更新 → 翻译 或 用户 → 个人资料 → 语言 中安装该语言。",
        "wp_language_pack_reload": "刷新页面",
        "wp_language_pack_reload_hint": "点击“刷新页面”以应用 WordPress 管理语言。",
    },
    "ko_KR": {
        "loading": "언어 적용 중...",
        "loading_translations": "번역 불러오는 중...",
        "loading_finalizing": "프로필 동기화 중...",
        "ready": "언어가 적용되었습니다. 페이지를 다시 로드하려면 \"새로고침\"을 클릭하세요.",
        "success": "언어가 성공적으로 저장되었습니다.",
        "error": "선택한 언어를 저장할 수 없습니다.",
        "session_expired": "세션이 만료되었습니다. 이 페이지를 새로고침한 후 다시 시도하세요.",
        "refresh": "새로고침",
        "tab_title": "언어",
        "page_description": "Breakdance Builder 인터페이스, 요소 이름 및 기본 컨트롤에 사용할 언어를 선택하세요.",
        "builder_language_label": "빌더 언어",
        "header_alt": "빌더 언어",
        "license_notice": "Builder Languages for Breakdance 번역을 사용하려면 유효한 라이선스를 활성화하세요.",
        "license_heading": "라이선스",
        "license_status": "상태",
        "license_account": "계정",
        "license_status_dev": "개발 모드",
        "license_status_active": "활성",
        "license_status_inactive": "비활성",
        "license_dev_description": "개발 빌드입니다. 이 환경에서는 모든 번역이 잠금 해제됩니다.",
        "license_inactive_description": "Breakdance Builder 번역을 불러오려면 라이선스를 활성화하세요.",
        "license_active_description": "라이선스가 활성 상태입니다. 포함된 모든 번역을 사용할 수 있습니다.",
        "license_manage": "라이선스 관리",
        "license_buy": "라이선스 구매",
        "license_environment": "환경",
        "license_environment_sandbox": "샌드박스 (Freemius)",
        "license_environment_production": "프로덕션",
        "license_environment_dev": "로컬 개발",
        "profile_language_heading": "WordPress 프로필 언어",
        "profile_language_current": "프로필 언어: %s",
        "profile_language_auto_hint": "“WordPress 프로필 언어 사용”을 선택하면 빌더가 사용자 → 프로필 → 언어를 따릅니다.",
        "profile_language_align_hint": "특정 빌더 언어를 선택하면 이 플러그인이 WordPress 프로필 언어도 맞춰 업데이트합니다.",
        "profile_language_mismatch": "WordPress 프로필은 %1$s인데 빌더 언어는 %2$s입니다. 둘을 맞추세요.",
        "profile_language_edit": "프로필 언어 편집",
        "profile_language_refresh_hint": "사용자 → 프로필에 이전 언어가 보이면 여기서 저장한 뒤 해당 페이지를 새로고침하세요.",
        "wp_language_pack_missing": "%s용 WordPress 관리자 언어 팩이 아직 설치되지 않았습니다. 사용자 → 프로필 및 다른 관리 화면 번역을 위해 설치하세요.",
        "wp_language_pack_installed": "%s용 WordPress 언어 팩이 설치되었습니다.",
        "wp_language_pack_install_button": "WordPress 언어 팩 설치",
        "wp_language_pack_installing": "WordPress 언어 팩 설치 중...",
        "wp_language_pack_downloading": "번역 파일 다운로드 중...",
        "wp_language_pack_finalizing": "설치 마무리 중...",
        "wp_language_pack_installed_short": "설치 완료.",
        "wp_language_pack_install_error": "WordPress 언어 팩을 설치할 수 없습니다. 연결을 확인하거나 대시보드 → 업데이트 → 번역에서 수동 설치하세요.",
        "wp_language_pack_manual_hint": "관리자에게 대시보드 → 업데이트 → 번역 또는 사용자 → 프로필 → 언어에서 설치를 요청하세요.",
        "wp_language_pack_reload": "페이지 새로고침",
        "wp_language_pack_reload_hint": "WordPress 관리자 언어를 적용하려면 “페이지 새로고침”을 클릭하세요.",
    },
}


def main() -> int:
    if hasattr(sys.stdout, "reconfigure"):
        sys.stdout.reconfigure(encoding="utf-8", errors="replace")

    src = SRC.read_text(encoding="utf-8")
    start = src.index("function breakdance_languages_settings_ui_catalog")
    end = src.index("function breakdance_languages_locale_labels_catalog")
    locales = parse_locale_blocks(src[start:end])

    # es_LA from es_ES with LATAM-friendly tweaks where needed
    if "es_ES" in locales and "es_LA" not in locales:
        es_la = dict(locales["es_ES"])
        locales["es_LA"] = es_la

    for loc, strings in NEW_LOCALES.items():
        locales[loc] = strings

    # Ensure every locale has all en_US keys
    base = locales["en_US"]
    for loc, strings in list(locales.items()):
        locales[loc] = {**base, **strings}

    OUT.write_text(json.dumps(locales, ensure_ascii=False, indent=2) + "\n", encoding="utf-8")
    print(f"wrote {OUT} with {len(locales)} locales")
    for loc in sorted(locales):
        print(f"  {loc}: {len(locales[loc])} keys")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
