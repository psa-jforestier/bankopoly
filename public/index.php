<?php
include('../src/lib/templating.php');
include('header.php');
include('../src/include.php');
?>
<form>
<button class="button button--primary button--small button--solid" formaction="new.php"><?=T('create_game')?></button>

<br/>
<br/>
<button class="button button--secondary button--small button--solid" formaction="join.php"><?=T('join_game')?></button><br/>
</form>


<?php

include('footer.php');

?>