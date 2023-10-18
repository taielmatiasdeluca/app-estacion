window.addEventListener('load',()=>{

})

getApi('get','app/list').then(data=>{
	data.forEach(app=>{
		let clon = app__tpl.content.cloneNode(true);
		clon.querySelector('.app').style.backgroundColor = '#'+app['color'];
		clon.querySelector('.app').href = app['link'];
		clon.querySelector('.name').textContent = app['app'];
		app__list.appendChild(clon);
	})
})