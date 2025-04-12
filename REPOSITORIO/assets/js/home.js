

    
    // Animación para la barra de búsqueda
    const searchBar = document.querySelector('.search-bar');
    const searchIcon = document.querySelector('.search-iconn');
    
    searchBar.addEventListener('focus', function() {
        searchIcon.style.color = '#ff5500';
    });
    
    searchBar.addEventListener('blur', function() {
        searchIcon.style.color = '#ff7e00';
    });
    
    // Efecto de flotación para los círculos de fondo
    const circles = document.querySelectorAll('.circle');
    
    function animateCircles() {
        circles.forEach((circle, index) => {
            // Pequeño movimiento aleatorio adicional
            const randomX = Math.random() * 10 - 5;
            const randomY = Math.random() * 10 - 5;
            
            circle.style.transform = `translate(calc(-50% + ${randomX}px), calc(-50% + ${randomY}px))`;
        });
        
        requestAnimationFrame(animateCircles);
    }
    
    // Descomenta la siguiente línea para activar animación adicional
    animateCircles();
    
    