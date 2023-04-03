import Vue from 'vue'
import './bootstrap.js'
import App from './App.vue'

document.addEventListener('DOMContentLoaded', (event) => {
	const View = Vue.extend(App)
	new View().$mount('#content')
})
