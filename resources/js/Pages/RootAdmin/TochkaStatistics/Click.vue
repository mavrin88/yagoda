<template>
  <div>
    <button @click="copyToClipboard">Скопировать текст</button>
    <p v-if="isCopied" class="success-message">Текст скопирован!</p>
  </div>
</template>

<script>
export default {
  data() {
    return {
      textToCopy: 'YAGODA', // Текст, который будет скопирован
      isCopied: false, // Флаг, указывающий, что текст скопирован
    };
  },
  methods: {
    async copyToClipboard() {
      if (!this.textToCopy) {
        alert('Введите текст для копирования');
        return;
      }

      try {
        // Используем API для копирования текста
        await navigator.clipboard.writeText(this.textToCopy);
        this.isCopied = true; // Показываем сообщение об успешном копировании
        setTimeout(() => {
          this.isCopied = false; // Скрываем сообщение через 2 секунды
        }, 2000);
      } catch (err) {
        console.error('Ошибка при копировании текста:', err);
        alert('Не удалось скопировать текст');
      }
    },
  },
};
</script>

<style>
.success-message {
  color: green;
  margin-top: 10px;
}
</style>