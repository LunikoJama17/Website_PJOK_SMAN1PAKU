<?php
function showSweetAlert($icon, $title, $text, $redirect) {
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: '$icon',
                title: '$title',
                text: '$text',
                timer: 2000,
                showConfirmButton: false
            }).then(function() {
                window.location.href='$redirect';
            });
        });
    </script>";
}
?>
