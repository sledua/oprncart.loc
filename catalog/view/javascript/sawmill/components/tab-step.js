Vue.component('tab-step', {
    template: '#template-tag-tab-step',
    computed: {
        ...Vuex.mapGetters(['step'])
    },
    methods: {
        handleStep(step) {
            this.$store.dispatch('SET_STEP', {step})
        }
    }
});