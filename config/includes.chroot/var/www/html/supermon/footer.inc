<div class="clearer"></div>

<div id="footer"><!-- Start Footer -->

<?php
if ($_SESSION['sm61loggedin'] === true) {
?>

<SCRIPT>
    function OpenExample () {
        window.open('http://anyurl.com');
    }
</SCRIPT>

<!--
<b>You can add buttons here:</b>   
   <input type="button" class="submit" value="Example" OnClick="OpenExample()">
 <br><br>
-->

<?php
}
print "<b>System maintained by:   <i>$NAME,   $CALL</i></b>";
?>

</div><!-- End Footer -->
<br><br>
</body>
</html>
