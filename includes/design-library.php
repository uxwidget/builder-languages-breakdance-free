<?php
/**
 * Builder Languages for Breakdance — Design Library.
 *
 * @package Builder Languages Breakdance
 * @author  UX Widget
 * @link    https://uxwidget.com
 * @license GPL-2.0-or-later
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

add_filter('breakdance_design_library_providers', 'breakdance_languages_translate_design_library_providers', 20);
add_action('wp_enqueue_scripts', 'breakdance_languages_override_design_library_script', 100);
add_action('wp_ajax_breakdance_languages_design_library_proxy', 'breakdance_languages_handle_design_library_proxy');

/**
 * Hosts allowed for the design library HTML proxy.
 *
 * @return list<string>
 */
function breakdance_languages_design_library_allowed_hosts(): array
{
    return [
        'breakdancelibrary.com',
        'www.breakdancelibrary.com',
        'breakdancedemos.com',
        'www.breakdancedemos.com',
    ];
}

/**
 * UI labels for design sets such as Samba (remote HTML + local buttons).
 *
 * @return array<string, array<string, string>>
 */
function breakdance_languages_design_library_ui_maps(): array
{
    return [
        'pt_BR' => [
            'Add to page' => 'Adicionar à página',
            'Copy to clipboard' => 'Copiar para a área de transferência',
            'View Sections' => 'Ver seções',
            'Go home' => 'Ir para o início',
            'Visit site' => 'Visitar site',
            'Close modal' => 'Fechar modal',
            'Filter by type' => 'Filtrar por tipo',
            'Portfolio' => 'Portfólio',
            'My Account' => 'Minha conta',
            'My account' => 'Minha conta',
            'Shop' => 'Loja',
            'Tabs' => 'Abas',
            'Info Boxes' => 'Caixas de informação',
            'Payments Icons' => 'Ícones de pagamento',
            'Reviews' => 'Avaliações',
            'Incentives' => 'Incentivos',
            'Content Cards' => 'Cards de conteúdo',
            'Author Bio' => 'Bio do autor',
            'Coming Soon' => 'Em breve',
            'Content Sections' => 'Seções de conteúdo',
            'Progress Bars' => 'Barras de progresso',
            'Gallery' => 'Galeria',
            'Image Boxes' => 'Caixas de imagem',
            'Video Boxes' => 'Caixas de vídeo',
            'Timeline' => 'Linha do tempo',
            'Post Lists' => 'Listas de posts',
            'Pricing' => 'Preços',
            'Banners' => 'Banners',
            'Newsletters' => 'Newsletters',
            'Headers' => 'Cabeçalhos',
            'Team' => 'Equipe',
            'Features' => 'Recursos',
            'Call to Actions' => 'Chamadas para ação',
            'Footers' => 'Rodapés',
            'Heros and Titles' => 'Heróis e títulos',
            'Contact' => 'Contato',
            'Stats' => 'Estatísticas',
            'FAQ' => 'Perguntas frequentes',
            'Logo Clouds' => 'Nuvem de logos',
            'Icon Boxes' => 'Caixas de ícones',
            'Testimonials' => 'Depoimentos',
            'UI Kit' => 'Kit de UI',
            'Fancy Sections' => 'Seções elegantes',
            'Element Demos' => 'Demonstrações de elementos',
            'Masks' => 'Máscaras',
            'Animated Sections' => 'Seções animadas',
            'Creative Sections' => 'Seções criativas',
            '3D Sections' => 'Seções 3D',
            'Animated Text' => 'Texto animado',
            'Animated Background' => 'Fundo animado',
            'Animated Backgrounds' => 'Fundos animados',
            'Advanced Sliders' => 'Sliders avançados',
            'CTA' => 'CTA',
        ],
        'pt_PT' => [
            'Add to page' => 'Adicionar à página',
            'Copy to clipboard' => 'Copiar para a área de transferência',
            'View Sections' => 'Ver secções',
            'Go home' => 'Ir para o início',
            'Visit site' => 'Visitar site',
            'Close modal' => 'Fechar modal',
            'Filter by type' => 'Filtrar por tipo',
            'Portfolio' => 'Portefólio',
            'My Account' => 'A minha conta',
            'My account' => 'A minha conta',
            'Shop' => 'Loja',
            'Tabs' => 'Separadores',
            'Info Boxes' => 'Caixas de informação',
            'Payments Icons' => 'Ícones de pagamento',
            'Reviews' => 'Avaliações',
            'Incentives' => 'Incentivos',
            'Content Cards' => 'Cartões de conteúdo',
            'Author Bio' => 'Biografia do autor',
            'Coming Soon' => 'Em breve',
            'Content Sections' => 'Secções de conteúdo',
            'Progress Bars' => 'Barras de progresso',
            'Gallery' => 'Galeria',
            'Image Boxes' => 'Caixas de imagem',
            'Video Boxes' => 'Caixas de vídeo',
            'Timeline' => 'Linha do tempo',
            'Post Lists' => 'Listas de publicações',
            'Pricing' => 'Preços',
            'Banners' => 'Banners',
            'Newsletters' => 'Newsletters',
            'Headers' => 'Cabeçalhos',
            'Team' => 'Equipa',
            'Features' => 'Funcionalidades',
            'Call to Actions' => 'Chamadas à ação',
            'Footers' => 'Rodapés',
            'Heros and Titles' => 'Heróis e títulos',
            'Contact' => 'Contacto',
            'Stats' => 'Estatísticas',
            'FAQ' => 'Perguntas frequentes',
            'Logo Clouds' => 'Nuvem de logótipos',
            'Icon Boxes' => 'Caixas de ícones',
            'Testimonials' => 'Testemunhos',
            'UI Kit' => 'Kit de UI',
            'Fancy Sections' => 'Secções elegantes',
            'Element Demos' => 'Demonstrações de elementos',
            'Masks' => 'Máscaras',
            'Animated Sections' => 'Secções animadas',
            'Creative Sections' => 'Secções criativas',
            '3D Sections' => 'Secções 3D',
            'Animated Text' => 'Texto animado',
            'Animated Background' => 'Fundo animado',
            'Animated Backgrounds' => 'Fundos animados',
            'Advanced Sliders' => 'Sliders avançados',
            'CTA' => 'CTA',
        ],
        'it_IT' => [
            'Add to page' => 'Aggiungi alla pagina',
            'Copy to clipboard' => 'Copia negli appunti',
            'View Sections' => 'Visualizza sezioni',
            'Go home' => 'Vai alla home',
            'Visit site' => 'Visita il sito',
            'Close modal' => 'Chiudi modale',
            'Filter by type' => 'Filtra per tipo',
            'Portfolio' => 'Portfolio',
            'My Account' => 'Il mio account',
            'My account' => 'Il mio account',
            'Shop' => 'Negozio',
            'Tabs' => 'Schede',
            'Info Boxes' => 'Box informativi',
            'Payments Icons' => 'Icone di pagamento',
            'Reviews' => 'Recensioni',
            'Incentives' => 'Incentivi',
            'Content Cards' => 'Card di contenuto',
            'Author Bio' => 'Bio autore',
            'Coming Soon' => 'Prossimamente',
            'Content Sections' => 'Sezioni di contenuto',
            'Progress Bars' => 'Barre di avanzamento',
            'Gallery' => 'Galleria',
            'Image Boxes' => 'Box immagine',
            'Video Boxes' => 'Box video',
            'Timeline' => 'Timeline',
            'Post Lists' => 'Elenchi di articoli',
            'Pricing' => 'Prezzi',
            'Banners' => 'Banner',
            'Newsletters' => 'Newsletter',
            'Headers' => 'Intestazioni',
            'Team' => 'Team',
            'Features' => 'Funzionalità',
            'Call to Actions' => 'Call to action',
            'Footers' => 'Footer',
            'Heros and Titles' => 'Hero e titoli',
            'Contact' => 'Contatto',
            'Stats' => 'Statistiche',
            'FAQ' => 'Domande frequenti',
            'Logo Clouds' => 'Nuvola di loghi',
            'Icon Boxes' => 'Box icona',
            'Testimonials' => 'Testimonianze',
            'UI Kit' => 'Kit UI',
            'Fancy Sections' => 'Sezioni eleganti',
            'Element Demos' => 'Demo degli elementi',
            'Masks' => 'Maschere',
            'Animated Sections' => 'Sezioni animate',
            'Creative Sections' => 'Sezioni creative',
            '3D Sections' => 'Sezioni 3D',
            'Animated Text' => 'Testo animato',
            'Animated Background' => 'Sfondo animato',
            'Animated Backgrounds' => 'Sfondi animati',
            'Advanced Sliders' => 'Slider avanzati',
            'CTA' => 'CTA',
        ],
        'ja_JP' => [
            'Add to page' => 'ページに追加',
            'Copy to clipboard' => 'クリップボードにコピー',
            'View Sections' => 'セクションを表示',
            'Masks' => 'マスク',
            'Animated Sections' => 'アニメーションセクション',
            'Creative Sections' => 'クリエイティブセクション',
            '3D Sections' => '3Dセクション',
            'Animated Text' => 'アニメーションテキスト',
            'Animated Background' => 'アニメーション背景',
            'Animated Backgrounds' => 'アニメーション背景',
            'Advanced Sliders' => '高度なスライダー',
            'CTA' => 'CTA',
            'Go home' => 'ホームへ',
            'Visit site' => 'サイトを見る',
            'Close modal' => 'モーダルを閉じる',
            'Filter by type' => '種類で絞り込み',
            'Fancy Sections' => 'ファンシーセクション',
            'Element Demos' => '要素デモ',
        ],
        'fr_FR' => [
            'Add to page' => 'Ajouter à la page',
            'Copy to clipboard' => 'Copier dans le presse-papiers',
            'View Sections' => 'Voir les sections',
            'Go home' => 'Retour à l’accueil',
            'Visit site' => 'Visiter le site',
            'Close modal' => 'Fermer la fenêtre',
            'Filter by type' => 'Filtrer par type',
            'Portfolio' => 'Portfolio',
            'My Account' => 'Mon compte',
            'My account' => 'Mon compte',
            'Shop' => 'Boutique',
            'Tabs' => 'Onglets',
            'Info Boxes' => 'Boîtes d’info',
            'Payments Icons' => 'Icônes de paiement',
            'Reviews' => 'Avis',
            'Incentives' => 'Incitations',
            'Content Cards' => 'Cartes de contenu',
            'Author Bio' => 'Bio de l’auteur',
            'Coming Soon' => 'Bientôt disponible',
            'Content Sections' => 'Sections de contenu',
            'Progress Bars' => 'Barres de progression',
            'Gallery' => 'Galerie',
            'Image Boxes' => 'Boîtes image',
            'Video Boxes' => 'Boîtes vidéo',
            'Timeline' => 'Chronologie',
            'Post Lists' => 'Listes d’articles',
            'Pricing' => 'Tarifs',
            'Banners' => 'Bannières',
            'Newsletters' => 'Newsletters',
            'Headers' => 'En-têtes',
            'Team' => 'Équipe',
            'Features' => 'Fonctionnalités',
            'Call to Actions' => 'Appels à l’action',
            'Footers' => 'Pieds de page',
            'Heros and Titles' => 'Heros et titres',
            'Contact' => 'Contact',
            'Stats' => 'Statistiques',
            'FAQ' => 'FAQ',
            'Logo Clouds' => 'Nuage de logos',
            'Icon Boxes' => 'Boîtes icône',
            'Testimonials' => 'Témoignages',
            'UI Kit' => 'Kit UI',
            'Fancy Sections' => 'Sections élégantes',
            'Element Demos' => 'Démos d’éléments',
            'Masks' => 'Masques',
            'Animated Sections' => 'Sections animées',
            'Creative Sections' => 'Sections créatives',
            '3D Sections' => 'Sections 3D',
            'Animated Text' => 'Texte animé',
            'Animated Background' => 'Arrière-plan animé',
            'Animated Backgrounds' => 'Arrière-plans animés',
            'Advanced Sliders' => 'Sliders avancés',
            'CTA' => 'CTA',
        ],
        'de_DE' => [
            'Add to page' => 'Zur Seite hinzufügen',
            'Copy to clipboard' => 'In Zwischenablage kopieren',
            'View Sections' => 'Abschnitte anzeigen',
            'Go home' => 'Zur Startseite',
            'Visit site' => 'Website besuchen',
            'Close modal' => 'Modal schließen',
            'Filter by type' => 'Nach Typ filtern',
            'Portfolio' => 'Portfolio',
            'My Account' => 'Mein Konto',
            'My account' => 'Mein Konto',
            'Shop' => 'Shop',
            'Tabs' => 'Tabs',
            'Info Boxes' => 'Infoboxen',
            'Payments Icons' => 'Zahlungssymbole',
            'Reviews' => 'Bewertungen',
            'Incentives' => 'Anreize',
            'Content Cards' => 'Inhaltskarten',
            'Author Bio' => 'Autorenbio',
            'Coming Soon' => 'Demnächst',
            'Content Sections' => 'Inhaltsabschnitte',
            'Progress Bars' => 'Fortschrittsbalken',
            'Gallery' => 'Galerie',
            'Image Boxes' => 'Bildboxen',
            'Video Boxes' => 'Videoboxen',
            'Timeline' => 'Zeitleiste',
            'Post Lists' => 'Beitragslisten',
            'Pricing' => 'Preise',
            'Banners' => 'Banner',
            'Newsletters' => 'Newsletter',
            'Headers' => 'Kopfzeilen',
            'Team' => 'Team',
            'Features' => 'Funktionen',
            'Call to Actions' => 'Call-to-Actions',
            'Footers' => 'Fußzeilen',
            'Heros and Titles' => 'Heroes und Titel',
            'Contact' => 'Kontakt',
            'Stats' => 'Statistiken',
            'FAQ' => 'FAQ',
            'Logo Clouds' => 'Logo-Wolke',
            'Icon Boxes' => 'Icon-Boxen',
            'Testimonials' => 'Kundenstimmen',
            'UI Kit' => 'UI-Kit',
            'Fancy Sections' => 'Elegante Abschnitte',
            'Element Demos' => 'Element-Demos',
            'Masks' => 'Masken',
            'Animated Sections' => 'Animierte Abschnitte',
            'Creative Sections' => 'Kreative Abschnitte',
            '3D Sections' => '3D-Abschnitte',
            'Animated Text' => 'Animierter Text',
            'Animated Background' => 'Animierter Hintergrund',
            'Animated Backgrounds' => 'Animierte Hintergründe',
            'Advanced Sliders' => 'Erweiterte Slider',
            'CTA' => 'CTA',
        ],
        'es_ES' => [
            'Add to page' => 'Añadir a la página',
            'Copy to clipboard' => 'Copiar al portapapeles',
            'View Sections' => 'Ver secciones',
            'Go home' => 'Ir al inicio',
            'Visit site' => 'Visitar sitio',
            'Close modal' => 'Cerrar modal',
            'Filter by type' => 'Filtrar por tipo',
            'Portfolio' => 'Portfolio',
            'My Account' => 'Mi cuenta',
            'My account' => 'Mi cuenta',
            'Shop' => 'Tienda',
            'Tabs' => 'Pestañas',
            'Info Boxes' => 'Cajas de información',
            'Payments Icons' => 'Iconos de pago',
            'Reviews' => 'Reseñas',
            'Incentives' => 'Incentivos',
            'Content Cards' => 'Tarjetas de contenido',
            'Author Bio' => 'Bio del autor',
            'Coming Soon' => 'Próximamente',
            'Content Sections' => 'Secciones de contenido',
            'Progress Bars' => 'Barras de progreso',
            'Gallery' => 'Galería',
            'Image Boxes' => 'Cajas de imagen',
            'Video Boxes' => 'Cajas de vídeo',
            'Timeline' => 'Línea de tiempo',
            'Post Lists' => 'Listas de entradas',
            'Pricing' => 'Precios',
            'Banners' => 'Banners',
            'Newsletters' => 'Newsletters',
            'Headers' => 'Encabezados',
            'Team' => 'Equipo',
            'Features' => 'Funciones',
            'Call to Actions' => 'Llamadas a la acción',
            'Footers' => 'Pies de página',
            'Heros and Titles' => 'Héroes y títulos',
            'Contact' => 'Contacto',
            'Stats' => 'Estadísticas',
            'FAQ' => 'Preguntas frecuentes',
            'Logo Clouds' => 'Nube de logos',
            'Icon Boxes' => 'Cajas de iconos',
            'Testimonials' => 'Testimonios',
            'UI Kit' => 'Kit de UI',
            'Fancy Sections' => 'Secciones elegantes',
            'Element Demos' => 'Demos de elementos',
            'Masks' => 'Máscaras',
            'Animated Sections' => 'Secciones animadas',
            'Creative Sections' => 'Secciones creativas',
            '3D Sections' => 'Secciones 3D',
            'Animated Text' => 'Texto animado',
            'Animated Background' => 'Fondo animado',
            'Animated Backgrounds' => 'Fondos animados',
            'Advanced Sliders' => 'Sliders avanzados',
            'CTA' => 'CTA',
        ],
        'es_LA' => [
            'Add to page' => 'Agregar a la página',
            'Copy to clipboard' => 'Copiar al portapapeles',
            'View Sections' => 'Ver secciones',
            'Go home' => 'Ir al inicio',
            'Visit site' => 'Visitar sitio',
            'Close modal' => 'Cerrar modal',
            'Filter by type' => 'Filtrar por tipo',
            'Portfolio' => 'Portfolio',
            'My Account' => 'Mi cuenta',
            'My account' => 'Mi cuenta',
            'Shop' => 'Tienda',
            'Tabs' => 'Pestañas',
            'Info Boxes' => 'Cajas de información',
            'Payments Icons' => 'Íconos de pago',
            'Reviews' => 'Reseñas',
            'Incentives' => 'Incentivos',
            'Content Cards' => 'Tarjetas de contenido',
            'Author Bio' => 'Bio del autor',
            'Coming Soon' => 'Próximamente',
            'Content Sections' => 'Secciones de contenido',
            'Progress Bars' => 'Barras de progreso',
            'Gallery' => 'Galería',
            'Image Boxes' => 'Cajas de imagen',
            'Video Boxes' => 'Cajas de video',
            'Timeline' => 'Línea de tiempo',
            'Post Lists' => 'Listas de posts',
            'Pricing' => 'Precios',
            'Banners' => 'Banners',
            'Newsletters' => 'Newsletters',
            'Headers' => 'Encabezados',
            'Team' => 'Equipo',
            'Features' => 'Funciones',
            'Call to Actions' => 'Llamadas a la acción',
            'Footers' => 'Pies de página',
            'Heros and Titles' => 'Héroes y títulos',
            'Contact' => 'Contacto',
            'Stats' => 'Estadísticas',
            'FAQ' => 'Preguntas frecuentes',
            'Logo Clouds' => 'Nube de logos',
            'Icon Boxes' => 'Cajas de íconos',
            'Testimonials' => 'Testimonios',
            'UI Kit' => 'Kit de UI',
            'Fancy Sections' => 'Secciones elegantes',
            'Element Demos' => 'Demos de elementos',
            'Masks' => 'Máscaras',
            'Animated Sections' => 'Secciones animadas',
            'Creative Sections' => 'Secciones creativas',
            '3D Sections' => 'Secciones 3D',
            'Animated Text' => 'Texto animado',
            'Animated Background' => 'Fondo animado',
            'Animated Backgrounds' => 'Fondos animados',
            'Advanced Sliders' => 'Sliders avanzados',
            'CTA' => 'CTA',
        ],
        'ar' => [
            'Add to page' => 'إضافة إلى الصفحة',
            'Copy to clipboard' => 'نسخ إلى الحافظة',
            'View Sections' => 'عرض الأقسام',
            'Go home' => 'الانتقال إلى الصفحة الرئيسية',
            'Visit site' => 'زيارة الموقع',
            'Close modal' => 'إغلاق النافذة',
            'Filter by type' => 'تصفية حسب النوع',
            'Portfolio' => 'معرض الأعمال',
            'My Account' => 'حسابي',
            'My account' => 'حسابي',
            'Shop' => 'المتجر',
            'Tabs' => 'علامات التبويب',
            'Info Boxes' => 'صناديق المعلومات',
            'Payments Icons' => 'أيقونات الدفع',
            'Reviews' => 'المراجعات',
            'Incentives' => 'الحوافز',
            'Content Cards' => 'بطاقات المحتوى',
            'Author Bio' => 'نبذة عن الكاتب',
            'Coming Soon' => 'قريباً',
            'Content Sections' => 'أقسام المحتوى',
            'Progress Bars' => 'أشرطة التقدم',
            'Gallery' => 'المعرض',
            'Image Boxes' => 'صناديق الصور',
            'Video Boxes' => 'صناديق الفيديو',
            'Timeline' => 'الجدول الزمني',
            'Post Lists' => 'قوائم المنشورات',
            'Pricing' => 'الأسعار',
            'Banners' => 'اللافتات',
            'Newsletters' => 'النشرات البريدية',
            'Headers' => 'الترويسات',
            'Team' => 'الفريق',
            'Features' => 'الميزات',
            'Call to Actions' => 'دعوات لاتخاذ إجراء',
            'Footers' => 'التذييلات',
            'Heros and Titles' => 'الأبطال والعناوين',
            'Contact' => 'اتصل بنا',
            'Stats' => 'الإحصائيات',
            'FAQ' => 'الأسئلة الشائعة',
            'Logo Clouds' => 'سحابة الشعارات',
            'Icon Boxes' => 'صناديق الأيقونات',
            'Testimonials' => 'الشهادات',
            'UI Kit' => 'طقم واجهة المستخدم',
            'Fancy Sections' => 'أقسام أنيقة',
            'Element Demos' => 'عروض العناصر',
            'Masks' => 'أقنعة',
            'Animated Sections' => 'أقسام متحركة',
            'Creative Sections' => 'أقسام إبداعية',
            '3D Sections' => 'أقسام ثلاثية الأبعاد',
            'Animated Text' => 'نص متحرك',
            'Animated Background' => 'خلفية متحركة',
            'Animated Backgrounds' => 'خلفيات متحركة',
            'Advanced Sliders' => 'أشرطة تمرير متقدمة',
            'CTA' => 'CTA',
        ],
    ];
}

