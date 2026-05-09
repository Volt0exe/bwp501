/* ==========================================================
   main.js - دليل فعاليات الجامعة الافتراضية
   ========================================================== */

document.addEventListener('DOMContentLoaded', () => {

    /* ── Dark Mode ── */
    const darkToggle = document.getElementById('darkToggle');
    const html = document.documentElement;

    const applyTheme = (theme) => {
        html.setAttribute('data-theme', theme);
        localStorage.setItem('theme', theme);
        if (darkToggle) {
            darkToggle.innerHTML = theme === 'dark'
                ? '<i class="bi bi-sun-fill"></i> فاتح'
                : '<i class="bi bi-moon-fill"></i> داكن';
        }
    };

    const savedTheme = localStorage.getItem('theme') || 'light';
    applyTheme(savedTheme);

    if (darkToggle) {
        darkToggle.addEventListener('click', () => {
            const current = html.getAttribute('data-theme');
            applyTheme(current === 'dark' ? 'light' : 'dark');
        });
    }

    /* ── Scroll to Top ── */
    const scrollBtn = document.getElementById('scrollTop');
    if (scrollBtn) {
        window.addEventListener('scroll', () => {
            scrollBtn.classList.toggle('visible', window.scrollY > 300);
        });
        scrollBtn.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    /* ── Category Filter on Events Page ── */
    const catBtns  = document.querySelectorAll('.cat-btn');
    const eventCards = document.querySelectorAll('.event-item');

    catBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            catBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            const cat = btn.dataset.cat;
            let visible = 0;
            eventCards.forEach(card => {
                const match = cat === 'all' || card.dataset.category === cat;
                card.style.display = match ? '' : 'none';
                if (match) visible++;
            });
            const noEvt = document.getElementById('noEvents');
            if (noEvt) noEvt.style.display = visible === 0 ? 'block' : 'none';
        });
    });

    /* ── Live Search Filter ── */
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            const q = searchInput.value.trim().toLowerCase();
            let visible = 0;
            eventCards.forEach(card => {
                const title = card.dataset.title?.toLowerCase() || '';
                const match = title.includes(q);
                card.style.display = match ? '' : 'none';
                if (match) visible++;
            });
            const noEvt = document.getElementById('noEvents');
            if (noEvt) noEvt.style.display = visible === 0 ? 'block' : 'none';
        });
    }

    /* ── Contact Form Validation ── */
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const name    = document.getElementById('cName');
            const email   = document.getElementById('cEmail');
            const message = document.getElementById('cMessage');
            const alertBox = document.getElementById('formAlert');
            let valid = true;

            // Reset
            [name, email, message].forEach(f => {
                f.classList.remove('is-invalid', 'is-valid');
            });

            // Validate Name
            if (!name.value.trim() || name.value.trim().length < 3) {
                name.classList.add('is-invalid');
                valid = false;
            } else {
                name.classList.add('is-valid');
            }

            // Validate Email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email.value.trim())) {
                email.classList.add('is-invalid');
                valid = false;
            } else {
                email.classList.add('is-valid');
            }

            // Validate Message
            if (!message.value.trim() || message.value.trim().length < 10) {
                message.classList.add('is-invalid');
                valid = false;
            } else {
                message.classList.add('is-valid');
            }

            if (valid) {
                alertBox.className = 'alert alert-success mt-3';
                alertBox.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i>تم إرسال رسالتك بنجاح! سنتواصل معك قريباً.';
                alertBox.style.display = 'block';
                contactForm.reset();
                [name, email, message].forEach(f => f.classList.remove('is-valid'));
                setTimeout(() => { alertBox.style.display = 'none'; }, 5000);
            } else {
                alertBox.className = 'alert alert-danger mt-3';
                alertBox.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-2"></i>يرجى تصحيح الأخطاء في النموذج.';
                alertBox.style.display = 'block';
            }
        });
    }

    /* ── Add to Calendar ── */
    const addCalBtn = document.getElementById('addCalendar');
    if (addCalBtn) {
        addCalBtn.addEventListener('click', () => {
            const title    = addCalBtn.dataset.title   || '';
            const date     = addCalBtn.dataset.date    || '';
            const location = addCalBtn.dataset.location || '';
            const dt = date.replace(/-/g, '');
            const gcUrl = `https://calendar.google.com/calendar/r/eventedit?text=${encodeURIComponent(title)}&dates=${dt}/${dt}&location=${encodeURIComponent(location)}`;
            window.open(gcUrl, '_blank');
        });
    }

    /* ── Share Event ── */
    const shareBtn = document.getElementById('shareBtn');
    if (shareBtn) {
        shareBtn.addEventListener('click', async () => {
            if (navigator.share) {
                await navigator.share({ title: document.title, url: window.location.href });
            } else {
                navigator.clipboard.writeText(window.location.href).then(() => {
                    shareBtn.innerHTML = '<i class="bi bi-check-lg me-1"></i>تم النسخ!';
                    setTimeout(() => {
                        shareBtn.innerHTML = '<i class="bi bi-share me-1"></i>شارك';
                    }, 2500);
                });
            }
        });
    }

    /* ── Date Filter ── */
    const dateFilter = document.getElementById('dateFilter');
    if (dateFilter) {
        dateFilter.addEventListener('change', () => {
            const sel = dateFilter.value;
            const now = new Date();
            eventCards.forEach(card => {
                const d = new Date(card.dataset.date);
                let show = true;
                if (sel === 'upcoming')  show = d >= now;
                if (sel === 'past')      show = d < now;
                if (sel === 'thisweek') {
                    const diff = Math.ceil((d - now) / (1000*60*60*24));
                    show = diff >= 0 && diff <= 7;
                }
                if (sel === 'thismonth') {
                    show = d.getMonth() === now.getMonth() && d.getFullYear() === now.getFullYear();
                }
                card.style.display = show ? '' : 'none';
            });
        });
    }

    /* ── Navbar active link ── */
    const page = window.location.pathname.split('/').pop();
    document.querySelectorAll('.navbar-nav .nav-link').forEach(link => {
        if (link.getAttribute('href') === page) link.classList.add('active');
    });
});
