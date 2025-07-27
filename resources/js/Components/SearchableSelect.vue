<!-- SearchableSelect.vue -->
<template>
    <div class="searchable-select-container">
      <div
        class="select-field"
        :class="{ 'active': isOpen }"
        @click="toggleDropdown"
      >
        <input
          type="text"
          v-model="searchQuery"
          @click.stop
          @input="filterOptions"
          @focus="isOpen = true"
          :placeholder="placeholder"
          
          class="focus:shadow-primary-outline bg-violet-950 text-white text-sm leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-violet-700 bg-clip-padding px-3 py-2 font-normal outline-none transition-all placeholder:text-violet-300 focus:border-blue-400 focus:outline-none pr-10"
/>
        
        <div class="select-arrow">
            <svg
  class="text-gray-600 dark:text-slate-200"
  width="16"
  height="16"
  viewBox="0 0 24 24"
  fill="none"
  xmlns="http://www.w3.org/2000/svg"
  :class="{ 'rotate': isOpen }"
  style="color: inherit;"
>
  <path
    d="M6 9L12 15L18 9"
    stroke="currentColor"
    stroke-width="2"
    stroke-linecap="round"
    stroke-linejoin="round"
  />
</svg>

        </div>
      </div>
      
      <!-- Dropdown menu -->
      <div
  v-if="isOpen"
  class="select-dropdown dark:bg-slate-900 dark:text-white"
>

        <div
          v-if="filteredOptions.length === 0"
          class="no-results"
        >
          No se encontraron resultados
        </div>
        <div
          v-for="option in filteredOptions"
          :key="option.value"
          @click="selectOption(option)"
          class="dropdown-option"
          :class="{ 'selected': selectedItem === option.value }"
        >
          {{ option.label }}
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
  
  const props = defineProps({
    modelValue: {
      type: [String, Number],
      default: ''
    },
    options: {
      type: Array,
      required: true
    },
    placeholder: {
      type: String,
      default: 'Seleccione una opción'
    },
    fieldType: {
      type: String,
      default: 'default' // Puede ser: 'distrito', 'codigoSie', 'institucion', 'default'
    }
  });
  
  const emit = defineEmits(['update:modelValue']);
  
  const isOpen = ref(false);
  const searchQuery = ref('');
  const selectedItem = ref(props.modelValue);
  const sortedOptions = computed(() => {
  if (!Array.isArray(props.options)) return [];
  return [...props.options].sort((a, b) => {
    const labelA = a?.label?.toString() || '';
    const labelB = b?.label?.toString() || '';
    return labelA.localeCompare(labelB);
  });
});

  
const filteredOptions = computed(() => {
  if (!searchQuery.value) return sortedOptions.value;
  return sortedOptions.value.filter(option => {
    const label = option?.label || '';
    return label.toLowerCase().includes(searchQuery.value.toLowerCase());
  });
});

  
  // Update the display value when modelValue changes
  watch(() => props.modelValue, () => {
    if (!props.modelValue) {
      searchQuery.value = '';
      selectedItem.value = null;
      return;
    }
    
    selectedItem.value = props.modelValue;
    const selectedOption = props.options.find(option => option.value == props.modelValue);
    if (selectedOption) {
      searchQuery.value = selectedOption.label;
    }
  }, { immediate: true });
  
  const toggleDropdown = () => {
    isOpen.value = !isOpen.value;
  };
  
  const selectOption = (option) => {
    selectedItem.value = option.value;
    emit('update:modelValue', option.value);
    searchQuery.value = option.label;
    isOpen.value = false;
  };
  
  const filterOptions = () => {
    isOpen.value = true;
  };
  
  // Close dropdown when clicking outside
  const closeOnClickOutside = (e) => {
    if (!e.target.closest('.searchable-select-container')) {
      isOpen.value = false;
    }
  };
  
  onMounted(() => {
    document.addEventListener('click', closeOnClickOutside);
  });
  
  onUnmounted(() => {
    document.removeEventListener('click', closeOnClickOutside);
  });
  </script>
  
  <style scoped>
