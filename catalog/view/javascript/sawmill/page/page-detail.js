Vue.component('page-detail', {
    template: '#template-tag-page-detail',
    data() {
        return {
            detail: {
                material_id: '',
                width: '',
                height: '',
                quantity: ''
            },
        };
    },
    computed: {
        ...Vuex.mapGetters(['products', 'details', 'activeProduct',])
    },
    methods: {
        handleSelectProduct(product_id) {
            console.log(product_id)
            this.$store.dispatch('SET_ACTIVE_PRODUCT', {productId: product_id})
        },
        handleNextStep() {
            this.$store.dispatch('SET_STEP', {step: 'additional'})
        },
        handleEditWidth(e, key) {
            const detail = this.details[key];
            this.$store.dispatch('UPDATE_DETAIL', {key: key, detail: {...detail, width: e.target.value}})
        },
        handleEditHeight(e, key) {
            const detail = this.details[key];
            this.$store.dispatch('UPDATE_DETAIL', {key: key, detail: {...detail, height: e.target.value}})
        },
        handleEditQuantity(e, key) {
            const detail = this.details[key];
            this.$store.dispatch('UPDATE_DETAIL', {key: key, detail: {...detail, quantity: e.target.value}})
        },
        addDetail() {
            if (this.activeProduct) {
                this.$store.dispatch('ADD_DETAIL', {
                    ...{
                        material_id: '',
                        width: '',
                        height: '',
                        quantity: ''
                    }, material_id: this.activeProduct
                });
            }
        },
        onSubmit() {
            this.$store.dispatch('ADD_DETAIL', {...this.detail, material_id: this.activeProduct});
            this.detail = {
                material_id: '',
                width: '',
                height: '',
                quantity: ''
            }
        }
    }
});