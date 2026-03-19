<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validation Bootstrap pour les formulaires
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
    
    console.log('JS Modals Catégories Financières chargé');
});
</script>