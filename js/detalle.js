const chip_id = chipid__hidden.value;
const cantidad_info = 7;

// variable que se usará para instanciar el gráfico
let grafico = null
let selected = 'Temperatura';
  Chart.defaults.borderColor = '#000000';
	Chart.defaults.color = '#000000';

let global_data;

function removeAllLink(){
	temp__value.closest('button').classList.remove('active');
	humedad__value.closest('button').classList.remove('active');
	compresion__value.closest('button').classList.remove('active');
	wind__value.closest('button').classList.remove('active');
	fire__value.closest('button').classList.remove('active');
}


temp__value.closest('button').addEventListener('click',e=>{
		removeAllLink();
	selected = 'Temperatura';
	temp__value.closest('button').classList.add('active');
	reloadData();
});
humedad__value.closest('button').addEventListener('click',e=>{
	selected = 'Humedad';
	removeAllLink();
	humedad__value.closest('button').classList.add('active');

	reloadData();
});
compresion__value.closest('button').addEventListener('click',e=>{
	selected = 'Presion';
	removeAllLink();
	compresion__value.closest('button').classList.add('active');

	reloadData();
});
wind__value.closest('button').addEventListener('click',e=>{
	selected = 'Viento';
	removeAllLink();
	wind__value.closest('button').classList.add('active');

	reloadData();
});
fire__value.closest('button').addEventListener('click',e=>{
	selected = 'Fuego';
	removeAllLink();
	fire__value.closest('button').classList.add('active');

	reloadData();
});



async function getData() {
	const response = await fetch(`https://mattprofe.com.ar/proyectos/app-estacion/datos.php?chipid=${chip_id}&cant=${cantidad_info}`)
	const data = await response.json();
	 return data;
}

function reloadData(){
	let data = global_data;
	//Reloading Buttons
	temp__value.textContent = Math.round(data[0].temperatura);
	humedad__value.textContent = Math.round(data[0].humedad);
	compresion__value.textContent = Math.round(data[0].presion);
	wind__value.textContent = Math.round(data[0].viento);
	fire__value.textContent = fireDanger(data[0].fwi)

	//
	let labels = [];
	let values = [];

	for (var i = data.length - 1; i >= 0; i--) {
		if(selected == 'Temperatura'){
			values.push(data[i].temperatura);
		}
		if(selected == 'Humedad'){
			values.push(data[i].humedad);
		}
		if(selected == 'Presion'){
			values.push(data[i].presion);
		}
		if(selected == 'Viento'){
			values.push(data[i].viento);
		}
		if(selected == 'Fuego'){
			values.push(data[i].fwi);
		}
		
		labels.push(data[i].fecha)
	}
	procesaDatos(values,labels);

}



var reloadInterval = window.setInterval(async function(){
  global_data = await getData();
  reloadData();
}, 10000);

window.addEventListener('load',async e=>{
	global_data = await getData();

	reloadData();
})

function procesaDatos(dato,labels){
	const valores = {
		labels: labels,
		datasets: [{
			label: selected, // detalle de la linea graficada
			backgroundColor: 'rgb(25, 174, 49)', // color circulo
			borderColor: 'rgb(25, 174, 49)', // color linea
			data: dato // valores a graficar
		}]
	}
	pintaGrafico(valores)
}


// muestra el gráfico
function pintaGrafico(valores){
	// Opciones generales del gráfico
	const options = {
		indexAxis: 'x', // Orden de los ejes del gráfico
		animation: {
			duration: 0
		},
		responsive: true,
		responsiveAnimationDuration: 0,
	}

	// Información con la cual se genera el gráfico
	const config = {
		type: 'line',
		data: valores,
		options: options
	}
	
	// si el objeto gráfico ya esta instanciado se destruye para que se vuelva a crear limpio
	if(grafico!=null){
      grafico.destroy();
   }

	// Crea el gráfico dentro del canvas
	grafico = new Chart(document.querySelector("#grafico__main"), config)

}


// Retorna el peligro de incendio con una frase
// =================================
function fireDanger(fwi){
	let fwiFloat = parseFloat(fwi)
	
	if(fwiFloat>=50){
		return "Extremo"
	}else{
		if(fwiFloat>=38){
			return "Muy alto"
		}else{
			if(fwiFloat>=21.3){
				return "Alto"
			}else{
				if(fwiFloat>=11.2){
					return "Moderado"
				}else{
					if(fwiFloat>=5.2){
						return "Bajo"
					}else{
						return "Muy bajo"
					}
				}
			}
		}
	}

}