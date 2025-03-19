<template>
  <div class="custom-select" ref="customSelect">
    <div class="custom-select__control" @click="toggleDropdown">
      <span v-if="!modelValue || !modelValue.title" class="custom-select__text">Выберите статус</span>
      <span v-else class="custom-select__text" :style="{ color: modelValue.color }">{{ modelValue.title }}</span>
      <span v-if="canChange" class="custom-select__arrow"></span>
    </div>
    <!-- Выпадающий список -->
    <ul v-if="isDropdownOpen && canChange" class="custom-select__dropdown">
      <li
        v-for="option in filteredOptions"
        :key="option.value"
        class="custom-select__option"
        @click="selectOption(option)"
      >
        {{ option.title }}
      </li>
    </ul>
  </div>
</template>

<script>
export default {
  name: 'CustomSelect',
  props: {
    options: {
      type: Array,
      required: true
    },
    canChange: {
      type: Boolean,
      required: true
    },
    modelValue: {
      type: Object,
      required: false,
      default: () => ({ value: '', title: '' })
    },
  },
  data() {
    return {
      searchText: this.modelValue.title || '',
      isDropdownOpen: false,
      filteredOptions: this.options
    };
  },
  mounted() {
    this.filteredOptions = this.options;
    document.addEventListener('click', this.handleOutsideClick);
  },
  beforeDestroy() {
    document.removeEventListener('click', this.handleOutsideClick);
  },
  methods: {
    toggleDropdown() {
      this.isDropdownOpen = !this.isDropdownOpen;
      if (!this.isDropdownOpen) {
        this.searchText = '';
      }
    },
    filterOptions() {
      this.filteredOptions = this.options.filter(option =>
        option.title.toLowerCase().includes(this.searchText.toLowerCase())
      );
    },
    selectOption(option) {
      this.$emit('update:modelValue', option);
      this.isDropdownOpen = false;
    },
    handleOutsideClick(event) {
      if (this.$refs.customSelect && !this.$refs.customSelect.contains(event.target)) {
        this.isDropdownOpen = false;
      }
    }
  },
  watch: {
    modelValue(newValue) {
      this.searchText = newValue.title;
    }
  },
};
</script>

<style scoped>
.custom-select {
  position: relative;
}
.custom-select__control {
  display: flex;
  justify-content: flex-end;
  align-items: center;
  padding: 5px;
  width: 100%;
}
.custom-select__text {
  font-size: 14px;
  color: #000000;
  font-weight: 600;
  padding-left: 10px;
  flex-grow: 1;
  text-align: right;
  cursor: pointer;
  padding-right: 10px;
}
.custom-select__arrow {
  width: 0;
  height: 0;
  border-left: 5px solid transparent;
  border-right: 5px solid transparent;
  border-top: 5px solid #333;
  cursor: pointer;
}
.custom-select__dropdown {
  position: absolute;
  width: 170px;
  top: 100%;
  left: 0;
  right: 0;
  background-color: #fff;
  border: 1px solid #ccc;
  border-radius: 4px;
  max-height: 200px;
  overflow-y: auto;
  box-shadow: 0px 3px 6px rgba(0, 0, 0, 0.1);
  z-index: 1000;
}
.custom-select__option {
  padding: 8px 10px;
  cursor: pointer;
}
.custom-select__option:hover {
  background-color: rgba(56, 176, 176, 0.3);
}
</style>
