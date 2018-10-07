Vue.component('page-detail', {
    template: '#template-tag-page-detail',
    data() {
        return {};
    },
    computed: {},
    methods: {
        handleNextStep() {
            this.$store.dispatch('SET_STEP', {step: 'additional'})
        }
    }
});