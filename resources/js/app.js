// Tells the page layout that this script started, so scroll-reveal content may stay
// hidden until it enters the viewport. If this file never runs, the layout reveals
// everything again after a few seconds.
window.__eclipzReady = true;

// Livewire is injected on every page (it ships with the admin panel) and brings its
// own copy of Alpine. Starting a second copy makes every @click run twice, which would
// open and instantly close the mobile menu. So Alpine is started here only when
// Livewire has not already started its own.
function startOwnAlpine() {
    import('alpinejs').then(({ default: Alpine }) => {
        window.Alpine = Alpine;
        Alpine.start();
    });
}

document.addEventListener('DOMContentLoaded', () => {
    // Livewire starts Alpine on DOMContentLoaded. Wait until its listeners have run.
    setTimeout(() => {
        if (!window.Livewire && !window.Alpine) {
            startOwnAlpine();
        }
    }, 0);

    const revealTargets = document.querySelectorAll('[data-reveal]');

    if (!('IntersectionObserver' in window) || revealTargets.length === 0) {
        revealTargets.forEach((el) => el.classList.add('is-visible'));
        return;
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.15 });

    revealTargets.forEach((el) => observer.observe(el));
});