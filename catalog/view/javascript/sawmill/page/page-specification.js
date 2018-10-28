Vue.component('page-specification', {
    template: '#template-tag-page-specification',
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
        ...Vuex.mapGetters(['products', 'details', 'activeProduct',])
    },
    methods: {
        handleSelectProduct(product_id) {
            this.$store.dispatch('SET_ACTIVE_PRODUCT', {productId: product_id})
        },
        handleNextStep() {
            this.$store.dispatch('SET_STEP', {step: 'cutting'})
        },
        handleEditWidth(e, detailID) {
            const detail = this.details[detailID];
            this.$store.dispatch('UPDATE_DETAIL', {detailID, detail: {...detail, width: Number(e.target.value)}})
        },
        handleEditHeight(e, detailID) {
            const detail = this.details[detailID];
            this.$store.dispatch('UPDATE_DETAIL', {detailID, detail: {...detail, height: Number(e.target.value)}})
        },
        handleEditQuantity(e, detailID) {
            const detail = this.details[detailID];
            this.$store.dispatch('UPDATE_DETAIL', {detailID, detail: {...detail, quantity: Number(e.target.value)}})
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