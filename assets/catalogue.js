// Effet visuel uniquement : aucune gestion de panier côté navigateur.
(() => {
    document.querySelectorAll('.add-button').forEach(button => {
        button.addEventListener('click', event => {
            if (matchMedia('(prefers-reduced-motion: reduce)').matches) return;
            const bounds = event.currentTarget.getBoundingClientRect();
            for (let i = 0; i < 16; i++) {
                const flame = document.createElement('span');
                flame.className = 'flame-particle';
                flame.textContent = i % 4 === 0 ? '✦' : '🔥';
                flame.style.left = `${bounds.left + bounds.width / 2}px`;
                flame.style.top = `${bounds.top + bounds.height / 2}px`;
                flame.style.setProperty('--dx', `${(Math.random() - .5) * 320}px`);
                flame.style.setProperty('--dy', `${-60 - Math.random() * 240}px`);
                flame.style.setProperty('--spin', `${(Math.random() - .5) * 100}deg`);
                document.getElementById('flames').append(flame);
                setTimeout(() => flame.remove(), 1000);
            }
        });
    });
})();
