<footer class="pt-4 pb-3">
  <div class="content has-text-centered">
    <p><?php echo APP_NAME; ?> - <?php echo UNIV_NAME; ?></p>
  </div>
</footer>

<script src="js/fontawesome.js"></script>
<script src="js/jquery-1.12.4.min.js" integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ="
  crossorigin="anonymous"></script>
<script>
  $(document).ready(function () {

    $(".session").click(function () {
      etat = $(this).val();

      if (etat == 1) {
        $("#field-session").removeClass("is-invisible");
      } else {
        $("#field-session").addClass("is-invisible");
      }

    });

  });

</script>
</body>

</html>