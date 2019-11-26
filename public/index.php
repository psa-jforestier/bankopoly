<?php
include('../src/lib/templating.php');
include('header.php');
include('../src/include.php');

$Game = new DAOGame();
$nb = $Game->getNumberOfGames()['nb'];

?>
<form>
<button class="button button--primary button--small button--solid" formaction="new.php"><?=T('create_game')?></button>

<br/>
<br/>
<button class="button button--secondary button--small button--solid" formaction="join.php"><?=T('join_game')?></button><br/>
<?=($nb > 1 ? sprintf(T('nbparties'), $nb) : sprintf(T('nbparty'), $nb))?>
</form>

<hr/>
[ <a href="https://github.com/psa-jforestier/bankopoly">Fork me on GitHub</a> ]
<hr/>
<?php

include('footer.php');

?>