/**
 * Known Design Library provider labels keyed by locale.
 *
 * @return array<string, array<string, string>>
 */
function breakdance_languages_design_library_provider_name_maps(): array
{
    return [
        'pt_BR' => [
            'This Website' => 'Este site',
            'Fancy Sections' => 'Seções elegantes',
            'Element Demos' => 'Demonstrações de elementos',
        ],
        'pt_PT' => [
            'This Website' => 'Este site',
            'Fancy Sections' => 'Secções elegantes',
            'Element Demos' => 'Demonstrações de elementos',
        ],
        'fr_FR' => [
            'This Website' => 'Ce site',
            'Fancy Sections' => 'Sections élégantes',
            'Element Demos' => 'Démonstrations d’éléments',
        ],
        'de_DE' => [
            'This Website' => 'Diese Website',
            'Fancy Sections' => 'Elegante Abschnitte',
            'Element Demos' => 'Element-Demos',
        ],
        'es_ES' => [
            'This Website' => 'Este sitio web',
            'Fancy Sections' => 'Secciones elegantes',
            'Element Demos' => 'Demostraciones de elementos',
        ],
        'it_IT' => [
            'This Website' => 'Questo sito',
            'Fancy Sections' => 'Sezioni eleganti',
            'Element Demos' => 'Demo degli elementi',
        ],
        'ja_JP' => [
            'This Website' => 'このサイト',
        ],
        'ar' => [
            'This Website' => 'هذا الموقع',
            'Fancy Sections' => 'أقسام أنيقة',
            'Element Demos' => 'عروض العناصر',
        ],
        'es_LA' => [
            'This Website' => 'Este sitio web',
            'Fancy Sections' => 'Secciones elegantes',
            'Element Demos' => 'Demos de elementos',
        ],
    ];
}

