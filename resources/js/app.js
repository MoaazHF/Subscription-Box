const resolveTheme = (savedTheme, prefersDark) => {
    if (savedTheme === 'light') {
        return 'light';
    }

    if (savedTheme === 'dark') {
        return 'dark';
    }

    return prefersDark ? 'dark' : 'light';
};

const applyTheme = (root, themeMode) => {
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const resolvedTheme = resolveTheme(themeMode, prefersDark);

    root.classList.toggle('dark', resolvedTheme === 'dark');
    root.setAttribute('data-theme', themeMode);

    const themeColor = resolvedTheme === 'dark' ? '#0f172a' : '#ff385c';
    const themeColorMeta = document.querySelector('meta[name="theme-color"]');

    if (themeColorMeta) {
        themeColorMeta.setAttribute('content', themeColor);
    }
};

const syncThemeToggleButtons = (themeMode) => {
    const toggleButtons = document.querySelectorAll('[data-theme-toggle]');

    toggleButtons.forEach((button) => {
        const isActive = button.getAttribute('data-theme-toggle') === themeMode;
        button.classList.toggle('theme-toggle-btn-active', isActive);
        button.setAttribute('aria-pressed', isActive ? 'true' : 'false');
    });
};

window.addEventListener('DOMContentLoaded', () => {
    const root = document.documentElement;
    const savedTheme = localStorage.getItem('theme');
    const initialThemeMode = savedTheme === 'light' || savedTheme === 'dark' ? savedTheme : 'system';

    applyTheme(root, initialThemeMode);
    syncThemeToggleButtons(initialThemeMode);

    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const selectedTheme = button.getAttribute('data-theme-toggle');

            if (selectedTheme === 'system') {
                localStorage.removeItem('theme');
            } else {
                localStorage.setItem('theme', selectedTheme);
            }

            applyTheme(root, selectedTheme);
            syncThemeToggleButtons(selectedTheme);

            if (window.lucide) {
                window.lucide.createIcons();
            }
        });
    });

    const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
    mediaQuery.addEventListener('change', () => {
        if (localStorage.getItem('theme')) {
            return;
        }

        applyTheme(root, 'system');
        syncThemeToggleButtons('system');
    });
});
