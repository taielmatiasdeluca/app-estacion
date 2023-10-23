main__form.addEventListener('submit',async e=>{
	e.preventDefault();
	let data = new FormData(main__form);
	let response = await fetch('https://mattprofe.com.ar/alumno/3890/app-estacion/api/user/recover',{
		method:'post',
		body: data
	});
	let info = await response.json();
	if(info.state == 'success'){
		notify__value.textContent = 'Si tu email coincide con alguno registrado se te enviaran las intruciones a el';
	}
});