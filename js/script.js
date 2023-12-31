

async function getEstaciones(){
	const response = await fetch("https://mattprofe.com.ar/proyectos/app-estacion/datos.php?mode=list-stations");
	const data = await response.json();
	return data;
}

cerrar__sesion.addEventListener('click',async e=>{
	await fetch('https://mattprofe.com.ar/alumno/3890/app-estacion/api/user/logout');
	window.location.href = "https://mattprofe.com.ar/alumno/3890/app-estacion/";
})

getEstaciones().then(data=>{
	data.map(estacion=>{
		let clon = estacion__tpl.content.cloneNode(true);
		clon.querySelector('.estacion').href = clon.querySelector('.estacion').href.replace('ID',estacion.chipid);
		clon.querySelector('.apodo').textContent = estacion.apodo.toUpperCase();
		clon.querySelector('.ubicacion').textContent = estacion.ubicacion;
		clon.querySelector('.visitas').textContent = estacion.visitas;
		estaciones_list.appendChild(clon);
	});
});