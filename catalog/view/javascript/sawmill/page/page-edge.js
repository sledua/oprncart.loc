Vue.component('page-edge', {
    template: '#template-tag-page-edge',
    data() {
        return {};
    },
    computed: {
        ...Vuex.mapGetters(['products', 'edgeProducts', 'activeProduct', 'isEdgeSelected'])
    },
    methods: {
        handleSelectProduct(product_id) {
            this.$store.dispatch('SET_ACTIVE_PRODUCT', {productId: product_id})
        },
        handleSelectEdge(product_id) {
            this.$store.dispatch('SET_EDGE_PRODUCT', {productId: this.activeProduct, edgeId: product_id})
        }
    }
});