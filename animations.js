document.addEventListener('DOMContentLoaded', function() {
    // Animation des cartes de donjon au survol
    const dungeonCards = document.querySelectorAll('.dungeon-card');
    dungeonCards.forEach(card => {
        card.addEventListener('mouseenter', () => {
            card.classList.add('animate__animated', 'animate__pulse');
        });
        
        card.addEventListener('mouseleave', () => {
            card.classList.remove('animate__animated', 'animate__pulse');
        });
    });
    
    // Animation des boutons
    const buttons = document.querySelectorAll('.btn');
    buttons.forEach(btn => {
        btn.addEventListener('mouseenter', () => {
            btn.classList.add('animate__animated', 'animate__headShake');
        });
        
        btn.addEventListener('mouseleave', () => {
            btn.classList.remove('animate__animated', 'animate__headShake');
        });
    });
    
    // Animation des éléments au chargement
    const animateOnLoad = document.querySelectorAll('.animate-on-load');
    animateOnLoad.forEach((el, index) => {
        setTimeout(() => {
            el.classList.add('animate__animated', 'animate__fadeInUp');
        }, index * 100);
    });
    
    // Effet parallaxe pour le background
    window.addEventListener('scroll', function() {
        const scrollPosition = window.pageYOffset;
        document.body.style.backgroundPositionY = scrollPosition * 0.5 + 'px';
    });
});