Vue.component('page-additional', {
    template: '#template-tag-page-additional',
    data() {
        return {};
    },
    computed: {},
    methods: {
        handleNextStep() {
            this.$store.dispatch('SET_STEP', {step: 'calculate'})
        }
    }
});