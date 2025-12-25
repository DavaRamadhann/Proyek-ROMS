import './bootstrap';
import './chat';
import { createIcons, icons } from 'lucide';

// Export ke window agar bisa diakses dari Blade
// Wrap createIcons to provide default icons if not specified
const customCreateIcons = (options = {}) => {
    if (!options.icons) {
        options.icons = icons;
    }
    return createIcons(options);
};

window.lucide = { createIcons: customCreateIcons, icons };

// Auto-initialize setelah DOM loaded
document.addEventListener('DOMContentLoaded', () => {
    if (window.lucide && window.lucide.createIcons) {
        window.lucide.createIcons();
    }
});