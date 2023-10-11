<?php 
	
	if(!isset($_GET['chipid'])){
		header('Location: panel.html');
	}
	$chip = $_GET['chipid'];


?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Detalles | AppEstacion</title>
	<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
	<link rel="stylesheet" href="detalle.css">
</head>
<body>
	
	<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/chart.js"></script>

	<input type="hidden" name="" id="chipid__hidden" value="<?= $chip ?>">
	
	<content>
		<main>
			<canvas id="grafico__main"></canvas>
		</main>

		<div class="controls">
			<button>
				<span class="material-symbols-outlined">device_thermostat</span>
				<div >
					<span id="temp__value">---</span> °
				</div>
			</button>
			<button>
				<span class="material-symbols-outlined">humidity_high</span>
				<div>
					<span id="humedad__value">---</span> %
				</div>
			</button>
			<button>
				<span class="material-symbols-outlined">compress</span>
				<div >
					<span id="compresion__value">---</span> hPa
				</div>
			</button>
			<button>
				<span class="material-symbols-outlined">wind_power</span>
				<div >
					<span id="wind__value">---</span> Km/H
				</div>
			</button>
			<button>
				<span class="material-symbols-outlined">local_fire_department</span>
				<div >
					<span id="fire__value">---</span>
				</div>
			</button>
		</div>
		
	</content>

	<script src="detalle.js"></script>
</body>
</html>