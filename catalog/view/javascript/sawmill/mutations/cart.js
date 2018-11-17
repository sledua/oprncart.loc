sawmill.state.cart = {
    activeProduct: null,
    edgeProduct: {}
};
sawmill.mutations.SET_ACTIVE_PRODUCT = (state, payload) => {
    Vue.set(state.cart, 'activeProduct', payload.productId)
};
sawmill.mutations.SET_EDGE_PRODUCT = (state, payload) => {
    Vue.set(state.cart.edgeProduct, payload.productId, payload.edgeId)
};