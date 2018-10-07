Vue.component('sawmill', {
    template: '#template-tag-sawmill',
    data() {
        return {
            showPopup: false,
            step: 'edge'
        };
    },
    computed: {
        ...Vuex.mapGetters(['products'])
    },
    methods: {
        handleOpen() {
            this.showPopup = !this.showPopup
        }
    }
});