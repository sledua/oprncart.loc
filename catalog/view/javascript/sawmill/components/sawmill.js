Vue.component('sawmill', {
    template: '#template-tag-sawmill',
    data() {
        return {
            showPopup: false
        };
    },
    computed: {
        ...Vuex.mapGetters(['step'])
    },
    methods: {
        handleOpen() {
            this.showPopup = !this.showPopup
        }
    }
});