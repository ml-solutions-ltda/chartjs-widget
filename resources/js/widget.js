import Widget from './components/Widget.vue'

Nova.booting(app => {
    setTimeout(() => {
        app.component('chartjs-widget', Widget)
    })
})
