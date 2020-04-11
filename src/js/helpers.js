import dayjs from 'dayjs'

export function formatSize(size) {
    let i = -1;
    const byteUnits = [' KB', ' MB', ' GB', ' TB', 'PB', 'EB', 'ZB', 'YB'];

    do {
        size = size / 1024;
        i++;
    } while (size > 1024);

    return Math.max(size, 0.1).toFixed(1) + byteUnits[i];
}

export function toast(options) {
    window.VueApp.$toast.open(Object.assign({
        actionText: null,
        position: 'is-top',
        duration: 5000
    }, options))
}

export function formatToLocalDateTime(datetime) {
    let dt = dayjs(datetime);
    if (!dt.isValid()) {
        return false;
    }

    let dateTimeArray = dt.toArray();

    return new Date(...dateTimeArray).toLocaleString();
}

export function formatToLocalDate(datetime) {
    let dt = dayjs(datetime);
    if (!dt.isValid()) {
        return false;
    }

    let dateTimeArray = dt.toArray();

    return new Date(...dateTimeArray).toLocaleDateString();
}

export function formatToLocalTime(datetime) {
    let dt = dayjs(datetime);
    if (!dt.isValid()) {
        return false;
    }

    let dateTimeArray = dt.toArray();

    return new Date(...dateTimeArray).toLocaleTimeString();
}