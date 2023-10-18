<?php
	function goToSection($section){
		header('Location: '.URL.'/'.$section);
		die();
	}

	function goToPanel(){
		header('Location: '.URL.'/panel');
		die();
	}

?>