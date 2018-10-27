sawmill.getters.activeProduct = (state) => (state.cart.activeProduct);
sawmill.getters.edgeProducts = (state, getters) => {
    let result = [];
    _.each(state.edge_products, (product) => {
        let tempProduct = {...product, active: false, selected: false};
        if(getters.activeProduct && !_.isUndefined(getters.edgeProduct[getters.activeProduct]) && getters.edgeProduct[getters.activeProduct] === product.product_id){
            tempProduct.active = true
        }
        result.push(tempProduct)
    });
    return result
    
};
sawmill.getters.edgeProduct = (state) => (state.cart.edgeProduct);
sawmill.getters.isEdgeSelected = (state, getters) => {
    let result = true;
    _.each(getters.products, (product) => {
        if(!product.active) {
            result = false
        }
    });

    return result
};
sawmill.getters.products = (state, getters) => {
    let result = [];
    _.each(state.products, (product) => {
        let tempProduct = {...product, active: false};
        if(getters.activeProduct === product.product_id){
            tempProduct.active = true
        }
        result.push(tempProduct)
    });
    return result
};