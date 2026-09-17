// api/student.js

const AdministratorApi = {
    getAll() {
        return $.ajax({
            url: '/api/administrator',
            method: 'GET'
        });
    },

    getById(id) {
        return $.ajax({
            url: `/api/administrator/${id}`,
            method: 'GET'
        });
    },

    add(data) {
        return $.ajax({
            url: '/api/administrator',
            method: 'POST',
            data,
            processData: false,
            contentType: false
        });
    },

    update(id, data) {
        return $.ajax({
            url: `/api/administrator/${id}`,
            method: 'POST',
            data,
            processData: false,
            contentType: false
        });
    },

    remove(id) {
        return $.ajax({
            url: `/api/administrator/${id}`,
            method: 'DELETE'
        });
    }
};

export default AdministratorApi;
