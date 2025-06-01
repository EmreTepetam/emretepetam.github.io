document.addEventListener('DOMContentLoaded', function() {
    // Hamburger menü
    const hamburger = document.querySelector('.hamburger');
    const navMenu = document.querySelector('.nav-menu');

    hamburger.addEventListener('click', function() {
        navMenu.classList.toggle('active');
    });

    // Form doğrulama
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const inputs = form.querySelectorAll('input[required], textarea[required]');
            let valid = true;
            inputs.forEach(input => {
                if (!input.value.trim()) {
                    valid = false;
                    input.style.border = '1px solid #E74C3C';
                } else {
                    input.style.border = '';
                }
            });
            if (!valid) {
                e.preventDefault();
                alert('Lütfen tüm zorunlu alanları doldurun.');
            }
        });
    });

    // Animasyonlu scroll efekti
    const sections = document.querySelectorAll('.content-section, .blog-card, .gallery-item, .faq-item');
    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate');
                entry.target.querySelectorAll('.animate-stagger').forEach((el, index) => {
                    el.style.animationDelay = `${(index + 1) * 0.1}s`;
                });
            }
        });
    }, { threshold: 0.1 });

    sections.forEach(section => observer.observe(section));

    // Pop-up mesaj için otomatik kapanma
    const success = document.querySelector('.success');
    if (success) {
        setTimeout(() => {
            success.style.display = 'none';
        }, 3000);
    }

    // Scroll-down tıklama ile yumuşak scroll
    const scrollDown = document.querySelector('#scroll-down');
    if (scrollDown) {
        scrollDown.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector('#first-section');
            if (target) {
                target.scrollIntoView({ behavior: 'smooth' });
            }
        });
    }

    // Hero solma efekti (scroll ile)
    const hero = document.querySelector('.hero');
    if (hero) {
        window.addEventListener('scroll', function() {
            const scrollPosition = window.scrollY;
            const heroHeight = hero.offsetHeight;
            hero.style.opacity = 1 - scrollPosition / (heroHeight * 0.7);
        });
    }

    // Gallery drag-scroll ve manuel kaydırma
    const galleries = document.querySelectorAll('.gallery-grid');
    galleries.forEach(gallery => {
        let isDragging = false;
        let startX;
        let scrollLeft;

        // Sürüklemeyi başlat
        gallery.addEventListener('mousedown', (e) => {
            isDragging = true;
            startX = e.pageX - gallery.offsetLeft;
            scrollLeft = gallery.scrollLeft;
            e.preventDefault(); // Varsayılan davranışı engelle
        });

        gallery.addEventListener('mouseleave', () => {
            isDragging = false;
        });

        gallery.addEventListener('mouseup', () => {
            isDragging = false;
        });

        gallery.addEventListener('mousemove', (e) => {
            if (!isDragging) return;
            e.preventDefault(); // Metin seçimi ve diğer varsayılan davranışları engelle
            const x = e.pageX - gallery.offsetLeft;
            const walk = (x - startX) * 2; // Sürükleme hızı
            gallery.scrollLeft = scrollLeft - walk;
        });

        // Görseller için dragstart olayını engelle
        const mediaElements = gallery.querySelectorAll('img, video');
        mediaElements.forEach(element => {
            element.addEventListener('dragstart', (e) => {
                e.preventDefault();
            });
        });

        // Touch desteği (mobil için)
        gallery.addEventListener('touchstart', (e) => {
            isDragging = true;
            startX = e.touches[0].pageX - gallery.offsetLeft;
            scrollLeft = gallery.scrollLeft;
            e.preventDefault();
        });

        gallery.addEventListener('touchend', () => {
            isDragging = false;
        });

        gallery.addEventListener('touchmove', (e) => {
            if (!isDragging) return;
            const x = e.touches[0].pageX - gallery.offsetLeft;
            const walk = (x - startX) * 2;
            gallery.scrollLeft = scrollLeft - walk;
        });
    });

    // Sol ve sağ butonlarla manuel kaydırma
    document.querySelectorAll('.gallery-controls').forEach(control => {
        const gallery = control.querySelector('.gallery-grid');
        const prev = control.querySelector('.gallery-prev');
        const next = control.querySelector('.gallery-next');

        prev.addEventListener('click', () => {
            gallery.scrollLeft -= 300; // Bir görsel genişliği kadar sola kaydır
        });

        next.addEventListener('click', () => {
            gallery.scrollLeft += 300; // Bir görsel genişliği kadar sağa kaydır
        });
    });
});