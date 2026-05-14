jQuery(document).ready(function($) {
    let currentUserId = null;
    let currentApplicationId = null;

    $('.pupbus-pets-copy-btn').on('click', function() {
        const $btn = $(this);
        const code = $btn.data('code');
        
        navigator.clipboard.writeText(code).then(function() {
            const originalText = $btn.text();
            $btn.text('Copied!').addClass('copied');
            
            setTimeout(function() {
                $btn.text(originalText).removeClass('copied');
            }, 2000);
        }).catch(function() {
            const $temp = $('<input>');
            $('body').append($temp);
            $temp.val(code).select();
            document.execCommand('copy');
            $temp.remove();
            
            const originalText = $btn.text();
            $btn.text('Copied!').addClass('copied');
            
            setTimeout(function() {
                $btn.text(originalText).removeClass('copied');
            }, 2000);
        });
    });

    function openModal(modalId) {
        $('#pupbus-pets-modal-overlay').addClass('active');
        $('#' + modalId).addClass('active');
        $('body').css('overflow', 'hidden');
    }

    $(document).on('click', '.pupbus-pets-btn-view[data-application-id]', function() {
        const applicationId = $(this).data('application-id');
        currentApplicationId = applicationId;

        $.ajax({
            url: pupbusPetsAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'pupbus_pets_get_application_details',
                nonce: pupbusPetsAdmin.nonce,
                application_id: applicationId
            },
            success: function(response) {
                if (response.success) {
                    const app = response.data;
                    let html = '<div class="pupbus-pets-user-details-grid">';
                    
                    html += '<div class="pupbus-pets-detail-row">';
                    html += '<span class="pupbus-pets-detail-label">Application ID</span>';
                    html += '<span class="pupbus-pets-detail-value">#' + app.id + '</span>';
                    html += '</div>';

                    html += '<div class="pupbus-pets-detail-row">';
                    html += '<span class="pupbus-pets-detail-label">Parent Name</span>';
                    html += '<span class="pupbus-pets-detail-value">' + escapeHtml(app.parent_name) + '</span>';
                    html += '</div>';

                    html += '<div class="pupbus-pets-detail-row">';
                    html += '<span class="pupbus-pets-detail-label">Email</span>';
                    html += '<span class="pupbus-pets-detail-value">' + escapeHtml(app.parent_email) + '</span>';
                    html += '</div>';

                    html += '<div class="pupbus-pets-detail-row">';
                    html += '<span class="pupbus-pets-detail-label">Phone</span>';
                    html += '<span class="pupbus-pets-detail-value">' + escapeHtml(app.parent_phone) + '</span>';
                    html += '</div>';

                    html += '<div class="pupbus-pets-detail-row">';
                    html += '<span class="pupbus-pets-detail-label">Neighborhood</span>';
                    html += '<span class="pupbus-pets-detail-value">' + escapeHtml(app.neighborhood) + '</span>';
                    html += '</div>';

                    html += '<div class="pupbus-pets-detail-row">';
                    html += '<span class="pupbus-pets-detail-label">Dog Name</span>';
                    html += '<span class="pupbus-pets-detail-value">' + escapeHtml(app.dog_name) + '</span>';
                    html += '</div>';

                    html += '<div class="pupbus-pets-detail-row">';
                    html += '<span class="pupbus-pets-detail-label">Dog Breed</span>';
                    html += '<span class="pupbus-pets-detail-value">' + escapeHtml(app.dog_breed) + '</span>';
                    html += '</div>';

                    html += '<div class="pupbus-pets-detail-row">';
                    html += '<span class="pupbus-pets-detail-label">Dog Age</span>';
                    html += '<span class="pupbus-pets-detail-value">' + escapeHtml(app.dog_age) + '</span>';
                    html += '</div>';

                    html += '<div class="pupbus-pets-detail-row">';
                    html += '<span class="pupbus-pets-detail-label">Session Preference</span>';
                    html += '<span class="pupbus-pets-detail-value">' + escapeHtml(ucFirst(app.session_preference)) + '</span>';
                    html += '</div>';

                    if (app.pup_notes) {
                        html += '<div class="pupbus-pets-detail-row pupbus-pets-detail-row-full">';
                        html += '<span class="pupbus-pets-detail-label">Notes</span>';
                        html += '<span class="pupbus-pets-detail-value">' + escapeHtml(app.pup_notes) + '</span>';
                        html += '</div>';
                    }

                    html += '<div class="pupbus-pets-detail-row">';
                    html += '<span class="pupbus-pets-detail-label">Submitted At</span>';
                    html += '<span class="pupbus-pets-detail-value">' + escapeHtml(app.submitted_at) + '</span>';
                    html += '</div>';

                    html += '</div>';

                    $('#pupbus-pets-view-modal-content').html(html);
                    openModal('pupbus-pets-view-modal');
                } else {
                    alert('Error: ' + response.data);
                }
            },
            error: function() {
                alert('An error occurred while fetching application details.');
            }
        });
    });

    $(document).on('click', '.pupbus-pets-btn-delete[data-application-id]', function() {
        const applicationId = $(this).data('application-id');
        const applicationName = $(this).data('application-name');
        currentApplicationId = applicationId;
        
        $('#pupbus-pets-delete-application-name').text(applicationName);
        openModal('pupbus-pets-delete-modal');
    });

    $(document).on('click', '.pupbus-pets-btn-confirm-delete', function() {
        if (currentApplicationId) {
            const $btn = $(this);
            $btn.prop('disabled', true).text('Deleting...');

            $.ajax({
                url: pupbusPetsAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'pupbus_pets_delete_application',
                    nonce: pupbusPetsAdmin.nonce,
                    application_id: currentApplicationId
                },
                success: function(response) {
                    if (response.success) {
                        closeModals();
                        $('tr[data-application-id="' + currentApplicationId + '"]').fadeOut(300, function() {
                            $(this).remove();
                        });
                    } else {
                        alert('Error: ' + response.data);
                        $btn.prop('disabled', false).text('Delete Application');
                    }
                },
                error: function() {
                    alert('An error occurred while deleting the application.');
                    $btn.prop('disabled', false).text('Delete Application');
                }
            });
        } else if (currentUserId) {
            const $btn = $(this);
            $btn.prop('disabled', true).text('Deleting...');

            $.ajax({
                url: pupbusPetsAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'pupbus_pets_delete_user',
                    nonce: pupbusPetsAdmin.nonce,
                    user_id: currentUserId
                },
                success: function(response) {
                    if (response.success) {
                        closeModals();
                        $('tr[data-user-id="' + currentUserId + '"]').fadeOut(300, function() {
                            $(this).remove();
                        });
                    } else {
                        alert('Error: ' + response.data);
                        $btn.prop('disabled', false).text('Delete User');
                    }
                },
                error: function() {
                    alert('An error occurred while deleting the user.');
                    $btn.prop('disabled', false).text('Delete User');
                }
            });
        }
    });

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function ucFirst(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    function closeModals() {
        $('#pupbus-pets-modal-overlay').removeClass('active');
        $('.pupbus-pets-modal').removeClass('active');
        $('body').css('overflow', '');
        $('#pupbus-pets-view-modal-content').html('');
        currentUserId = null;
    }

    $('.pupbus-pets-btn-view').on('click', function() {
        const userId = $(this).data('user-id');
        currentUserId = userId;

        $.ajax({
            url: pupbusPetsAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'pupbus_pets_get_user_details',
                nonce: pupbusPetsAdmin.nonce,
                user_id: userId
            },
            success: function(response) {
                if (response.success) {
                    const user = response.data;
                    let html = '<div class="pupbus-pets-user-details-grid">';
                    
                    html += '<div class="pupbus-pets-detail-row">';
                    html += '<span class="pupbus-pets-detail-label">User ID</span>';
                    html += '<span class="pupbus-pets-detail-value">#' + user.id + '</span>';
                    html += '</div>';

                    html += '<div class="pupbus-pets-detail-row">';
                    html += '<span class="pupbus-pets-detail-label">Full Name</span>';
                    html += '<span class="pupbus-pets-detail-value">' + escapeHtml(user.name) + '</span>';
                    html += '</div>';

                    html += '<div class="pupbus-pets-detail-row">';
                    html += '<span class="pupbus-pets-detail-label">Email</span>';
                    html += '<span class="pupbus-pets-detail-value">' + escapeHtml(user.email) + '</span>';
                    html += '</div>';

                    html += '<div class="pupbus-pets-detail-row">';
                    html += '<span class="pupbus-pets-detail-label">Phone</span>';
                    html += '<span class="pupbus-pets-detail-value">' + escapeHtml(user.phone) + '</span>';
                    html += '</div>';

                    html += '<div class="pupbus-pets-detail-row">';
                    html += '<span class="pupbus-pets-detail-label">Neighborhood</span>';
                    html += '<span class="pupbus-pets-detail-value">' + escapeHtml(user.neighborhood) + '</span>';
                    html += '</div>';

                    html += '<div class="pupbus-pets-detail-row">';
                    html += '<span class="pupbus-pets-detail-label">Home Address</span>';
                    html += '<span class="pupbus-pets-detail-value">' + escapeHtml(user.home_address) + '</span>';
                    html += '</div>';

                    html += '<div class="pupbus-pets-detail-row">';
                    html += '<span class="pupbus-pets-detail-label">Emergency Contact Name</span>';
                    html += '<span class="pupbus-pets-detail-value">' + escapeHtml(user.emergency_name) + '</span>';
                    html += '</div>';

                    html += '<div class="pupbus-pets-detail-row">';
                    html += '<span class="pupbus-pets-detail-label">Emergency Contact Phone</span>';
                    html += '<span class="pupbus-pets-detail-value">' + escapeHtml(user.emergency_phone) + '</span>';
                    html += '</div>';

                    html += '<div class="pupbus-pets-detail-row">';
                    html += '<span class="pupbus-pets-detail-label">Registered Date</span>';
                    html += '<span class="pupbus-pets-detail-value">' + escapeHtml(user.registered) + '</span>';
                    html += '</div>';

                    html += '</div>';

                    if (user.pets && user.pets.length > 0) {
                        html += '<div class="pupbus-pets-pets-section">';
                        html += '<h3>Pets (' + user.pets.length + ')</h3>';
                        
                        user.pets.forEach(function(pet, index) {
                            html += '<div class="pupbus-pets-pet-card">';
                            html += '<div class="pupbus-pets-pet-header">';
                            html += '<h4 class="pupbus-pets-pet-name">Pet ' + (index + 1) + (pet.pet_name ? ': ' + escapeHtml(pet.pet_name) : '') + '</h4>';
                            html += '</div>';
                            html += '<div class="pupbus-pets-pet-grid">';
                            
                            for (let key in pet) {
                                if (key !== 'id' && pet[key]) {
                                    const label = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                                    html += '<div class="pupbus-pets-detail-row">';
                                    html += '<span class="pupbus-pets-detail-label">' + label + '</span>';
                                    html += '<span class="pupbus-pets-detail-value">' + escapeHtml(pet[key]) + '</span>';
                                    html += '</div>';
                                }
                            }
                            
                            html += '</div>';
                            html += '</div>';
                        });
                        
                        html += '</div>';
                    }

                    $('#pupbus-pets-view-modal-content').html(html);
                    openModal('pupbus-pets-view-modal');
                } else {
                    alert('Error: ' + response.data);
                }
            },
            error: function() {
                alert('An error occurred while fetching user details.');
            }
        });
    });

    $('.pupbus-pets-btn-delete').on('click', function() {
        const userId = $(this).data('user-id');
        const userName = $(this).data('user-name');
        currentUserId = userId;
        
        $('#pupbus-pets-delete-user-name').text(userName);
        openModal('pupbus-pets-delete-modal');
    });

    $('.pupbus-pets-btn-cancel').on('click', closeModals);
    $('.pupbus-pets-modal-close').on('click', closeModals);
    $('#pupbus-pets-modal-overlay').on('click', closeModals);

    $('.pupbus-pets-btn-confirm-delete').on('click', function() {
        if (!currentUserId) return;

        const $btn = $(this);
        $btn.prop('disabled', true).text('Deleting...');

        $.ajax({
            url: pupbusPetsAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'pupbus_pets_delete_user',
                nonce: pupbusPetsAdmin.nonce,
                user_id: currentUserId
            },
            success: function(response) {
                if (response.success) {
                    closeModals();
                    $('tr[data-user-id="' + currentUserId + '"]').fadeOut(300, function() {
                        $(this).remove();
                    });
                } else {
                    alert('Error: ' + response.data);
                    $btn.prop('disabled', false).text('Delete User');
                }
            },
            error: function() {
                alert('An error occurred while deleting the user.');
                $btn.prop('disabled', false).text('Delete User');
            }
        });
    });

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
});
