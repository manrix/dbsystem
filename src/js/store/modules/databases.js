// initial state
const state = {
    drivers: {
        mysql: 'MySql',
        postgresql: 'PostgreSql',
        sqlite: 'Sqlite',
    }
};

// getters
const getters = {
    databaseDrivers: state => {
        return state.drivers;
    },
};

export default {
    state,
    getters,
}