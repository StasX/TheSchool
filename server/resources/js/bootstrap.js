import $ from 'jquery';
import Swal from 'sweetalert2';

window.$ = $;
window.jQuery = $;
window.Swal = Swal;

// Enable cookies in AJAX requests
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
