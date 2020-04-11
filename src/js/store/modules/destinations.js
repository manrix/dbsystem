// initial state
const state = {
    drivers: {
        ftp: 'Ftp',
        dropbox: 'Dropbox',
        g3: 'Google Drive',
        local: 'Local',
    }
};

// getters
const getters = {
    destinationDrivers: state => {
        return state.drivers;
    },
};

export default {
    state,
    getters,
}