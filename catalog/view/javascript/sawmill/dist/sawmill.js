"use strict";

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; var ownKeys = Object.keys(source); if (typeof Object.getOwnPropertySymbols === 'function') { ownKeys = ownKeys.concat(Object.getOwnPropertySymbols(source).filter(function (sym) { return Object.getOwnPropertyDescriptor(source, sym).enumerable; })); } ownKeys.forEach(function (key) { _defineProperty(target, key, source[key]); }); } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

var sawmill = {
  initState: function initState(state) {
    this.state = $.extend({}, this.state, state);
  },
  state: {},
  actions: {},
  mutations: {},
  getters: {},
  routes: []
};

sawmill.actions.SET_ACTIVE_PRODUCT = function (_ref, payload) {
  var commit = _ref.commit;
  return commit('SET_ACTIVE_PRODUCT', payload);
};

sawmill.actions.SET_EDGE_PRODUCT = function (_ref2, payload) {
  var commit = _ref2.commit;
  return commit('SET_EDGE_PRODUCT', payload);
};

sawmill.actions.ADD_DETAIL = function (_ref3, payload) {
  var commit = _ref3.commit;
  return commit('ADD_DETAIL', payload);
};

sawmill.actions.UPDATE_DETAIL = function (_ref4, payload) {
  var commit = _ref4.commit;
  return commit('UPDATE_DETAIL', payload);
};

sawmill.actions.SET_STEP = function (_ref5, payload) {
  var commit = _ref5.commit;
  return commit('SET_STEP', payload);
};

Vue.component('sawmill', {
  template: '#template-tag-sawmill',
  data: function data() {
    return {
      showPopup: false
    };
  },
  computed: _objectSpread({}, Vuex.mapGetters(['step'])),
  methods: {
    handleOpen: function handleOpen() {
      this.showPopup = !this.showPopup;
    }
  }
});
Vue.component('tab-step', {
  template: '#template-tag-tab-step',
  computed: _objectSpread({}, Vuex.mapGetters(['step'])),
  methods: {
    handleStep: function handleStep(step) {
      this.$store.dispatch('SET_STEP', {
        step: step
      });
    }
  }
});

sawmill.getters.activeProduct = function (state) {
  return state.cart.activeProduct;
};

sawmill.getters.edgeProducts = function (state, getters) {
  var result = [];

  _.each(state.edge_products, function (product) {
    var tempProduct = _objectSpread({}, product, {
      active: false,
      selected: false
    });

    if (getters.activeProduct && !_.isUndefined(getters.edgeProduct[getters.activeProduct]) && getters.edgeProduct[getters.activeProduct] === product.product_id) {
      tempProduct.active = true;
    }

    result.push(tempProduct);
  });

  return result;
};

sawmill.getters.edgeProduct = function (state) {
  return state.cart.edgeProduct;
};

sawmill.getters.isEdgeSelected = function (state, getters) {
  var result = true;

  _.each(getters.products, function (product) {
    if (!product.active) {
      result = false;
    }
  });

  return result;
};

sawmill.getters.products = function (state, getters) {
  var result = [];

  _.each(state.products, function (product) {
    var tempProduct = _objectSpread({}, product, {
      active: false
    });

    if (getters.activeProduct === product.product_id) {
      tempProduct.active = true;
    }

    result.push(tempProduct);
  });

  return result;
};

sawmill.getters.details = function (state, getters) {
  var result = [];

  _.each(state.detail.entities, function (product) {
    if (getters.activeProduct === product.material_id) {
      var productInfo = _.find(getters.products, {
        product_id: product.material_id
      });

      var tempProduct = _objectSpread({}, product, {
        product: productInfo
      });

      result.push(tempProduct);
    }
  });

  return result;
};

sawmill.getters.step = function (state) {
  return state.step.active;
};

sawmill.state.cart = {
  activeProduct: null,
  edgeProduct: {}
};

sawmill.mutations.SET_ACTIVE_PRODUCT = function (state, payload) {
  Vue.set(state.cart, 'activeProduct', payload.productId);
};

sawmill.mutations.SET_EDGE_PRODUCT = function (state, payload) {
  Vue.set(state.cart.edgeProduct, payload.productId, payload.edgeId);
};

