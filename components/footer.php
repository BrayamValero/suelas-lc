<!-- JavaScript -->
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.js"></script>
<script src="js/jquery.mCustomScrollbar.concat.min.js"></script>

<script>

$(document).ready(function () {
    
    // Custom Scroll Bar
    $("#sidebar").mCustomScrollbar({
        theme: "minimal-dark"
    });

    // Toggle .active classes
    $('.sidebarCollapse').on('click', function () {
        $('#sidebar, #contenido').toggleClass('active');
    });

    // select2 => Aplicando el dropdown.
    $('.dropdown-select2').select2({
        theme: "bootstrap4",
    });

    // Datatables => Buscador Personalizado
    document.getElementById('searchInput').addEventListener('keyup', function () {
        tabla.search(this.value).draw();
    });

});

</script>

</body>
</html>