<template>
  <div class="flex flex-col mb-6 mt-8 ml-8">

      <div class="flex items-center justify-between mb-4">

        <div class="relative w-full">
            <h4 class="font-bold">Доступные интерфейсы:</h4>
        </div>

      </div>

    <div v-for="(item, index) in items" :key="index" class="flex items-center mb-2">
      <input
        type="checkbox"
        :id="item.id"
        v-model="selectedItems"
        :value="item.value"
        class="mr-2" />
      <label :for="item.id" class="text-sm ml-2">{{ item.name }}</label>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      items: [
        { id: 'item1', value: 'value1', name: 'Элемент 1' },
        { id: 'item2', value: 'value2', name: 'Элемент 2' },
        { id: 'item3', value: 'value3', name: 'Элемент 3' },
        { id: 'item4', value: 'value4', name: 'Элемент 4' },
      ],
      selectedItems: [],
    };
  },
};
</script>

<style scoped>

.flex {
  display: flex;
}

.flex-col {
  flex-direction: column;
}

.items-center {
  align-items: center;
}

.mb-2 {
  margin-bottom: 8px;
}

.mr-2 {
  margin-right: 8px;
}

.text-sm {
  font-size: 14px;
}

input[type="checkbox"] {
  margin: 0;
  padding: 0;
  width: 20px;
  height: 20px;
  border-radius: 50%;
  border: 1px solid black;
  background-color: floralwhite;
  appearance: none;
  cursor: pointer;
  position: relative;
}

input[type="checkbox"]:checked {
  background-color: #38b0b0;
  border-color: #38b0b0;
}

input[type="checkbox"]:checked::after {
  content: '\2713';
  position: absolute;
  top: 2px;
  left: 4px;
  color: white;
  font-size: 14px;
  font-weight: bold;
}
</style>