sawmill.state.detail = {
  entities: []
};

sawmill.mutations.ADD_DETAIL = function (state, payload) {
  var details = JSON.parse(JSON.stringify(state.detail.entities));
  details.push(payload);
  Vue.set(state.detail, 'entities', details);
};

sawmill.mutations.UPDATE_DETAIL = function (state, payload) {
  Vue.set(state.detail.entities, payload.key, payload.detail);
};

sawmill.state.step = {
  active: 'detail'
};

sawmill.mutations.SET_STEP = function (state, payload) {
  Vue.set(state.step, 'active', payload.step);
};

Vue.component('page-additional', {
  template: '#template-tag-page-additional',
  data: function data() {
    return {};
  },
  computed: {},
  methods: {
    handleNextStep: function handleNextStep() {
      this.$store.dispatch('SET_STEP', {
        step: 'calculate'
      });
    }
  }
});
Vue.component('page-calculate', {
  template: '#template-tag-page-calculate',
  data: function data() {
    return {};
  },
  computed: {},
  methods: {
    handleNextStep: function handleNextStep() {
      this.$store.dispatch('SET_STEP', {
        step: 'order'
      });
    }
  }
});
Vue.component('page-detail', {
  template: '#template-tag-page-detail',
  data: function data() {
    return {
      detail: {
        material_id: '',
        width: '',
        height: '',
        quantity: ''
      }
    };
  },
  computed: _objectSpread({}, Vuex.mapGetters(['products', 'details', 'activeProduct'])),
  methods: {
    handleSelectProduct: function handleSelectProduct(product_id) {
      this.$store.dispatch('SET_ACTIVE_PRODUCT', {
        productId: product_id
      });
    },
    handleNextStep: function handleNextStep() {
      this.$store.dispatch('SET_STEP', {
        step: 'additional'
      });
    },
    handleEditWidth: function handleEditWidth(e, key) {
      var detail = this.details[key];
      this.$store.dispatch('UPDATE_DETAIL', {
        key: key,
        detail: _objectSpread({}, detail, {
          width: e.target.value
        })
      });
    },
    handleEditHeight: function handleEditHeight(e, key) {
      var detail = this.details[key];
      this.$store.dispatch('UPDATE_DETAIL', {
        key: key,
        detail: _objectSpread({}, detail, {
          height: e.target.value
        })
      });
    },
    handleEditQuantity: function handleEditQuantity(e, key) {
      var detail = this.details[key];
      this.$store.dispatch('UPDATE_DETAIL', {
        key: key,
        detail: _objectSpread({}, detail, {
          quantity: e.target.value
        })
      });
    },
    addDetail: function addDetail() {
      if (this.activeProduct) {
        this.$store.dispatch('ADD_DETAIL', _objectSpread({}, {
          material_id: '',
          width: '',
          height: '',
          quantity: ''
        }, {
          material_id: this.activeProduct
        }));
      }
    },
    onSubmit: function onSubmit() {
      this.$store.dispatch('ADD_DETAIL', _objectSpread({}, this.detail, {
        material_id: this.activeProduct
      }));
      this.detail = {
        material_id: '',
        width: '',
        height: '',
        quantity: ''
      };
    }
  }
});
Vue.component('page-edge', {
  template: '#template-tag-page-edge',
  data: function data() {
    return {};
  },
  computed: _objectSpread({}, Vuex.mapGetters(['products', 'edgeProducts', 'activeProduct', 'isEdgeSelected'])),
  methods: {
    handleSelectProduct: function handleSelectProduct(product_id) {
      this.$store.dispatch('SET_ACTIVE_PRODUCT', {
        productId: product_id
      });
    },
    handleSelectEdge: function handleSelectEdge(product_id) {
      this.$store.dispatch('SET_EDGE_PRODUCT', {
        productId: this.activeProduct,
        edgeId: product_id
      });
    },
    handleNextStep: function handleNextStep() {
      this.$store.dispatch('SET_STEP', {
        step: 'detail'
      });
    }
  }
});
Vue.component('page-order', {
  template: '#template-tag-page-order',
  data: function data() {
    return {};
  },
  computed: {},
  methods: {}
});