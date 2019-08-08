<?php

	if ( !isset($pageView) or !is_string($pageView) )
	{
		header('HTTP/1.0 500 Internal Server Error');
		echo '<h1>Variável não encontrada! <strong>$pageView</strong>';
	}