/**
 * Resolve the locale used for Design Library provider labels.
 */
function breakdance_languages_resolve_design_library_locale(): ?string
{
    if (!breakdance_languages_can_apply_translations()) {
        return null;
    }

    if (function_exists('breakdance_languages_resolve_editor_locale')) {
        $editorLocale = breakdance_languages_resolve_editor_locale();

        if ($editorLocale !== null) {
            return $editorLocale;
        }
    }

    $locale = breakdance_languages_resolve_locale();

    if ($locale === null) {
        return null;
    }

    $maps = breakdance_languages_design_library_provider_name_maps();
    $uiMaps = breakdance_languages_design_library_ui_maps();

    if (array_key_exists($locale, $maps) || array_key_exists($locale, $uiMaps)) {
        return $locale;
    }

    return null;
}

/**
 * @return array<string, string>
 */
function breakdance_languages_get_design_library_provider_name_map(?string $locale = null): array
{
    $locale = $locale ?: breakdance_languages_resolve_design_library_locale();

    if ($locale === null) {
        return [];
    }

    $maps = breakdance_languages_design_library_provider_name_maps();

    return $maps[$locale] ?? [];
}

/**
 * Flatten UI maps with optional lowercase variants for CSS-transformed labels.
 *
 * @return array<string, string>
 */
