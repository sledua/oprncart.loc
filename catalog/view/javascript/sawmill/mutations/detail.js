sawmill.state.detail = {
    entities: []
};
sawmill.mutations.ADD_DETAIL = (state, payload) => {
    let details = JSON.parse(JSON.stringify(state.detail.entities));
    details.push(payload);
    Vue.set(state.detail, 'entities', details)
};
sawmill.mutations.UPDATE_DETAIL = (state, payload) => {
    Vue.set(state.detail.entities, payload.key, payload.detail)
};