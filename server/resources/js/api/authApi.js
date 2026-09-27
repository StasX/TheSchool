// api/student.js

const AuthApi = {
    login(data) {
        return $.post('/api/login', data);
    },
    logout() {
        return $.post('/api/logout');
    },
    auth() {
        return $.get('/api/auth');
    }
};

export default AuthApi;