.searchable-select-container {
  position: relative;
  width: 100%;
}

.select-field {
  display: flex;
  align-items: center;
  justify-content: space-between;
  border: 1px solid #7c3aed; /* border-violet-700 */
  border-radius: 0.5rem;
  background-color: #2e1065;
  transition: all 0.2s ease;
  padding-right: 0.75rem; 
}
:deep(html.dark) {
  --bg-select-field: #0f172a; /* slate-900 */
}


.select-field.active {
  border-color: #3b82f6;
  box-shadow: 0 0 0 1px rgba(59, 130, 246, 0.5);
}

.select-field input {
  width: 100%;
  border: none;
  outline: none;
  padding: 0.5rem 0.75rem;
  font-size: 0.875rem;
  border-radius: 0.5rem;
}


.select-field input::placeholder {
  color: #9ca3af;
}

.select-arrow {
  height: 100%;
  padding: 0 0.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  border-left: 1px solid #d1d5db;
  background-color: inherit;
  border-top-right-radius: 0.5rem;
  border-bottom-right-radius: 0.5rem;
}


.select-arrow svg {
  transition: transform 0.2s ease;
}

.select-arrow svg.rotate {
  transform: rotate(180deg);
}

.select-dropdown {
  position: absolute;
  z-index: 50;
  top: calc(100% + 0.25rem);
  left: 0;
  right: 0;
  border: 1px solid #d1d5db;
  border-radius: 0.5rem;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  max-height: 15rem;
  overflow-y: auto;
  overflow-x: hidden;
  width: 100%;
}

.dropdown-option {
  padding: 0.6rem 1rem;
  font-size: 0.875rem;
  color: #374151;
  cursor: pointer;
  transition: background-color 0.15s ease;
}

.dropdown-option:hover {
  background-color: #f3f4f6;
}

.dropdown-option.selected {
  background-color: #e0f2fe;
}

.no-results {
  padding: 0.75rem 1rem;
  color: #9ca3af;
  font-size: 0.875rem;
  text-align: center;
}

.select-dropdown::-webkit-scrollbar {
  width: 6px;
}
.select-dropdown::-webkit-scrollbar-track {
  background: transparent;
}
.select-dropdown::-webkit-scrollbar-thumb {
  background: #cbd5e0;
  border-radius: 3px;
}
.select-dropdown {
  background-color: #2e1065; /* violet-950 */
  border-color: #7c3aed;     /* violet-700 */
  color: #f1f5f9;            /* blanco */
}

.dropdown-option {
  color: #f1f5f9;
  background-color: #2e1065;
}

.dropdown-option:hover {
  background-color: #3b0764; /* más oscuro al hacer hover */
}

.dropdown-option.selected {
  background-color: #7c3aed; /* violeta seleccionado */
  color: white;
}


/* 🌙 DARK MODE con :deep() */
:deep(html.dark) .select-field {
  background-color: #0f172a; /* slate-900 */
  border-color: #334155;
}

:deep(html.dark) .select-field input {
  color: #f1f5f9; /* texto blanco */
}
:deep(html.dark) .select-field input::placeholder {
  color: #64748b; /* slate-400 */
}
:deep(html.dark) .select-arrow {
  background-color: #0f172a;
  border-left: 1px solid #334155;
  color: #cbd5e1;
}

:deep(html.dark) .select-dropdown {
  background-color: #0f172a;
  border-color: #334155;
  color: #f1f5f9;
}
:deep(html.dark) .dropdown-option {
  color: #f1f5f9;
}
:deep(html.dark) .dropdown-option:hover {
  background-color: #1e293b; /* slate-800 */
}
:deep(html.dark) .dropdown-option.selected {
  background-color: #3b82f6;
  color: white;
}
:deep(html.dark) .no-results {
  color: #94a3b8;
}
</style>
