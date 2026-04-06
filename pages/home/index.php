<?php
require_once __DIR__ . '/../../functions/auth.php';
require_once __DIR__ . '../../../helper/route.php';
require_once __DIR__ . '../../../functions/homeImages.php';
require_once __DIR__ . '../../../functions/homeDesc.php';
require_once __DIR__ . '../../../functions/about.php';
require_once __DIR__ . '../../../functions/services.php';
require_once __DIR__ . '../../../functions/services.php';
require_once __DIR__ . '../../../functions/client.php';
require_once __DIR__ . '../../../functions/contact.php';
checkAuth();

$heroImages     = getHomeSliders();
$heroContent    = getHomeContent();
$aboutData      = getAbout(); 
$services       = getServices();
$clientData      = getClients(); 
$contactData    = getContact(); 

$animations     = [
    'zoomIn', 'zoomOut', 'panLeft', 'panRight', 'zoomRotate',
    'slowZoom', 'panUp', 'panDown', 'zoomBlur', 'kenBurns'
];

$slideDuration = 5;
$totalImages = count($heroImages);
$totalDuration = $totalImages * $slideDuration;
$heroContent = array_merge(array_filter($heroContent));
$aboutData = array_merge(array_filter($aboutData, fn($v) => $v !== null));
$services = $servicesData['services'] ?? [];
$totalServices = count($services);
$contactData = array_merge(array_filter($contactData, fn($v) => $v !== null));

