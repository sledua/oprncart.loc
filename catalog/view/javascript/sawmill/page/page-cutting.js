Vue.component('page-cutting', {
    template: '#template-tag-page-cutting',
    data() {
        return {};
    },
    computed: {},
    methods: {
        handleNextStep() {
            this.$store.dispatch('SET_STEP', {step: 'services'})
        },
    }
});