function breakdance_languages_get_design_library_ui_dictionary(?string $locale = null, bool $expandLowercase = true): array
{
    $locale = $locale ?: breakdance_languages_resolve_design_library_locale();

    if ($locale === null) {
        return [];
    }

    $maps = breakdance_languages_design_library_ui_maps();
    $dictionary = $maps[$locale] ?? [];

    if (!$expandLowercase) {
        return $dictionary;
    }

    $expanded = [];

    foreach ($dictionary as $source => $target) {
        $expanded[$source] = $target;
        $expanded[strtolower($source)] = $target;
    }

    return $expanded;
}

/**
 * Resolve a visible label translation without touching URLs or asset paths.
 */
function breakdance_languages_translate_design_library_label(string $label, ?string $locale = null): ?string
{
    $dictionary = breakdance_languages_get_design_library_ui_dictionary($locale, false);
    $trimmed = trim($label);

    if ($trimmed === '') {
        return null;
    }

    if (isset($dictionary[$trimmed])) {
        return $dictionary[$trimmed];
    }

    $lowerDictionary = breakdance_languages_get_design_library_ui_dictionary($locale, true);
    $lower = strtolower($trimmed);

    if (isset($lowerDictionary[$lower])) {
        return $lowerDictionary[$lower];
    }

    // Fancy Sections set titles often keep a series suffix: "Masks #2", "Animated Text #3".
    if (preg_match('/^(.*?)(\s+#\d+)$/u', $trimmed, $matches) === 1) {
        $base = trim($matches[1]);
        $suffix = $matches[2];
        $translatedBase = breakdance_languages_translate_design_library_label($base, $locale);

        if ($translatedBase !== null) {
            return $translatedBase . $suffix;
        }
    }

    // Truncated titles with ellipsis: "Animated Background..."
    if (preg_match('/^(.*?)(\.\.\.|…)$/u', $trimmed, $matches) === 1) {
        $base = rtrim($matches[1]);
        $ellipsis = $matches[2];
        $translatedBase = breakdance_languages_translate_design_library_label($base, $locale);

        if ($translatedBase !== null) {
            return $translatedBase . $ellipsis;
        }
    }

    return null;
}