$cleanNumber = preg_replace('/[^0-9]/', '', $contactData['whatsapp_number']); 
$waMessage = rawurlencode("Hello ESTU, I'm interested in your Event Organizer Bali services");
?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>ESTU - Event Organizer Bali | Professional & Creative Local EO Services</title>
        <meta name="description" content="ESTU is your trusted local Bali event organizer. Professional EO Bali services for weddings, corporate events, festivals & cultural celebrations. 8+ years experience.">
        <meta name="keywords" content="event organizer bali, eo bali, local bali eo, bali event planner, wedding organizer bali, corporate event bali, eo lokal bali">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        fontFamily: {
                            sans: ['Inter', 'sans-serif'],
                            display: ['Space Grotesk', 'sans-serif'],
                        },
                        colors: {
                            primary: '#25D366',
                            dark: '#0a0a0a',
                            darker: '#050505',
                        }
                    }
                }
            }
        </script>

        <link rel="stylesheet" href="<?= asset('css/animations.css') ?>">
        <link rel="stylesheet" href="<?= asset('css/base.css') ?>">
        <link rel="stylesheet" href="<?= asset('css/components.css') ?>">
        <link rel="stylesheet" href="<?= asset('css/sections.css') ?>">
    </head>
    <body class="bg-dark text-white font-sans overflow-x-hidden">
        <div class="cursor-dot hidden md:block" aria-hidden="true"></div>
        <div class="cursor-outline hidden md:block" aria-hidden="true"></div>

        <nav class="nav-fixed" id="navbar" role="navigation" aria-label="Main navigation">
            <a href="#" class="text-2xl font-display font-bold tracking-tight z-10">ESTU</a>
            
            <div class="nav-links hidden md:flex gap-8">
                <a href="#about" class="nav-link">About</a>
                <a href="#services" class="nav-link">Services</a>
                <a href="#portfolio" class="nav-link">Portfolio</a>
                <a href="#clients" class="nav-link">Clients</a>
                <a href="#contact" class="nav-link">Contact</a>
            </div>

            <button class="mobile-menu-btn md:hidden" onclick="mobileMenu.toggle()" aria-label="Toggle menu">
                <i class="fas fa-bars"></i>
            </button>
        </nav>

        <div id="mobileMenu" class="mobile-menu" role="dialog" aria-label="Mobile menu">
            <button onclick="mobileMenu.toggle()" class="absolute top-6 right-6 text-2xl" aria-label="Close menu">
                <i class="fas fa-times"></i>
            </button>
            <nav class="flex flex-col gap-8 text-center">
                <a href="#about" onclick="mobileMenu.toggle()" class="text-3xl font-display font-bold">ABOUT</a>
                <a href="#services" onclick="mobileMenu.toggle()" class="text-3xl font-display font-bold">SERVICES</a>
                <a href="#portfolio" onclick="mobileMenu.toggle()" class="text-3xl font-display font-bold">PORTFOLIO</a>
                <a href="#clients" onclick="mobileMenu.toggle()" class="text-3xl font-display font-bold">CLIENTS</a>
                <a href="#contact" onclick="mobileMenu.toggle()" class="text-3xl font-display font-bold">CONTACT</a>
            </nav>
        </div>

       <a href="https://wa.me/<?= $cleanNumber ?>?text=<?= $waMessage ?>" 
        target="_blank" 
        rel="noopener noreferrer"
        class="wa-float wa-pulse" 
        aria-label="Chat on WhatsApp">
            <i class="fab fa-whatsapp text-2xl"></i>
        </a>
        <button class="scroll-top" id="scrollTop" onclick="scrollManager.toTop()" aria-label="Scroll to top">
            <i class="fas fa-arrow-up"></i>
        </button>

        <!-- Hero Section -->
        <section class="hero" id="home">
            <div class="hero-bg" id="hero-dynamic-bg">
                <?php if ($totalImages > 0): ?>
                    <?php foreach ($heroImages as $index => $img): 
                        $animation = $animations[$index % count($animations)];
                        $delay = $index * $slideDuration;
                        // Pastikan filepath lengkap atau tambahkan base path
                        $imagePath = (strpos($img['filepath'], '/') === 0) 
                            ? $img['filepath'] 
                            : '/estu/' . ltrim($img['filepath'], '/');
                    ?>
                        <div class="hero-slide" 
                            data-index="<?= $index ?>"
                            style="background-image: url('<?= htmlspecialchars($imagePath) ?>');
                                    animation: <?= $animation ?> <?= $totalDuration ?>s infinite;
                                    animation-delay: <?= $delay ?>s;
                                    opacity: <?= $index === 0 ? '1' : '0' ?>;">
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="hero-slide hero-slide-fallback" 
                        style="background: linear-gradient(135deg, #1a1a1a 0%, #0a0a0a 100%);
                                opacity: 1;">
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="hero-grid"></div>
            
            <div class="bali-ornament top-left"></div>
            <div class="bali-ornament bottom-right"></div>
            
                <div class="hero-content">
                <p class="text-xs tracking-[0.4em] uppercase text-white mb-8 fade-up">
                    <?= htmlspecialchars($heroContent['topLabel']) ?>
                </p>
                
                <div class="text-reveal mb-8">
                    <h1 class="text-[12vw] md:text-[10rem] font-display font-bold leading-none">
                        <span><?= htmlspecialchars($heroContent['mainTitle']) ?></span>
                    </h1>
                </div>
                
                <p class="text-white max-w-xl mx-auto mb-12 font-light leading-relaxed fade-up delay-2">
                    <span class="text-2xl font-bold block">
                        <?= htmlspecialchars($heroContent['boldSubtitle']) ?>
                    </span>
                    <span class="block text-lg mt-2">
                        <?= htmlspecialchars($heroContent['lightSubtitle']) ?>
                    </span>
                </p>
                
                <a href="#about" class="btn-primary fade-up delay-3 group">
                    <?= htmlspecialchars($heroContent['ctaText']) ?> 
                    <i class="fas fa-arrow-down transform group-hover:translate-y-1 transition-transform"></i>
                </a>
            </div>
        </section>

       <section class="section" id="about">
        <div class="about-grid">
            <div class="about-image fade-up">
                <img src="<?= htmlspecialchars($aboutData['image'] ?? 'assets/images/About Us.png') ?>" 
                     alt="ESTU Event Organizer Bali Team" 
                     loading="lazy">
            </div>
            <div class="fade-up delay-2">
                <span class="section-label">
                    <?= htmlspecialchars($aboutData['sectionLabel']) ?>
                </span>
                <h2 class="section-title">
                    <?= htmlspecialchars($aboutData['titleLine1']) ?><br>
                    <span class="text-gray-600">
                        <?= htmlspecialchars($aboutData['titleLine2']) ?>
                    </span>
                </h2>
                <div class="line-decor"></div>
                <p class="text-gray-400 font-light leading-relaxed mb-6">
                    <?= htmlspecialchars($aboutData['paragraph1']) ?>
                </p>
                <p class="text-gray-500 font-light leading-relaxed mb-10">
                    <?= htmlspecialchars($aboutData['paragraph2']) ?>
                </p>
                
                <!-- Dynamic Stats -->
                <div class="stats-grid">
                <?php foreach ($aboutData['stats'] as $stat): ?>
                    <div class="stat-item">
                        <div class="stat-number counter" 
                            data-target="<?= htmlspecialchars($stat['number'] ?? '0') ?>"
                            data-suffix="<?= htmlspecialchars($stat['suffix'] ?? '') ?>">
                            0<?= htmlspecialchars($stat['suffix'] ?? '') ?>
                        </div>
                        <div class="stat-label">
                            <?= htmlspecialchars(trim($stat['label'] ?? '')) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

