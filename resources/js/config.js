const baseUrl = './../';
const storageUrl = baseUrl + 'storage/';


function filePath(path) {
    return storageUrl + path;
}

const defaultMinute = 60;
const defaultHour = 60 * defaultMinute;
const defaultDay = 24 * defaultHour;
const defaultMonth = 30 * defaultDay;
const defaultYear = 12 * defaultMonth;

function timestamp2String(timestamp) {
    var diff = (new Date()).getTime()/1000 - timestamp;
    var diffInYear = Math.floor(diff/defaultYear);
    if (diffInYear > 0) {
        return diffInYear + '年前';
    }
    var diffInMonth = Math.floor(diff/defaultMonth);
    if (diffInMonth > 0) {
        return diffInMonth + '月前';
    }
    var diffInDay = Math.floor(diff/defaultDay);
    if (diffInDay > 0) {
        return diffInDay + '天前';
    }
    var diffInHour = Math.floor(diff/defaultHour);
    if (diffInHour > 0) {
        return diffInHour + '小时前';
    }
    var diffInMinute = Math.floor(diff/defaultMinute);
    if (diffInMinute > 0) {
        return diffInMinute + '分钟前';
    }
}

function matchStatus2String(status) {
    var diff = (new Date()).getTime()/1000 - timestamp;
    var diffInYear = Math.floor(diff/defaultYear);
    if (diffInYear > 0) {
        return diffInYear + '年前';
    }
    var diffInMonth = Math.floor(diff/defaultMonth);
    if (diffInMonth > 0) {
        return diffInMonth + '月前';
    }
    var diffInDay = Math.floor(diff/defaultDay);
    if (diffInDay > 0) {
        return diffInDay + '天前';
    }
    var diffInHour = Math.floor(diff/defaultHour);
    if (diffInHour > 0) {
        return diffInHour + '小时前';
    }
    var diffInMinute = Math.floor(diff/defaultMinute);
    if (diffInMinute > 0) {
        return diffInMinute + '分钟前';
    }
}

export default {
    baseUrl,
    storageUrl,
    filePath,
    timestamp2String,
};