/**
 * Translate hardcoded Design Library provider names.
 *
 * @param array<int, array<string, mixed>> $providers
 * @return array<int, array<string, mixed>>
 */
function breakdance_languages_translate_design_library_providers(array $providers): array
{
    $map = array_merge(
        breakdance_languages_get_design_library_provider_name_map(),
        breakdance_languages_get_design_library_ui_dictionary()
    );

    if ($map === []) {
        return $providers;
    }

    foreach ($providers as &$provider) {
        if (!is_array($provider)) {
            continue;
        }

        if (!isset($provider['name']) || !is_string($provider['name'])) {
            continue;
        }

        if (isset($map[$provider['name']])) {
            $provider['name'] = $map[$provider['name']];
        }
    }

    unset($provider);

    return $providers;
}

/**
 * Replace design library strings inside remote HTML.
 */
function breakdance_languages_translate_design_library_html(string $html, ?string $locale = null): string
{
    return breakdance_languages_translate_design_library_html_dom($html, $locale);
}

/**
 * Translate visible text nodes in proxied HTML without mutating asset URLs.
 */
function breakdance_languages_translate_design_library_html_dom(string $html, ?string $locale = null): string
{
    if (breakdance_languages_get_design_library_ui_dictionary($locale, false) === []) {
        return $html;
    }

    if (!class_exists(DOMDocument::class)) {
        return $html;
    }

    $internalErrors = libxml_use_internal_errors(true);
    $document = new DOMDocument();
    $loaded = $document->loadHTML(
        '<?xml encoding="utf-8" ?>' . $html,
        LIBXML_NOWARNING | LIBXML_NOERROR
    );
    libxml_clear_errors();
    libxml_use_internal_errors($internalErrors);

    if (!$loaded) {
        return $html;
    }

    $xpath = new DOMXPath($document);
    $textNodes = $xpath->query('//text()[not(ancestor::script) and not(ancestor::style)]');

    if ($textNodes !== false) {
        foreach ($textNodes as $textNode) {
            if (!$textNode instanceof DOMText) {
                continue;
            }

            $value = $textNode->nodeValue ?? '';
            $trimmed = trim($value);

            if ($trimmed === '') {
                continue;
            }

            $replacement = breakdance_languages_translate_design_library_label($trimmed, $locale);

            if ($replacement !== null) {
                $textNode->nodeValue = str_replace($trimmed, $replacement, $value);
            }
        }
    }

    $ariaNodes = $xpath->query('//*[@aria-label]');

    if ($ariaNodes !== false) {
        foreach ($ariaNodes as $node) {
            if (!$node instanceof DOMElement) {
                continue;
            }

            $label = $node->getAttribute('aria-label');
            $replacement = breakdance_languages_translate_design_library_label($label, $locale);

            if ($replacement !== null) {
                $node->setAttribute('aria-label', $replacement);
            }
        }
    }

    $translated = $document->saveHTML();

    return is_string($translated) && $translated !== '' ? $translated : $html;
}

