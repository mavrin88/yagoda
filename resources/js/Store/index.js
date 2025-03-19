import { createStore } from 'vuex';

const store = createStore({
    state: {
        count: 0  // Пример состояния
    },
    mutations: {
        increment(state) {
            state.count++;
        },
        decrement(state) {
            state.count--;
        }
    },
    actions: {
        increment({ commit }) {
            commit('increment');
        },
        decrement({ commit }) {
            commit('decrement');
        }
    },
    getters: {
        getCount(state) {
            return state.count;
        }
    }
});

export default store;
