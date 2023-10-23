main__form.addEventListener('submit',async e=>{
	e.preventDefault();
	let data = new FormData(main__form);
	let response = await fetch('https://mattprofe.com.ar/alumno/3890/app-estacion/api/user/resetPass',{
		method:'post',
		body: data
	});
	let info = await response.json();
	if(info.state == 'success'){
		notify__value.textContent = 'Se cambio la contraseña ahora inicie sesion';
	}
});