<section class="section" id="services">
    <div class="services-container">
        <div class="text-center mb-16">
            <span class="section-label">Our Services</span>
            <h2 class="section-title">
                <?= htmlspecialchars($servicesData['titleLine1'] ?? 'INTEGRATED EVENT') ?><br>
                <?= htmlspecialchars($servicesData['titleLine2'] ?? 'SOLUTIONS') ?>
            </h2>
            <p class="text-gray-400 mt-6 max-w-2xl mx-auto font-light">
                <?= htmlspecialchars($servicesData['sectionDesc'] ?? 'End-to-end event organizer Bali services...') ?>
            </p>
        </div>
        
        <div class="services-grid" data-count="<?= $totalServices ?>">
            <?php foreach ($services as $index => $service): 
                $isWide = $service['isWide'] ?? false;
                $class = $isWide ? 'service-card wide' : 'service-card';
            ?>
                <article class="<?= $class ?>">
                    <div class="service-icon">
                        <i class="fas <?= htmlspecialchars($service['icon'] ?? 'fa-star') ?>"></i>
                    </div>
                    <h3><?= htmlspecialchars($service['title']) ?></h3>
                    <p><?= htmlspecialchars($service['description']) ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

        <section class="portfolio-section" id="portfolio">
            <div class="portfolio-header">
                <span class="section-label">Portfolio</span>
                <h2 class="section-title">OUR WORK</h2>
                <button class="show-all-btn" onclick="portfolio.showAll()">
                    View All Projects <i class="fas fa-arrow-right"></i>
                </button>
            </div>

            <div class="portfolio-masonry" id="portfolioGrid">
            </div>
        </section>

        <section class="clients-section" id="clients">
            <div class="clients-header">
                <span class="section-label text-gray-500">Trusted By</span>
                <h2 class="font-display font-bold text-4xl mt-4">OUR CLIENTS</h2>
            </div>
            
            <div class="clients-logo-grid" id="clientsGrid">
            </div>
        </section>

        <section class="contact-section" id="contact">
            <div class="contact-container">
                <div class="contact-header">
                    <span class="section-label">Contact</span>
                    <h2 class="section-title"><?= htmlspecialchars($contactData['title_line']) ?></h2>
                    <p><?= htmlspecialchars($contactData['section_desc']) ?></p>
                </div>
                
                <div class="contact-grid-clean">
                    <div class="contact-info-clean">
                        <div class="contact-item-clean">
                            <span class="contact-label-clean">WhatsApp</span>
                            <span class="contact-value-clean text-lg">
                                <a href="https://wa.me/<?= $cleanNumber ?>?text=<?= $waMessage ?>" 
                                target="_blank" 
                                rel="noopener noreferrer">
                                    <i class="fab fa-whatsapp"></i>
                                    <?= htmlspecialchars($contactData['whatsapp_number']) ?>
                                </a>
                            </span>
                            <p class="contact-note"><?= htmlspecialchars($contactData['whatsapp_note']) ?></p>
                        </div>

                        <div class="contact-item-clean">
                            <span class="contact-label-clean">Email</span>
                            <span class="contact-value-clean">
                                <?= htmlspecialchars($contactData['email']) ?>
                            </span>
                        </div>

                        <div class="contact-item-clean">
                            <span class="contact-label-clean">Operating Hours</span>
                            <span class="contact-value-clean">
                                <?= strip_tags($contactData['operating_hours'], '<br><i>') ?>
                            </span>
                        </div>

                        <div class="contact-item-clean">
                            <span class="contact-label-clean">Location</span>
                            <span class="contact-value-clean">
                                <?= strip_tags($contactData['location'], '<br><i>') ?>
                            </span>
                        </div>

                        <!-- <div class="contact-cta">
                            <a href="https://wa.me/6281572000039?text=Hello%20ESTU,%20I'm%20interested%20in%20your%20Event%20Organizer%20Bali%20services" 
                            target="_blank" 
                            rel="noopener noreferrer"
                            class="btn-whatsapp-clean">
                                <i class="fab fa-whatsapp"></i>
                                Chat WhatsApp Now
                            </a>
                        </div> -->
                    </div>

                    <div class="why-choose-clean">
                        <h3>Why Choose ESTU?</h3>
                        <ul class="why-list">
                           <?php foreach ($contactData['why_choose'] as $why): ?>
                                <li>
                                    <i class="fas fa-check"></i>
                                    <span><?= htmlspecialchars($why) ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        
                        <div class="why-quote">
                            <p>
                                <?= strip_tags($contactData['why_quote'], '<br><i>') ?>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="contact-footer">
                    <div class="contact-footer-brand">
                        <span class="logo"><?= htmlspecialchars($contactData['brand_name']) ?></span>
                        <span class="tagline"><?= htmlspecialchars($contactData['brand_tagline']) ?></span>
                    </div>
                    <div class="contact-footer-copy">
                        <?= htmlspecialchars($contactData['copyright_text']) ?>
                    </div>
                </div>
            </div>
        </section>

        <script>
            // Pass PHP data ke JavaScript untuk live update
            window.initialHeroImages    = <?= json_encode($heroImages) ?>;
            window.initialHeroContent   = <?= json_encode($heroContent) ?>; 
            window.initialAboutData     = <?= json_encode($aboutData) ?>; 
            window.initialContactData   = <?= json_encode($contactData) ?>; 
            window.heroConfig = {
                slideDuration: <?= $slideDuration ?>,
                totalDuration: <?= $totalDuration ?>,
                animations: <?= json_encode($animations) ?>
            };
        </script>
        <script>
            const BASE_URL = "<?= base_url() ?>";
            const ASSET_URL = "<?= asset() ?>";
        </script>
        <script src="<?= asset('js/animations.js') ?>"></script>
        <script src="<?= asset('js/cursor.js') ?>"></script>
        <script src="<?= asset('js/main.js') ?>"></script>
        <script src="<?= asset('js/portfolio.js') ?>"></script>
    </body>
    </html>