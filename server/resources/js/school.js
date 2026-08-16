import template from '../templates/pages/school.html?raw';
import {student} from './student';


export default function school(user) {
    $('body').html(template);

    if (user && (user.Role === 'owner' || user.Role === 'manager')) {
        $('#navbar').append(`
                            <li class="nav-item">
                                <a href="#!administration" class="nav-link">
                                    Administration
                                </a>
                            </li>
                        `);
    }
    $('#user-info').text(`${user.Name}, ${user.Role}`);
    $.get(user.Image).done(() => {
        $('#user-image').attr('src', user.Image);
    }).fail(() => {
        $('#user-image').attr('src', '/img/user.png');
    });
    $.get('/api/student').done((data)=>{
        console.log({'students':data});
    });
    $.get('/api/course').done((data)=>{
        console.log({'course':data});
    });

}
