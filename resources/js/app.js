import { createApp } from 'vue'
import Welcome from './components/Welcome.vue';

// const app = createApp()

var app = new Vue({
el: '#app'
});

Vue.component('welcome', {
template: Welcome
})

app.component('welcome', Welcome)

app.mount('#app')

