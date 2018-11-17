sawmill.state.step = {
    active: 'specification'
};
sawmill.mutations.SET_STEP = (state, payload) => {
    Vue.set(state.step, 'active', payload.step)
};