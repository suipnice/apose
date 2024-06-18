<?php

include "CAS/CAS.php";

# session_start(); session_name();

phpCAS::client(CAS_VERSION_2_0,"login.univ-cotedazur.fr",443,"");
phpCAS::forceAuthentication();

$_SESSION['authen'] = "ok";

echo '<meta http-equiv="Refresh" content="0;url=comp.php">';

?>
