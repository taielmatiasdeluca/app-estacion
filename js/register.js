main__form.addEventListener('submit',async e=>{
	e.preventDefault();
	let data = new FormData(main__form);
	;
	if(!email__input.value.includes('@')){
		notify__value.style.color = 'red';
		notify__value.textContent = 'Ingrese un email correcto';
		return;
	}
	if(password__input.value != repeat__input.value){
		notify__value.style.color = 'red';
		notify__value.textContent = 'Las contraseñas no son iguales';
		return;
	}
	
	let response = await fetch('https://mattprofe.com.ar/alumno/3890/app-estacion/api/user/register',{
		method:'post',
		body: data
	});
	let info = await response.json();
	if(info.state == 'success'){
		notify__value.style.color = 'green';
		notify__value.textContent = 'Ahora debes activar la cuenta desde tu email';
	}else{
		notify__value.style.color = 'red';
		notify__value.textContent = info.type;
	}
	

})