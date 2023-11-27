<template>
  <div class="profile__edit-modal">
    <!-- Modal -->
    <div
      class="modal fade"
      id="profile_edit_modal"
      tabindex="-1"
      aria-labelledby="profile_edit_modal"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="profile__edit-wrapper">
            <div class="profile__edit-close">
              <button
                type="button"
                class="profile__edit-close-btn"
                data-bs-dismiss="modal"
              >
                <i class="fa fa-times"></i>
              </button>
            </div>
            <!-- form start -->
            <profile-edit-form />
            <!-- form end -->
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import ProfileEditForm from "@/components/forms/ProfileEditForm.vue";

export default defineComponent({
  components: { ProfileEditForm },
  setup() {
    return {};
  },
});
</script>
