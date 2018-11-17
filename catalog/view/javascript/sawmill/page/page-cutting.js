Vue.component('page-cutting', {
    template: '#template-tag-page-cutting',
    data() {
        return {
            edge_top: '',
            edge_bottom: '',
            edge_left: '',
            edge_right: '',
            kerf_thickness: '',
            kerf_options: [
                {text: this.$t('common.text_none'), value: ''},
                {text: 1 + ' ' + this.$t('common.text_unit_square_mm'), value: 1},
                {text: 2 + ' ' + this.$t('common.text_unit_square_mm'), value: 2},
                {text: 3 + ' ' + this.$t('common.text_unit_square_mm'), value: 3},
                {text: 4 + ' ' + this.$t('common.text_unit_square_mm'), value: 4},
                {text: 5 + ' ' + this.$t('common.text_unit_square_mm'), value: 5},
            ]
        };
    },
    computed: {
        square() {
            let square = 0;
            for(let detailID in this.details) {
                square += this.details[detailID].width * this.details[detailID].height;
            }
            return square;
        },
        quantity() {
            let quantity = 0;
            for(let detailID in this.details) {
                quantity += this.details[detailID].quantity;
            }
            return quantity;
        },
        ...Vuex.mapGetters(['details', 'activeProduct'])
    },
    methods: {
        handleNextStep() {
            this.$store.dispatch('SET_STEP', {step: 'services'})
        },
    }
});