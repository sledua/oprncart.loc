sawmill.getters.details = (state, getters) => {
    let result = [];
    _.each(state.detail.entities, (product) => {
        let productInfo = _.find(getters.products, {product_id: product.material_id});
        let tempProduct = {...product, product: productInfo};
        result.push(tempProduct)
    });
    return result
};