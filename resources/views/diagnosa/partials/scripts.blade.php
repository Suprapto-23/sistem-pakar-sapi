<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Animasi progress bars
        const progressBars = document.querySelectorAll('.bg-green-600, .bg-yellow-600, .bg-orange-600, .bg-blue-600');
        progressBars.forEach(bar => {
            const width = bar.style.width;
            bar.style.width = '0';
            setTimeout(() => {
                bar.style.width = width;
            }, 500);
        });

        // Animasi fade-in pada scroll
        const fadeElements = document.querySelectorAll('.fade-in');
        
        const fadeInOnScroll = () => {
            fadeElements.forEach(element => {
                const elementTop = element.getBoundingClientRect().top;
                const elementVisible = 150;
                
                if (elementTop < window.innerHeight - elementVisible) {
                    element.classList.add('visible');
                }
            });
        };
        
        window.addEventListener('scroll', fadeInOnScroll);
        fadeInOnScroll();
    });

    // Enhanced print functionality
    function enhancedPrint() {
        const printButton = document.querySelector('button[onclick="enhancedPrint()"]');
        const originalText = printButton.innerHTML;
        printButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Mempersiapkan...';
        
        setTimeout(() => {
            window.print();
            setTimeout(() => {
                printButton.innerHTML = originalText;
            }, 1000);
        }, 1000);
    }
</script>