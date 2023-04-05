import Vue from 'vue'
import './bootstrap.js'
import App from './App.vue'
import { Tooltip } from '@nextcloud/vue'

Vue.directive('tooltip', Tooltip)

document.addEventListener('DOMContentLoaded', (event) => {
	const View = Vue.extend(App)
	new View().$mount('#content')
})
