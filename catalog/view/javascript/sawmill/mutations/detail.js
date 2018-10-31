sawmill.state.detail = {
    entities: []
};
sawmill.mutations.ADD_DETAIL = (state, payload) => {
    let details = JSON.parse(JSON.stringify(state.detail.entities));
    details.push(payload);
    console.log(details);
    Vue.set(state.detail, 'entities', details)
};