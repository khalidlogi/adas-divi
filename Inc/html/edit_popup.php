<?php
echo '<div id="edit-popup" class="edit-popup draggable" style="display: none;">
    <div class="popup-content">
        <button class="dismiss-btn"><i class="fas fa-times"></i></button>
        <h1 class="myh1">Edit values</h1>
        <form id="edit-form" class="edit-form input-row">
        // Generate nonce in form
            <button id="submit-button">Submit</button>
            <div id="result"></div>
            <!-- Form fields go here -->
        </form>
        <button type="submit" data-nonceupdate="'.wp_create_nonce('nonceupdate').'" data-form-id="'.esc_attr($this->formbyid).'"  class="update-btn">Save</button>
    </div>
</div>';