<template>
  <div class="form">
    <div class="form__item">
      <v-select :options="options" label="title" :searchable="false" :clearable="false"></v-select>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    options: {
      type: Array,
      required: true,
    }
  }
}
</script>
