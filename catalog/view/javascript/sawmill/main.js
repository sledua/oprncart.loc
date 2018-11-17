const sawmill = {
    initState(state) {
        this.state = $.extend({}, this.state, state)
    },
    state: {},
    actions: {},
    mutations: {},
    getters: {},
    routes: []
};