/**
 * @return array{addToPage: string, copyToClipboard: string}
 */
function breakdance_languages_get_design_library_script_labels(?string $locale = null): array
{
    $dictionary = breakdance_languages_get_design_library_ui_dictionary($locale);

    return [
        'addToPage' => $dictionary['Add to page'] ?? 'Add to page',
        'copyToClipboard' => $dictionary['Copy to clipboard'] ?? 'Copy to clipboard',
    ];
}

/**
 * Swap Breakdance's hardcoded design-library.js for a localized build.
 */
function breakdance_languages_override_design_library_script(): void
{
    if (!function_exists('Breakdance\DesignLibrary\isDesignLibraryEnabledForCurrentRequest')) {
        return;
    }

    if (!\Breakdance\DesignLibrary\isDesignLibraryEnabledForCurrentRequest()) {
        return;
    }

    if (!breakdance_languages_resolve_design_library_locale()) {
        return;
    }

    if (!wp_script_is('breakdance-design-library', 'registered')) {
        return;
    }

    $handle = 'breakdance-languages-design-library';
    $src = plugins_url('assets/design-library.js', BREAKDANCE_LANGUAGES_FILE);

    wp_dequeue_script('breakdance-design-library');
    wp_deregister_script('breakdance-design-library');

    wp_register_script(
        $handle,
        $src,
        [],
        BREAKDANCE_LANGUAGES_VERSION,
        true
    );

    wp_localize_script(
        $handle,
        'breakdanceLanguagesDesignLibraryLabels',
        breakdance_languages_get_design_library_script_labels()
    );

    wp_enqueue_script($handle);
}

/**
 * Normalize a design library URL against a base document URL.
 */
function breakdance_languages_normalize_design_library_url(string $url, string $baseUrl): ?string
{
    $parts = wp_parse_url($url);

    if ($parts === false) {
        return null;
    }

    if (!isset($parts['host'])) {
        $base = wp_parse_url($baseUrl);

        if ($base === false || !isset($base['scheme'], $base['host'])) {
            return null;
        }

        $path = $url;

        if ($path === '' || $path[0] !== '/') {
            $basePath = $base['path'] ?? '/';
            $directory = rtrim(str_replace('\\', '/', dirname($basePath)), '/');
            $path = ($directory === '' ? '' : $directory) . '/' . ltrim($path, '/');
        }

        $url = $base['scheme'] . '://' . $base['host'] . $path;
        $parts = wp_parse_url($url);

        if ($parts === false || !isset($parts['host'])) {
            return null;
        }
    }

    $host = strtolower($parts['host']);

    if (!in_array($host, breakdance_languages_design_library_allowed_hosts(), true)) {
        return null;
    }

    $scheme = strtolower((string) ($parts['scheme'] ?? 'https'));

    if ($scheme !== 'https') {
        return null;
    }

    $path = $parts['path'] ?? '/';
    $query = isset($parts['query']) ? '?' . $parts['query'] : '';

    return 'https://' . $host . $path . $query;
}

