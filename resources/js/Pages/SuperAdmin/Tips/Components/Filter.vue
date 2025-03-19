<template>
  <div class="superadmin-tips__filter">
    <div class="superadmin-tips__step superadmin-tips__step_prev"></div>
    <div class="superadmin-tips__date">{{ date }}</div>
    <div class="superadmin-tips__step superadmin-tips__step_next"></div>
  </div>
</template>

<script>
export default {
  props: {
    date: {
      type: String,
      required: true
    }
  }
}
</script>
