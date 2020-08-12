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

        // Select2 Plugin
        $('.select-filter').select2({
            theme: "bootstrap4",
        });

    });

</script>

</body>
</html>