/**
 * Rewrite internal navigation links so browsing stays inside the local proxy.
 *
 * Only <a href> attributes are rewritten. Stylesheet/preload links must keep
 * pointing at the remote design library host or the iframe loses all CSS.
 */
function breakdance_languages_rewrite_design_library_links(string $html, string $baseUrl, string $proxyEndpoint): string
{
    return (string) preg_replace_callback(
        '/<a\b([^>]*?)\shref=(["\'])([^"\']+)\2/i',
        static function (array $matches) use ($baseUrl, $proxyEndpoint): string {
            $beforeHref = $matches[1];
            $quote = $matches[2];
            $href = html_entity_decode($matches[3], ENT_QUOTES);

            if ($href === '' || $href[0] === '#' || strpos($href, 'mailto:') === 0 || strpos($href, 'tel:') === 0) {
                return $matches[0];
            }

            $absolute = breakdance_languages_normalize_design_library_url($href, $baseUrl);

            if ($absolute === null) {
                return $matches[0];
            }

            $proxied = add_query_arg(
                [
                    'action' => 'breakdance_languages_design_library_proxy',
                    'nonce' => wp_create_nonce('breakdance_languages_design_library_proxy'),
                    'url' => $absolute,
                ],
                $proxyEndpoint
            );

            return '<a' . $beforeHref . ' href=' . $quote . esc_url($proxied) . $quote;
        },
        $html
    );
}

/**
 * Ensure relative asset URLs resolve against the remote design library origin.
 */
function breakdance_languages_inject_design_library_base_tag(string $html, string $documentUrl): string
{
    $parts = wp_parse_url($documentUrl);

    if ($parts === false || !isset($parts['scheme'], $parts['host'])) {
        return $html;
    }

    $path = $parts['path'] ?? '/';

    if (substr($path, -1) !== '/') {
        $path = rtrim(str_replace('\\', '/', dirname($path)), '/') . '/';
    }

    $baseHref = esc_url($parts['scheme'] . '://' . $parts['host'] . $path);
    $baseTag = '<base href="' . $baseHref . '">';

    if (stripos($html, '<base ') !== false) {
        return (string) preg_replace('/<base\b[^>]*>/i', $baseTag, $html, 1);
    }

    if (preg_match('/<head\b[^>]*>/i', $html)) {
        return (string) preg_replace('/<head\b[^>]*>/i', '$0' . $baseTag, $html, 1);
    }

    return $baseTag . $html;
}

/**
 * Apply dictionary replacements only outside script/style blocks.
 */
function breakdance_languages_translate_design_library_html_safe(string $html, ?string $locale = null): string
{
    return breakdance_languages_translate_design_library_html_dom($html, $locale);
}

/**
 * Swap the remote Breakdance design-library.js for the localized plugin build.
 */
function breakdance_languages_replace_proxied_design_library_script(string $html, ?string $locale = null): string
{
    $labels = breakdance_languages_get_design_library_script_labels($locale);
    $labelsJson = wp_json_encode($labels);

    if (!is_string($labelsJson)) {
        return $html;
    }

    $localScript = esc_url(
        add_query_arg(
            'ver',
            BREAKDANCE_LANGUAGES_VERSION,
            plugins_url('assets/design-library.js', BREAKDANCE_LANGUAGES_FILE)
        )
    );
    $labelsScript = '<script>window.breakdanceLanguagesDesignLibraryLabels=' . $labelsJson . ';</script>';
    $replacement = $labelsScript . '<script src="' . $localScript . '" id="breakdance-design-library-js"></script>';

    $replaced = preg_replace(
        '/<script\b[^>]*\bsrc=["\'][^"\']*\/design-library\.js[^"\']*["\'][^>]*>\s*<\/script>/i',
        $replacement,
        $html,
        1,
        $count
    );

    return ($count > 0 && is_string($replaced)) ? $replaced : $html;
}

/**
 * Inject a DOM walker for dynamically created buttons inside proxied pages.
 *
 * @param array<string, string> $dictionary
 */
function breakdance_languages_build_design_library_inline_translation_script(array $dictionary): string
{
    $json = wp_json_encode($dictionary);

    if (!is_string($json)) {
        return '';
    }

    return '<script>(function(){var dictionary=' . $json . ';function lookup(text){var normalized=(text||"").replace(/^[\s\u2022\u00b7•·\-]+/,"").trim();if(Object.prototype.hasOwnProperty.call(dictionary,normalized)){return dictionary[normalized];}if(Object.prototype.hasOwnProperty.call(dictionary,text)){return dictionary[text];}return null;}function translateNode(node){if(!node||node.nodeType!==3){return;}var raw=node.nodeValue||"";var trimmed=raw.trim();if(!trimmed){return;}var replacement=lookup(trimmed);if(!replacement){return;}node.nodeValue=raw.replace(trimmed,replacement);}function translateButtons(root){(root||document).querySelectorAll(".bd-copy-button").forEach(function(button){if(button.firstChild&&button.firstChild.nodeType===3){translateNode(button.firstChild);}});}function scan(root){if(!root){return;}translateButtons(root);var walker=document.createTreeWalker(root,4);var current;while((current=walker.nextNode())){translateNode(current);}}function boot(){scan(document.body);new MutationObserver(function(mutations){mutations.forEach(function(mutation){if(mutation.type==="characterData"){translateNode(mutation.target);return;}mutation.addedNodes.forEach(function(node){if(node.nodeType===3){translateNode(node);}else if(node.querySelectorAll){scan(node);}if(node.classList&&node.classList.contains("bd-copy-button")){translateButtons(node);}});});}).observe(document.body,{childList:true,subtree:true,characterData:true});}if(document.readyState==="loading"){document.addEventListener("DOMContentLoaded",boot);}else{boot();}})();</script>';
}

