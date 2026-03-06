const toggleBtn = document.getElementById('toggleBtn');
const DashboardSidebar = document.getElementById('DashboardSidebar');
const DashboardRightContainer = document.getElementById('DashboardRightContainer');

toggleBtn.addEventListener('click', (event) => {
    event.preventDefault();
    DashboardSidebar.classList.toggle('collapsed');
    DashboardRightContainer.classList.toggle('expanded');
});


let message = $('.responseMessage');

if(message.length){

    // Fade IN
    setTimeout(function(){
        message.addClass('show');
    }, 100);

    // Fade OUT after 3 seconds
    setTimeout(function(){
        message.removeClass('show').addClass('hide');

        setTimeout(function(){
            message.remove();
        }, 500);

    }, 3000);
}


// After removing the user reordering the table 
function reOrderTable(){
    $('.users tbody tr').each(function(index){
        $(this).find('td:first').text(index + 1);
    });
}

$(document).on('click', '.deleteUser', function(e){
    e.preventDefault();

    let button = $(this);
    let userId = button.data('userid');
    let fname = button.data('fname');
    let lname = button.data('lname');
    let fullName = fname + ' ' + lname;

    Swal.fire({
        title: 'Delete User?',
        text: "Are you sure you want to remove " + fullName + "?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#008cff',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Delete',
        reverseButtons: true
    }).then((result) => {

        if(result.isConfirmed){

            $.ajax({
                method: 'POST',
                url: 'database/delete-user.php',
                data: { user_id: userId },
                dataType: 'json',

                success: function(response){

                    if(response.success){

                        button.closest('tr').fadeOut(300, function(){
                            $(this).remove();
                            reOrderTable(); // 🔥 fixes numbering
                        });

                        let count = $('.users tbody tr').length - 1;
                        $('.userCount').text(count + " Users");

                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: fullName + ' has been removed.',
                            confirmButtonColor: '#008cff',
                            timer: 2000,
                            showConfirmButton: false
                        });

                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                }
            });
        }
    });
});

// Edit logic 
$(document).on('click', '.editUser', function(e){
    e.preventDefault();

    let button = $(this);

    let userId = button.data('userid');
    let fname = button.data('fname');
    let lname = button.data('lname');
    let email = button.data('email');

    Swal.fire({
        title: 'Edit User',
        html:
            `<input id="swal_fname" class="swal2-input" placeholder="First Name" value="${fname}">
             <input id="swal_lname" class="swal2-input" placeholder="Last Name" value="${lname}">
             <input id="swal_email" class="swal2-input" placeholder="Email" value="${email}">`,
        confirmButtonText: 'Update',
        confirmButtonColor: '#008cff',
        focusConfirm: false,
        preConfirm: () => {
            return {
                first_name: document.getElementById('swal_fname').value,
                last_name: document.getElementById('swal_lname').value,
                email: document.getElementById('swal_email').value
            }
        }
    }).then((result) => {

        if(result.isConfirmed){

            $.ajax({
                method: 'POST',
                url: 'database/update-user.php',
                data: {
                    user_id: userId,
                    first_name: result.value.first_name,
                    last_name: result.value.last_name,
                    email: result.value.email
                },
                dataType: 'json',

                success: function(response){
                    if(response.success){

                        let row = button.closest('tr');

                        row.find('.fname').text(result.value.first_name);
                        row.find('.lname').text(result.value.last_name);
                        row.find('.email').text(result.value.email);

                        // IMPORTANT: update data attributes
                        button.attr('data-fname', result.value.first_name);
                        button.attr('data-lname', result.value.last_name);
                        button.attr('data-email', result.value.email);

                        Swal.fire({
                            icon: 'success',
                            title: 'Updated!',
                            text: 'User updated successfully.',
                            confirmButtonColor: '#008cff',
                            timer: 2000,
                            showConfirmButton: false
                        });

                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                }
            });
        }
    });
});

// Sidebar submenu toggle functionality
document.addEventListener("DOMContentLoaded", function () {
    
    // Handle clicks on the menu link (including arrow)
    const menuLinks = document.querySelectorAll('.has-submenu > a');
    
    menuLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const parentLi = this.closest('.liMenu');
            if (!parentLi) return;
            
            // Close other open menus
            document.querySelectorAll('.liMenu.open').forEach(item => {
                if (item !== parentLi) {
                    item.classList.remove('open');
                }
            });
            
            // Toggle current menu
            parentLi.classList.toggle('open');
        });
    });
    
});
