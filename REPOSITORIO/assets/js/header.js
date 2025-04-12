
    const micIcon = document.querySelector('.mic-icon');
    const searchInput = document.querySelector('.search-box input');

    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;

    if (SpeechRecognition) {
        const recognition = new SpeechRecognition();
        recognition.lang = 'es-ES';
        recognition.continuous = false;

        micIcon.addEventListener('click', () => {
            micIcon.classList.add('listening');
            recognition.start();
        });

        recognition.onresult = (event) => {
            const transcript = event.results[0][0].transcript;
            searchInput.value = transcript;
            micIcon.classList.remove('listening');
        };

        recognition.onend = () => {
            micIcon.classList.remove('listening');
        };

        recognition.onerror = (event) => {
            console.error('Error de reconocimiento:', event.error);
            micIcon.classList.remove('listening');
        };
    } else {
        // Si no hay soporte, oculta el icono y no muestra ninguna alerta
        if (micIcon) {
            micIcon.style.display = 'none';
        }
    }

    // Sticky header con opacidad dinámica
    const header = document.querySelector("header");

    window.addEventListener("scroll", () => {
        if (window.scrollY > 10) {
            header.classList.add("header-scrolled");
        } else {
            header.classList.remove("header-scrolled");
        }
    });

    function toggleMenu() {
    document.querySelector('nav').classList.toggle('active');
}

