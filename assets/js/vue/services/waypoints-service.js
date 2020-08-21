import axios from 'axios';

// export function fetchItems(searchTerm, pageNum) {
// return []
// }
/**
 * @param {string|null} searchTerm
 * @param {int|null} pageNum
 * @returns {Promise}
 */
export function fetchItems(searchTerm, pageNum) {
    const params = {};
    if (searchTerm) {
        params.nickname = searchTerm;
    }

    if (pageNum) {
        params.page = pageNum;
    }

    return axios.get('/api/waypoints', {
        params,
    });
}
