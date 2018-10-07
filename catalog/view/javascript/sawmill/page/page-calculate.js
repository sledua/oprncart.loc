Vue.component('page-calculate', {
    template: '#template-tag-page-calculate',
    data() {
        return {};
    },
    computed: {},
    methods: {
        handleNextStep() {
            this.$store.dispatch('SET_STEP', {step: 'order'})
        }
    }
});