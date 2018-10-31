Vue.component('page-detail', {
    template: '#template-tag-page-detail',
    data() {
        return {
            detail: {
                name: '',
                material_id: '',
                width: '',
                height: '',
                quantity: '',
                multiplicity_stitching: '',
                take_into_account_texture: 0
            },
            textureOptions: {
                0: this.$t('common.text_yes'),
                1: this.$t('common.text_no')
            }
        };
    },
    computed: {
        ...Vuex.mapGetters(['products', 'details'])
    },
    methods: {
        handleNextStep() {
            this.$store.dispatch('SET_STEP', {step: 'additional'})
        },
        onSubmit() {
            this.$store.dispatch('ADD_DETAIL', {...this.detail});
            this.detail = {
                name: '',
                material_id: '',
                width: '',
                height: '',
                quantity: '',
                multiplicity_stitching: '',
                take_into_account_texture: 0
            }
        }
    }
});