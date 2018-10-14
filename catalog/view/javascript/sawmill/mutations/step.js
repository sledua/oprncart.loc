sawmill.state.step = {
    active: 'edge'
};
sawmill.mutations.SET_STEP = (state, payload) => {
    Vue.set(state.step, 'active', payload.step)
};