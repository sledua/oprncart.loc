sawmill.state.detail = {
    entities: {}
};
sawmill.mutations.ADD_DETAIL = (state, payload) => {
    var detailID = Math.random().toString(36).substr(2, 9);
    const detail = {
        ...payload,
        id: detailID
    };
    Vue.set(state.detail.entities, detailID, detail)
};
sawmill.mutations.UPDATE_DETAIL = (state, payload) => {
    Vue.set(state.detail.entities, payload.detailID, payload.detail)
};