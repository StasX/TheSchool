// api/student.js

const CourseApi = {
    getAll() {
        return $.ajax({
            url: '/api/course',
            method: 'GET'
        });
    },

    getById(id) {
        return $.ajax({
            url: `/api/course/${id}`,
            method: 'GET'
        });
    },

    add(data) {
        return $.ajax({
            url: '/api/course',
            method: 'POST',
            data,
            processData: false,
            contentType: false
        });
    },

    update(id, data) {
        return $.ajax({
            url: `/api/course/${id}`,
            method: 'POST',
            data,
            processData: false,
            contentType: false
        });
    },

    remove(id) {
        return $.ajax({
            url: `/api/course/${id}`,
            method: 'DELETE'
        });
    }
};

export default CourseApi;
