import { invalidCredentials } from "./invalidFields";

const AuthFailure = {
    login(xhr) {
        invalidCredentials(xhr.status === 401);
    },
    logout(xhr) {
        Swal.fire({
            icon: "error",
            title: "Error occurred during logout",
            text: "Please try again later.",
            confirmButtonText: "OK",
            buttonsStyling: false,
            customClass: {
                confirmButton: "btn btn-dark"
            }
        });
    },
    auth(xhr) {
        console.log(xhr);
        if (xhr.status === 401) {
            location.hash = '';
            location.reload();
        } else {
            Swal.fire({
                icon: "error",
                title: "Some error occurred...",
                text: "Please try again later.",
                confirmButtonText: "OK",
                buttonsStyling: false,
                customClass: {
                    confirmButton: "btn btn-dark"
                }
            });
        }
    },
}

export default AuthFailure;
