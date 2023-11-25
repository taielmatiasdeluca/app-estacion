cerrar__sesion.addEventListener('click',async e=>{
	await fetch('https://mattprofe.com.ar/alumno/3890/app-estacion/api/user/logout');
	window.location.href = "https://mattprofe.com.ar/alumno/3890/app-estacion/";
})