/**
 * Strip remote scripts and inline event handlers from proxied Design Library HTML.
 */
function breakdance_languages_sanitize_design_library_html(string $html): string
{
    $html = (string) preg_replace('#<script\b[^>]*>.*?</script>#is', '', $html);
    $html = (string) preg_replace('/\son[a-z]+\s*=\s*(["\']).*?\1/iu', '', $html);
    $html = (string) preg_replace('/\son[a-z]+\s*=\s*[^\s>]+/iu', '', $html);

    return $html;
}

/**
 * Local Design Library bootstrap scripts (labels + plugin JS).
 */
function breakdance_languages_build_design_library_local_scripts(?string $locale = null): string
{
    $labels = breakdance_languages_get_design_library_script_labels($locale);
    $labelsJson = wp_json_encode($labels);

    if (!is_string($labelsJson)) {
        $labelsJson = '{}';
    }

    $localScript = esc_url(
        add_query_arg(
            'ver',
            BREAKDANCE_LANGUAGES_VERSION,
            plugins_url('assets/design-library.js', BREAKDANCE_LANGUAGES_FILE)
        )
    );

    return '<script>window.breakdanceLanguagesDesignLibraryLabels=' . $labelsJson . ';</script>'
        . '<script src="' . $localScript . '" id="breakdance-design-library-js"></script>';
}

/**
 * Proxy remote design library HTML through the local site for translation.
 */
function breakdance_languages_handle_design_library_proxy(): void
{
    if (!is_user_logged_in() || !current_user_can('edit_posts')) {
        wp_die(esc_html__('Unauthorized', 'breakdance-languages'), '', ['response' => 403]);
    }

    check_ajax_referer('breakdance_languages_design_library_proxy', 'nonce');

    $locale = breakdance_languages_resolve_design_library_locale();

    if ($locale === null) {
        wp_die(esc_html__('Translations are not enabled.', 'breakdance-languages'), '', ['response' => 403]);
    }

    $rawUrl = isset($_GET['url']) ? wp_unslash((string) $_GET['url']) : '';

    if ($rawUrl === '') {
        wp_die(esc_html__('Missing URL.', 'breakdance-languages'), '', ['response' => 400]);
    }

    $url = breakdance_languages_normalize_design_library_url($rawUrl, $rawUrl);

    if ($url === null) {
        wp_die(esc_html__('URL not allowed.', 'breakdance-languages'), '', ['response' => 400]);
    }

    $response = wp_remote_get(
        $url,
        [
            'timeout' => 20,
            'redirection' => 3,
            'headers' => [
                'Accept' => 'text/html',
            ],
        ]
    );

    if (is_wp_error($response)) {
        wp_die(esc_html($response->get_error_message()), '', ['response' => 502]);
    }

    $status = (int) wp_remote_retrieve_response_code($response);

    if ($status < 200 || $status >= 400) {
        wp_die(esc_html__('Could not load the design library page.', 'breakdance-languages'), '', ['response' => 502]);
    }

    $html = (string) wp_remote_retrieve_body($response);
    $html = breakdance_languages_sanitize_design_library_html($html);
    $html = breakdance_languages_inject_design_library_base_tag($html, $url);
    $html = breakdance_languages_translate_design_library_html_safe($html, $locale);
    $html = breakdance_languages_rewrite_design_library_links($html, $url, admin_url('admin-ajax.php'));

    $inject = breakdance_languages_build_design_library_local_scripts($locale)
        . breakdance_languages_build_design_library_inline_translation_script(
            breakdance_languages_get_design_library_ui_dictionary($locale)
        );

    if (stripos($html, '</body>') !== false) {
        $html = str_ireplace('</body>', $inject . '</body>', $html);
    } else {
        $html .= $inject;
    }

    header('Content-Type: text/html; charset=UTF-8');
    header('X-Robots-Tag: noindex');
    header(
        "Content-Security-Policy: default-src 'none'; "
        . "base-uri 'self'; "
        . "img-src https: data:; "
        . "style-src https: 'unsafe-inline'; "
        . "font-src https: data:; "
        . "script-src 'self' 'unsafe-inline'; "
        . "connect-src 'self'; "
        . "frame-ancestors 'self'; "
        . "form-action 'none'"
    );

    echo $html;
    exit;
}

/**
 * Builder bridge config for proxying remote design library iframes.
 *
 * @return array{proxyBase: string, nonce: string}|null
 */
function breakdance_languages_get_design_library_iframe_bridge_config(): ?array
{
    if (!breakdance_languages_is_builder_page()) {
        return null;
    }

    if (breakdance_languages_resolve_design_library_locale() === null) {
        return null;
    }

    return [
        'proxyBase' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('breakdance_languages_design_library_proxy'),
        'allowedHosts' => breakdance_languages_design_library_allowed_hosts(),
    ];
}
