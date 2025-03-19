// Плавный скролл до якоря
export default {
    mounted(el, binding) {
        el.addEventListener('click', (event) => {
            event.preventDefault();
            const href = el.getAttribute('href');
            if (!href || !href.startsWith('#')) return;
            const targetId = href.slice(1);
            const target = document.getElementById(targetId);
            console.log('target', target)
            if (target) {
                console.log('ok', target)
                // Можно задавать отступ через объект директивы, например, { offset: 80 }
                const offset = binding.value?.offset || 80;
                const elementPosition = target.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - offset;
                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });
            }
        });
    }
};
