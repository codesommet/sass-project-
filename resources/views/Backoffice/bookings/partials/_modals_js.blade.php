<script>
document.addEventListener('DOMContentLoaded', function() {
    // Bootstrap validation for any forms
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
    
    console.log('Bookings Modals JS chargé');
});
</script>