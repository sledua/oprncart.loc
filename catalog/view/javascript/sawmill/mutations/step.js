sawmill.state.step = {
    active: 'detail'
};
sawmill.mutations.SET_STEP = (state, payload) => {
    Vue.set(state.step, 'active', payload.step)
};